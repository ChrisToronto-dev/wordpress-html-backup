<?php

namespace App\Services;

use App\Events\BackupProgress;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BackupService
{
    protected Client $http;
    protected array $visited = [];
    protected string $tmpPath;
    protected int $maxDepth;

    public function __construct()
    {
        $this->http = new Client([
            'timeout' => 15,
            'verify' => false, // For local dev with self-signed certs
            'headers' => [
                'User-Agent' => 'WordPress HTML Backup Tool'
            ]
        ]);
        $this->tmpPath = config('backup.tmp_path');
        $this->maxDepth = config('backup.max_depth');
        
        if (!is_dir($this->tmpPath)) {
            mkdir($this->tmpPath, 0755, true);
        }
    }

    public function run(string $url, string $jobId): void
    {
        $this->visited = [];
        $base = rtrim($url, '/');
        
        $this->broadcastProgress($jobId, 0, "Starting backup for {$base}");
        
        $this->crawl($base, $base, 0, $jobId);
        
        $zipPath = "{$this->tmpPath}/{$jobId}.zip";
        $this->packageZip("{$this->tmpPath}/{$jobId}_files", $zipPath, $jobId);
        
        // Clean up the temporary files
        $this->deleteDirectory("{$this->tmpPath}/{$jobId}_files");
        
        $this->broadcastProgress($jobId, count($this->visited), "completed");
    }

    protected function crawl(string $baseUrl, string $currentUrl, int $depth, string $jobId): void
    {
        if ($depth > $this->maxDepth || isset($this->visited[$currentUrl])) {
            return;
        }
        
        $this->visited[$currentUrl] = true;
        
        try {
            $response = $this->http->get($currentUrl);
            $html = (string) $response->getBody();
            
            $filename = $this->urlToPath($currentUrl);
            if (empty(pathinfo($filename, PATHINFO_EXTENSION))) {
                // Determine if it should be an index.html in a directory
                $filename = rtrim($filename, '/') . '/index.html';
            }

            $fullPath = "{$this->tmpPath}/{$jobId}_files/{$filename}";
            $this->ensureDirectoryExists($fullPath);
            
            $this->rewriteResources($html, $baseUrl, $jobId);
            file_put_contents($fullPath, $html);
            
            $this->broadcastProgress($jobId, count($this->visited), "Saved page: {$filename}");
        } catch (\Exception $e) {
            $this->broadcastProgress($jobId, count($this->visited), "Failed to save {$currentUrl}: {$e->getMessage()}");
            return;
        }
        
        // Discover links
        $crawler = new Crawler($html, $currentUrl);
        $links = $crawler->filter('a[href]')->links();
        
        foreach ($links as $link) {
            $href = $link->getUri();
            // Remove hash fragments for comparison
            $href = explode('#', $href)[0];
            
            if (Str::startsWith($href, $baseUrl) && $href !== $currentUrl) {
                $this->crawl($baseUrl, $href, $depth + 1, $jobId);
            }
        }
    }

    protected function rewriteResources(string &$html, string $baseUrl, string $jobId): void
    {
        // Find links, scripts, and images that need to be downloaded and rewritten
        $crawler = new Crawler($html, $baseUrl);
        $replacements = [];

        // CSS and Favicon
        $crawler->filter('link[href]')->each(function (Crawler $node) use ($baseUrl, $jobId, &$replacements) {
            $href = $node->attr('href');
            if ($href && Str::startsWith($node->link()->getUri(), $baseUrl)) {
                $replacements[$href] = $this->processAsset($node->link()->getUri(), $baseUrl, $jobId);
            }
        });

        // JavaScript
        $crawler->filter('script[src]')->each(function (Crawler $node) use ($baseUrl, $jobId, &$replacements) {
            $src = $node->attr('src');
            if ($src && Str::startsWith($node->link()->getUri(), $baseUrl)) {
                $replacements[$src] = $this->processAsset($node->link()->getUri(), $baseUrl, $jobId);
            }
        });

        // Images
        $crawler->filter('img[src]')->each(function (Crawler $node) use ($baseUrl, $jobId, &$replacements) {
            $src = $node->attr('src');
            if ($src && Str::startsWith($node->link()->getUri(), $baseUrl)) {
                $replacements[$src] = $this->processAsset($node->link()->getUri(), $baseUrl, $jobId);
            }
        });

        // Replace all found URLs in the HTML
        foreach ($replacements as $original => $relative) {
            if ($relative) {
                // Create a relative path from the current page to the asset.
                // For simplicity here we just use absolute root path like /assets/..
                // but since it needs to be opened locally, we just use the relative URL
                $html = str_replace('="' . $original . '"', '="/' . ltrim($relative, '/') . '"', $html);
                $html = str_replace("='" . $original . "'", "='/" . ltrim($relative, '/') . "'", $html);
            }
        }
        
        // Also rewrite internal A tags to relative HTML paths
        $crawler->filter('a[href]')->each(function (Crawler $node) use ($baseUrl, &$html) {
            $href = $node->attr('href');
            if ($href && Str::startsWith($node->link()->getUri(), $baseUrl)) {
                $uri = $node->link()->getUri();
                $path = $this->urlToPath($uri);
                $path = rtrim($path, '/') . '/index.html';
                
                $html = str_replace('href="' . $href . '"', 'href="/' . ltrim($path, '/') . '"', $html);
            }
        });
    }

    protected function processAsset(string $url, string $baseUrl, string $jobId): ?string
    {
        $relativePath = parse_url($url, PHP_URL_PATH);
        if (!$relativePath) {
            return null;
        }

        // Clean up any query parameters from path for the local file
        $relativePath = ltrim($relativePath, '/');
        $fullPath = "{$this->tmpPath}/{$jobId}_files/{$relativePath}";

        // If not already downloaded, download it
        if (!file_exists($fullPath)) {
            $this->downloadAsset($url, $fullPath);
        }

        return $relativePath;
    }

    protected function downloadAsset(string $url, string $dest): void
    {
        try {
            $this->ensureDirectoryExists($dest);
            $resp = $this->http->get($url);
            $content = $resp->getBody()->getContents();
            file_put_contents($dest, $content);
        } catch (\Exception $e) {
            // Silently ignore asset download failures for now
        }
    }

    protected function urlToPath(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH) ?? '';
        $path = rtrim($path, '/');
        return ltrim($path, '/');
    }

    protected function ensureDirectoryExists(string $path): void
    {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    protected function packageZip(string $sourceDir, string $zipPath, string $jobId): void
    {
        if (!is_dir($sourceDir)) {
            return;
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($sourceDir, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );
            
            foreach ($files as $file) {
                $realPath = $file->getRealPath();
                $relative = substr($realPath, strlen($sourceDir) + 1);

                if ($file->isDir()) {
                    $zip->addEmptyDir($relative);
                } else if ($file->isFile()) {
                    $zip->addFile($realPath, $relative);
                }
            }
            $zip->close();
            $this->broadcastProgress($jobId, count($this->visited), "Zip package created successfully");
        }
    }

    protected function deleteDirectory(string $dir): void
    {
        if (!file_exists($dir)) {
            return;
        }
        
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->deleteDirectory("$dir/$file") : unlink("$dir/$file");
        }
        rmdir($dir);
    }

    protected function broadcastProgress(string $jobId, int $completed, string $message): void
    {
        $payload = [
            'job_id' => $jobId,
            'completed' => $completed,
            'total' => count($this->visited),
            'message' => $message,
            'status' => $message === 'completed' ? 'completed' : 'processing'
        ];
        
        event(new BackupProgress($payload));
        
        // Save state to JSON file as fallback for polling
        $jsonPath = "{$this->tmpPath}/{$jobId}.json";
        file_put_contents($jsonPath, json_encode($payload));
    }
}
