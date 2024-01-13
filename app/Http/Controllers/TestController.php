<?php

namespace App\Http\Controllers;

use App\Events\MessageEvent;
use App\Events\VideoStatusEvent;
use App\Models\Message;
use App\Models\Party;
use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TestController extends Controller
{

    public function index(Request $request)
    {
        $party = Party::where('id', 1)->first();
        broadcast(new VideoStatusEvent($request->status, $request->current_time));
        if($request->filled('status')) {
            // if($party->status !== $request->status) {
                if($request->status == 'play') {
                    $new_status = 'stop';
                } else if($request->status == 'stop') {
                    $new_status = 'play';
                }
                else if($request->status == 'seeking') {
                    $new_status = $party->status;
                }
                $party->update([
                    'status' => $new_status,
                    'current_time' => $request->current_time
                ]);
                $party->status = $new_status;
            // }
        } 
        return $party;
    }

    public function play(Request $request)
    {
        broadcast(new VideoStatusEvent('play', $request->current_time));
    }

    public function stop(Request $request)
    {
        broadcast(new VideoStatusEvent('stop', $request->current_time));
    }

    public function send_message(Request $request)
    {
        $user_id = auth()->user()->id;
        $data = $request->all();
        $data['user_id'] = $user_id;
        $message = Message::create($data);     
        broadcast(new MessageEvent($message->id));
    }

    public function show_messages($id)
    {
        return Message::with('user')->where('party_id', $id)->get();
    }

    public function videos()
    {
        return Video::orderBy('id', 'DESC')->get();
    }

    public function show_videos($id)
    {
        $user_id = 1;
        $video = Video::find($id);
        $party = Party::where('video_id', $id)->where('user_id', $user_id)->first();
        return [
            'video' => $video,
            'party' => $party
        ];
    }

    public function party(Request $request)
    {
        $video_id = $request->video_id;
        $video = Video::find($video_id);
        $user_id = 1;
        return Party::create([
            'video_id' => $video->id,
            'user_id' => $user_id,
            'name' => $video->title,
            'url' => $video->title,
            'start_time' => Carbon::now(),
        ]);        

    }
}
