<?php

return [
    'max_depth' => env('BACKUP_MAX_DEPTH', 3),
    'tmp_path' => storage_path('app/tmp'),
    'zip_ttl_minutes' => env('ZIP_TTL_MINUTES', 1440), // 24 hours
];
