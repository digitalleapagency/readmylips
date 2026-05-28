<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;

use Auth;
use Mail;
use DateTime;

use App\Models\User;
use App\Models\CustomerSetting;
use App\Models\Customer;
use App\Models\Meeting;
use App\Models\Booking;
use App\Models\Asset;

class MeetingController extends Controller
{
    public function index($type = false) {
	    $customer = \Request::header('Customer');
	    
	    $meetings = Meeting::where('customer_id', $customer)->orderBy('start_date', 'desc');
	    
	    if($type) {
		    switch($type) {
			    case 'future':
			    	$meetings->where('start_date', '>=', time());
			    	break;
			    case 'past':
			    	$meetings->where('start_date', '<', time());
			    	$meetings->limit(20);
			    	break;
			    case 'summarized':
			    	$meetings->where('summarized', 1);
			    	break;
		    }
	    }
	    
	    $meetings = $meetings->get();
	    
	    return response()->json(['meetings' => $meetings], 200);
    }
    
    public function mail_summary($meeting_id) {
	    $meeting = Meeting::where('id', $meeting_id)->first();
	    
	    if($meeting->booking_id) {
		    $booking = Booking::where('id', $meeting->booking_id)->first();
		    $asset = Asset::where('id', $booking->asset_id)->first();
		    
		    $to = $booking->email;
		    $cc = $asset->email;
		    
		    $booking->status = 6;
		    $booking->finished = 1;
		    $booking->save();
		} else {
			$to = $meeting->invitees;
			$cc = false;
			
			if($to) {
				$to_expl = explode(',', $to);
				
				$to = array();
				foreach($to_expl as $expl) {
					$to[] = $expl;
				}
			}
		}
	    
	    $customer_settings = CustomerSetting::where('customer_id', $meeting->customer_id)->first();
						
		$fromName = $customer_settings->application_name;
		$name = strtolower($fromName);
		$name = preg_replace('/[^a-z0-9]/', '', $name);
		$fromEmail = $name . '@mlbx.email';
	    
	    $subject = 'Summary of your meeting at '.date('d-m-Y', $meeting->start_date);
        $mail_text = '<div style="text-align: left;">Below you can find the summary of the meeting we had on '.date('d-m-Y', $meeting->start_date).'.</div>';
		$mail_text .= '<br>';				
        $mail_text .= '<div style="text-align: left;">'.$meeting['summary'].'</div>';
		$mail_text .= '<br>';				
		$mail_text .= '<p>Kind regards!</p>';
		$mail_text .= '<p>'.$customer_settings->application_name.'</p>';
    
		$htmlMessage = view('mail', compact('mail_text', 'customer_settings'))->render();
		
		if($to) {
			
			if($cc) {
				// Send email with attachment
				Mail::mailer('xo_smtp')->send([], [], function ($message) use ($to, $cc, $subject, $htmlMessage, $fromEmail, $fromName) {
				    $message->to($to)
				    		->cc($cc)
							->from($fromEmail, $fromName)
				            ->subject($subject)
				            ->html($htmlMessage, 'text/html');
				});
			} else {
				// Send email with attachment
				Mail::mailer('xo_smtp')->send([], [], function ($message) use ($to, $subject, $htmlMessage, $fromEmail, $fromName) {
				    $message->to($to)
							->from($fromEmail, $fromName)
				            ->subject($subject)
				            ->html($htmlMessage, 'text/html');
				});
			}
		}
	    
	    return response()->json(['meeting' => $meeting], 200);
    }
    
    public function update($meeting_id, Request $request) {
	    $meeting = Meeting::where('id', $meeting_id)->first();
	    
	    $meeting->summary = $request->summary;
	    $meeting->save();
	    
	    return response()->json(['meeting' => $meeting], 200);
    }
    
    public function show($meeting_id) {
	    $meeting = Meeting::where('id', $meeting_id)->first();
	    
	    return response()->json(['meeting' => $meeting], 200);
    }
    
    public function store(Request $request) {	 
	    $customer = \Request::header('Customer');
	    $user = User::find(Auth::user()->id);
	    
	    $api_key = env('WHEREBY_KEY');
	    
	    $headers = [
            'Authorization: Bearer '.$api_key,
            'Content-Type: application/json',
        ];

        $data = [
            'startDate' => date('c', strtotime($request->date.' '.$request->date_hour_start)),
            'endDate' => date('c', strtotime('+90 days')), // Adjust as needed
            'fields' => [
                "hostRoomUrl", "viewerRoomUrl"
            ],
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.whereby.dev/v1/meetings');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        curl_close($ch);

        $responseData = json_decode($response, true);
        
        /* Insert new meeting */
	    $meeting = new Meeting;
	    $meeting->fill([
		    'customer_id' => $customer,
		    'title' => $request->title,
		    'start_date' => strtotime($request->date.' '.$request->date_hour_start),
		    'end_date' => strtotime($request->date.' '.$request->date_hour_end),
		    'user_id' => $user->id,
		    'invitees' => $request->invitees,
		    'text_invite' => $request->text_invite,
		    'meetingId' => $responseData['meetingId'],
		    'roomName' => $responseData['roomName'],
		    'roomUrl' => $responseData['roomUrl'],
		    'hostRoomUrl' => $responseData['hostRoomUrl'],
		    'viewerRoomUrl' => $responseData['viewerRoomUrl']
	    ]);
	    
	    $meeting->save();
	    
	    $customer_settings = CustomerSetting::where('customer_id', $customer)->first();
	    
	    /* Send email */
		$start_time = $request->date.' '.$request->date_hour_start;
		$end_time = $request->date.' '.$request->date_hour_end;
		    
		$icalContent = Calendar::create($request->title)
		    ->event(
		        Event::create($request->title)
		            ->startsAt(new DateTime($start_time))
		            ->endsAt(new DateTime($end_time))
				    ->description($request->text_invite . $responseData['roomUrl'])
				    ->address($responseData['roomUrl'])
		    )
		    ->get();
		
		// Save iCal content to a file
		if($request->invitees) {
			$fileName = 'event.ics'; // Choose a file name
			Storage::put($fileName, $icalContent);
			
			$fromName = $customer_settings->application_name;
			$name = strtolower($fromName);
			$name = preg_replace('/[^a-z0-9]/', '', $name);
			$fromEmail = $name . '@mlbx.email';
			
			$user = User::find(Auth::user()->id);
	        $reply_to = $user->email;
			
			$to = explode(',', trim($request->invitees));
			
			$subject = $request->title.' '.$request->date;
	        $mail_text = '<div style="text-align: left;">'.$request->text_invite.'</div>';
			$mail_text .= '<br>';				
	        $mail_text .= '<div style="text-align: left;"><a href="'.$responseData['roomUrl'].'" target="_blank">Meeting room link</a>.</div>';
			$mail_text .= '<br>';
			    
			$htmlMessage = view('mail', compact('mail_text', 'customer_settings'))->render();
			
			Mail::mailer('xo_smtp')->send([], [], function ($message) use ($to, $subject, $htmlMessage, $fileName, $reply_to, $fromEmail, $fromName) {
			    $message->to($to)
			    		->cc($reply_to)
			    		->replyTo($reply_to)
			            ->from($fromEmail, $fromName)
			            ->subject($subject)
			            ->html($htmlMessage, 'text/html') // Set the content type to HTML
			            ->attach(Storage::path($fileName), ['as' => 'event.ics', 'mime' => 'text/calendar']);
			});
		}
	    
	    return response()->json(['meeting' => $meeting], 200);
    }
    
    public function generate_summary($meeting_id) {
	    
    }
}
