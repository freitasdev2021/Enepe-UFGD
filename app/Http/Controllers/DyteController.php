<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DyteController extends Controller
{
    public static function createMeeting($data){
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . 'MTQyN2I4ZWItM2EzNS00MTViLWI0MzMtYTFmZDYzZDVhYWM2OmQ2MzliMzQ5ZmY0MGJlNzA4NzRm',
            'Content-Type' => 'application/json',
        ])->post('https://api.dyte.io/v2/meetings',$data);

        return $response->json();
    }

    public function abrirSala(){
        $meetingId = $_GET['meeting_id'];
        $participantName = $_GET['name'];

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . 'MTQyN2I4ZWItM2EzNS00MTViLWI0MzMtYTFmZDYzZDVhYWM2OmQ2MzliMzQ5ZmY0MGJlNzA4NzRm',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post('https://api.dyte.io/v2/meetings/' . $meetingId . '/participants', [
            'preset_name' => 'group_call_participant',
            'name' => $participantName,
            "custom_participant_id" => Auth::user()->email
        ]);

        return $response->json();
    }
}
