<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Party;

class PartyVideoPlayTimeChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $party;

    public $current_time;
    /**
     * Create a new event instance.
     */
    public function __construct(Party $party, $current_time)
    {
        //
        $this->party = $party;
        $this->current_time = $current_time;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("party." . $this->party->id),
        ];
    }

    public function boradcastWith()
    {
        return [
            'current_time' => $this->current_time
        ];
    }

    public function broadcastAs()
    {
        return 'video-stream-updated';
    }
}
