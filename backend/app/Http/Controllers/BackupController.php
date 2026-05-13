<?php

namespace App\Http\Controllers;

use App\Http\Requests\BackupRequest;
use App\Jobs\RunBackupJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupController extends Controller
{
    /**
     * Start a new backup job.
     */
    public function start(BackupRequest $request): JsonResponse
    {
        $url = $request->validated('url');
        
        // Create the job and dispatch it
        $job = new RunBackupJob($url);
        dispatch($job);
        
        return response()->json([
            'message' => 'Backup job started successfully',
            'job_id' => $job->getJobId(),
        ], 202);
    }

    /**
     * Get the progress of a backup job (Fallback for WebSocket).
     */
    public function progress(string $jobId): JsonResponse
    {
        $path = config('backup.tmp_path') . "/{$jobId}.json";
        
        if (!file_exists($path)) {
            return response()->json(['status' => 'not_found'], 404);
        }
        
        $data = json_decode(file_get_contents($path), true);
        return response()->json($data);
    }

    /**
     * Download the completed backup ZIP file.
     */
    public function download(string $jobId): BinaryFileResponse|JsonResponse
    {
        $zipPath = config('backup.tmp_path') . "/{$jobId}.zip";
        
        if (!file_exists($zipPath)) {
            return response()->json(['error' => 'Backup ZIP not ready or not found'], 404);
        }
        
        return response()->download($zipPath, "backup-{$jobId}.zip")->deleteFileAfterSend(true);
    }
}
