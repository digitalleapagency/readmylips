<?php

namespace App\Http\Controllers\Demo;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Twilio\TwiML\VoiceResponse;


class MeetingController extends Controller
{
    private $twilio;

    public function __construct()
    {
        $this->twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
    }

    public function joinMeeting()
    {
        // Parameters to customize
	    $callerNumber = env('TWILIO_PHONE_NUMBER');
	    $meetingNumber = request('meeting_number');
	    $meetingPin = request('meeting_pin'); // Add PIN from request
	
        try {
		    $res = $this->twilio->calls->create(
		        $callerNumber, // Dial this participant
		        $callerNumber,      // From this Twilio number
		        [
	                'url' => route('twilioWebhook', [
	                    'meeting_pin' => $meetingPin,
	                    'meeting_number' => $meetingNumber
	                ])
		        ]
		    );
	
	    return response()->json(['message' => 'Call initiated to join meeting with PIN.']);
        } catch (\Exception $e) {
            \Log::error('Error initiating Twilio call:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to initiate call.'], 500);
        }
    }
    
    public function twilioListen() {
	    $response = new VoiceResponse();

		// Add instructions to record the call
		$response->record([
			'timeout' => 300, //Timeout after 5 minutes of silence
		    'maxLength' => 3600, // Maximum recording length in seconds (1 hour)
		    'transcribe' => false, // Whether to transcribe the recording
		    'playBeep' => false,
		    'trim' => 'trim-silence'
		]);
		
		// Output the TwiML
		header('Content-Type: text/xml');
		echo $response;
    }

    public function twilioWebhook(Request $request)
    {
	    die();
        $meetingPin = $request->input('meeting_pin'); // Retrieve PIN from request
        $meetingNumber = $request->input('meeting_number');

        try {
	    	$response = new VoiceResponse();
            $dial = $response->dial();
            $dial->number($meetingNumber, ['sendDigits' => 'ww'.$meetingPin.'#']);
			$response->record();
	
            return response($response, 200)->header('Content-Type', 'text/xml');
        } catch (\Exception $e) {
            \Log::error('Error generating TwiML:', ['error' => $e->getMessage()]);
            return response('Error processing request.', 500);
        }
    }

    public function handleTranscription(Request $request)
    {
        // Handle transcription callback
        $transcriptionText = $request->input('TranscriptionText');

        try {
        \App\Models\Transcription::create([
            'text' => $transcriptionText,
            'meeting_name' => $request->input('ConferenceSid'),
        ]);

        return response()->json(['message' => 'Transcription saved successfully.']);
        } catch (\Exception $e) {
            \Log::error('Error saving transcription:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to save transcription.'], 500);
        }
    }
}