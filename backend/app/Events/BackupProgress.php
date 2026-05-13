<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class BackupProgress implements ShouldBroadcastNow
{
    use InteractsWithSockets;

    public $payload;

    /**
     * Create a new event instance.
     *
     * @param array $payload Must contain 'job_id', 'completed', 'total', 'message'
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Broadcast on a private channel specific to the job.
        // We will skip authorization for this channel in routes/channels.php for simplicity,
        // or we could use a regular Channel if we want to make it completely public.
        // Let's use a public channel for now since there's no auth, but prefix it.
        return [
            new \Illuminate\Broadcasting\Channel('backup-progress.' . $this->payload['job_id'])
        ];
    }
}
