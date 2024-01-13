<?php

namespace App\Http\Controllers;

use App\Events\MessageEvent;
use App\Events\PartyVideoPlayTimeChanged;
use App\Events\VideoStatusEvent;
use App\Http\Requests\Message\MessageRequest;
use App\Http\Requests\Stream\ChangePlayTimeRequest;
use App\Http\Requests\Stream\ChangeStatusRequest;
use App\Models\Message;
use App\Models\Party;


class StreamingController extends Controller
{
    //

    public function changeStatus(ChangeStatusRequest $request)
    {
        $party = Party::where('id', $request->party_id)->first();
        broadcast(new VideoStatusEvent($party, $request->action))->toOthers();
    }

    public function newMessage(MessageRequest $request)
    {
        $party = Party::where('id', $request->party_id)->first();
        $user_id = auth()->user()->id;
        $data = $request->all();
        $data['user_id'] = $user_id;
        $message = Message::create($data);     
        broadcast(new MessageEvent($party, $message->id))->toOthers();
    }

    public function changeVideoPlaytime(ChangePlayTimeRequest $request)
    {
        $party = Party::where('id', $request->party_id)->first();
        $party->update([
            'current_time' => $request->current_time
        ]);
        $party->save();
        broadcast(new PartyVideoPlayTimeChanged($party, $request->current_time))->toOthers();
    }   

    public function showMessages($id)
    {
        return Message::with('user')->where('party_id', $id)->get();
    }

}
