<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VideoStatusEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */

    public $status;
    public $current_time;

    public function __construct($status, $current_time = null)
    {
        $this->status = $status;
        $this->current_time = $current_time;
        
    }

    public function broadcastWith()
    {
        if(is_null($this->current_time)) {
            return ['status' => $this->status];
        }
        $user_id = 1;

        return [
            'status' => $this->status,
            'current_time' => $this->current_time,
            'user_id' => $user_id
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('video-status-channel'),
        ];
    }
}
