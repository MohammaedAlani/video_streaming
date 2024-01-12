<?php

namespace App\Http\Controllers;

use App\Events\PartyVideoPlayTimeChanged;
use App\Http\Requests\Stream\ChangePlayTimeRequest;
use App\Models\Party;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StreamingController extends Controller
{
    //
    public function video_playtime_changed(ChangePlayTimeRequest $request)
    {
        $party = Party::find($request->party_id);
        broadcast(new PartyVideoPlayTimeChanged($party, $request->current_time))->toOthers();
        return response()->json(["message" => "broadcasted_successfully"], Response::HTTP_OK);
    }   
}
