<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageSent;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function showPartyChat($party_id)
    {
        $messages = Message::where('party_id', $party_id)->orderBy('id', 'asc')->get();

        return response()->json($messages);
    }

    public function create(Request $request)
    {
        $user = auth('api')->user();

        $message = Message::create([
            'user_id' => $user->id,
            'party_id' => $request->party_id,
            'body' => $request->message,
        ]);

        broadcast(new ChatMessageSent($user, $message));

        return response()->json([
            'status' => true,
            'data' => []
        ]);
    }
}
