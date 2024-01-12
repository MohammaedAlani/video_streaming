<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageSent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $party;
    public $message_id;

    public function __construct($message_id, $party)
    {
        //
        $this->party = $party;
        $this->message_id = $message_id;
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

    public function broadcastWith()
    {
        $message = Message::where('id', $this->message_id)->with('user')->first()->toArray();
        return $message;
    }

    public function broadcastAs()
    {
        return 'message-sent';
    }

    
}
