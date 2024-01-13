<?php

namespace App\Http\Controllers;

use App\Http\Requests\Party\CreatePartyRequest;
use App\Http\Resources\PartyCollection;
use App\Http\Resources\PartyResource;
use App\Models\Party;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PartyController extends Controller
{
    //

    public function index()
    {
        $parties = Party::where('status', 'live')->orderBy('id', 'DESC')->paginate();
        $response = new PartyCollection($parties);
        return $response;
    }

    public function create(CreatePartyRequest $request)
    {
        if(!(Party::where(['user_id' => $request->user()->id, 'video_id' => $request->video_id, 'status' => 'live'])->exists())){
            Party::create([
                'user_id' => $request->user()->id,
                'video_id' => $request->video_id,
                'name' => $request->name,
                'start_time' => Carbon::now()->toDateTimeString(),
                'current_video_time' => $request->current_video_time,
            ]);
            return response()->json(["message" => "Party Created!"], Response::HTTP_ACCEPTED);
        }
        return response()->json(["message" => "You already have a party for this video"], Response::HTTP_FORBIDDEN);
    }

    public function joinParty($id)
    {
        //return party data (Video URL + video play time) so the video can be displayed from Frontend at the correct moment 
        $party = Party::where('id', $id)->first();
        $response = new PartyResource($party);
        return $response;
    }

}
