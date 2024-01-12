<?php

namespace App\Events;

use App\Models\Party;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserJoinedParty implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */

    public $party;
    public $user;
    public function __construct(Party $party, User $user)
    {
        //
        $this->party = $party;
        $this->user = $user;
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
            'new_user' => $this->user->name
        ];
    }

    public function broadcastAs()
    {
        return 'user-joined-party';
    }
}
