<?php

namespace App\Http\Controllers\Demo;

use App\Http\Controllers\Controller;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AudioController extends Controller
{
    public function uploadAudio(Request $request)
    {
        // Validate the request for an audio file
	    $request->validate([
	        'audio' => 'required|file|max:10240', // 10 MB max
	    ]);
	
	    // Get the uploaded file
	    $file = $request->file('audio');
	    
	    // Generate a unique filename
	    $filename = Str::random(10) . '.wav';
	    
	    // Define the destination path within the public directory
	    $destinationPath = public_path('audio_translate'); 
	
	    // Ensure the folder exists, if not create it
	    if (!file_exists($destinationPath)) {
	        mkdir($destinationPath, 0777, true);
	    }
	
	    // Move the uploaded file to the destination folder
	    $file->move($destinationPath, $filename);
	
	    // Generate the public URL
	    $url = asset('audio_translate/' . $filename);
	    
	    $audio_to_text = self::audio_to_text($url);
	
	    // Return the URL in the response
	    return response()->json(['url' => $url, 'audio_to_text' => $audio_to_text], 200);
    }
    
    public static function audio_to_text($url) {
	    $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://api.edenai.run/v2/audio/speech_to_text_async');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
		    "authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoiZjZhOWNiNTEtNjBlYy00ZjczLWE3NTktMDllMzFhZmQ4ZWI3IiwidHlwZSI6ImFwaV90b2tlbiJ9.ct5ZHDM2-iFDCCJbML0F4N5U5jFhPCvkDuqG6xXxzBc",
		    'content-type: application/json',
		]);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "{\n  \"providers\": \"deepgram\",\n  \"file_url\": \"".$url."?v=".time()."\",\n  \"speakers\": 1,\n \"profanity_filter\": false,\n  \"custom_vocabulary\": \"\"\n}");
		
		$response = curl_exec($ch);
		
		curl_close($ch);
		
		$result = json_decode($response);
		
		if(isset($result->results->deepgram->text)) {
			$text = $result->results->deepgram->text;
			
			return $text;
		} else {
			return $result;
		}
    }
}