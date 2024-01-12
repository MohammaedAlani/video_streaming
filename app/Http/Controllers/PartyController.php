<?php

namespace App\Http\Controllers;

use App\Http\Requests\Party\CreatePartyRequest;
use App\Models\Party;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PartyController extends Controller
{
    //
    public function create(CreatePartyRequest $request)
    {
        Party::create([
            'user_id' => $request->user_id,
            'video_id' => $request->video_id,
            'name' => $request->name,
            'start_time' => Carbon::now()->toDateTimeString(),
            'current_video_time' => $request->current_video_time,
        ]);
    }

}
