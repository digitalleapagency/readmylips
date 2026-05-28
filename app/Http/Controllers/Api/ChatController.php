<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Booking;
use App\Models\User;
use App\Models\Asset;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\MailSettings;

use Twilio\Rest\Client;

use Auth;
use Chat;
use Mail;

class ChatController extends Controller
{
    public function index($type = false) {
		$conversation = Chat::conversations();
    }
    
    public function message(Request $request, $id) {
	    $customer = \Request::header('Customer');
	    
	    $data = self::getConversation($id);
	    $logged_in_user = User::where('id', Auth::user()->id)->first();
	    
	    $id = $data['id'];
	    $conversation = $data['conversation'];
	    
	    $message = Chat::message($request->message)->from($logged_in_user)->to($conversation)->send();
	    $data = self::getConversation($id);
	    
	    $id = $data['id'];
	    $conversation = $data['conversation'];
        
        $participants = $conversation->getParticipants();
        $messages = Chat::conversation($conversation)->setParticipant($logged_in_user)->setPaginationParams([
            'page' => 1,
            'perPage' => 200,
            'sorting' => "asc",
            'columns' => [
                '*'
            ]
        ])->getMessages();
        
        /* Loop through the admins to detect if it is a user or an admin */
        $admin_user_ids = RoleUser::where('role_id', 1)->where('customer_id', $customer)->pluck('user_id')->toArray();
		$is_admin = in_array($logged_in_user->id, $admin_user_ids);
		
		/* Send mail if is admin */
		if($is_admin) {
			$user_id = false;
			$type = '';
			
	        $participants = $conversation->getParticipants();
					
			foreach($participants as $participant) {
				if(!in_array($participant->id, $admin_user_ids)) {
					$user_id = $participant->id;
					$type = '';
				}
			}
			
			$response = '';
			
			if($user_id) {
				$asset = Asset::where('user_id', $id)->first();
				
				if($asset) {
					$to = $asset->email;
					
					if($to != '') {
				        $mailsettings = MailSettings::where('customer_id', $customer)->first();
				        $subject = $mailsettings->mail_newmessage_subject_nl;
				        $mail_text = '<div style="text-align: left;">'.nl2br($mailsettings->mail_newmessage_text_nl).'</div>';					
						$mail_text .= '<p>Fijne dag alvast!</p>';
						$mail_text .= '<p>Read My Lips Team!</p>';
					    
					    $htmlMessage = view('mail', compact('mail_text'))->render();
						
						Mail::send([], [], function ($message) use ($to, $subject, $htmlMessage) {
		                    $message->to($to)
		                        ->cc('admin@readmylips.be')
		                        ->subject($subject)
		                        ->html($htmlMessage, 'text/html');
		                });
	                }
	            }
	            
	            /* Test whatsapp */
/*
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, 'https://api.twilio.com/2010-04-01/Accounts/ACf52bd93b42e7e6dfed07e758ba4e915a/Messages.json');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
				curl_setopt($ch, CURLOPT_HTTPHEADER, [
				    'Content-Type: application/x-www-form-urlencoded',
				]);
				curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				curl_setopt($ch, CURLOPT_USERPWD, 'ACf52bd93b42e7e6dfed07e758ba4e915a:06f342cfb772f1ef6e4d2d169e54a400');
				curl_setopt($ch, CURLOPT_POSTFIELDS, 'To=whatsapp%3A%2B32472377593&From=whatsapp%3A%2B16509895047&Body=Nieuw bericht via ReadMyLips, je kan hier gewoon reageren. 
				
*Bericht:* 
'.$request->message);
*/
				
				/* Disable Twilio */
				//$response = curl_exec($ch);
				
				//curl_close($ch);
			}
		}

		return response()->json(['id' => $id, 'conversation' => $conversation, 'participants' => $participants, 'messages' => $messages], 200);	    
    }
    
    public function twilio_message() {
	    // Ensure requests are POST requests
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		    // Retrieve the body of the message
		    $body = $_POST['Body'];
		    $from = $_POST['From'];
		    
		    $logged_in_user = User::where('id', 8)->first();
		    $data = self::getConversation(8);
		    
		    $id = $data['id'];
		    $conversation = $data['conversation'];
		    
		    $message = Chat::message('Bericht van de klant via Whatsapp ('.$from.'): '.$body)->from($logged_in_user)->to($conversation)->send();		    
		
		    // Send a response back to the sender via Twilio
		    header('Content-Type: text/xml');
		    echo '<?xml version="1.0" encoding="UTF-8"?>';
		    echo '<Response>';
		    //echo '<Message>Bedankt voor je bericht, we beantwoorden het zo spoedig mogelijk!</Message>';
		    echo '</Response>';
		} else {
		    // Handle non-POST requests or show an error or a message indicating the method is not allowed
		    echo "HTTP Method not allowed";
		}
    }
    
    public function show($id = false) {
	    $logged_in_user = User::where('id', Auth::user()->id)->first();
	    $data = self::getConversation($id);
	    
	    $id = $data['id'];
	    $conversation = $data['conversation'];
	    $admin_user_ids = $data['admin_user_ids'];
        
        $participants = $conversation->getParticipants();
        $messages = Chat::conversation($conversation)->setParticipant($logged_in_user)->setPaginationParams([
            'page' => 1,
            'perPage' => 200,
            'sorting' => "asc",
            'columns' => [
                '*'
            ]
        ])->getMessages();
        
        /* Set read messages */
        sleep(2);
        
        Chat::conversation($conversation)->setParticipant($logged_in_user)->readAll();

		return response()->json(['id' => $id, 'admin_user_ids' => $admin_user_ids, 'conversation' => $conversation, 'participants' => $participants, 'messages' => $messages], 200);
    }
    
    public function unread() {
	    $logged_in_user = User::where('id', Auth::user()->id)->first();      
        $unreadCount = Chat::messages()->setParticipant($logged_in_user)->unreadCount();
        
        $unread_conversations = array();
        $unread_users = array();
        
        $conversations = Chat::conversations()->setParticipant($logged_in_user)->get();
        
        foreach($conversations as $conversation_detail) {
	        $conversation = Chat::conversations()->getById($conversation_detail->id);	        
	        $unreadcount = Chat::conversation($conversation)->setParticipant($logged_in_user)->unreadCount();
			
			if($unreadcount > 0) {							
				$participants = $conversation->getParticipants();
				
				foreach($participants as $participant) {
					$unread_users[] = $participant->id;
				}
				
				$unread_conversations[] = $conversation;
			}
        }

		return response()->json(['unreadCount' => $unreadCount, 'unreadConversations' => $unread_conversations, 'unread_users' => $unread_users], 200);
    }
    
    public function getConversation($id) {
	    $customer = \Request::header('Customer');
	    
	    $booking_check = false;
	    
	    if (strpos($id, 'b') !== false) {
		    $id = intval(str_replace('b', '', $id));
		    $booking_check = true;
		}
	    
	    if(Auth::check()) {
		    $user_id = Auth::user()->id;
	    } else {
		    $user_id = $id;
	    }
	    
	    if($id == 'overview') {
		    $admin_user_ids = RoleUser::where('role_id', 1)->where('customer_id', $customer)->pluck('user_id')->toArray();
		    
		    if(in_array($user_id, $admin_user_ids)) {
		    	$asset = Asset::where('user_id', '>', 0)->whereNotIn('user_id', $admin_user_ids)->where('customer_id', $customer)->orderBy('title', 'asc')->first();    	
		    	$id = $asset->user_id;
		    } else {
		    	$id = $user_id;
		    }
	    }
	    
	    /* If booking, check for  */
	    if($booking_check) {
		    $logged_in_user = Booking::where('id', $id)->first();
		} else {
			$logged_in_user = User::where('id', $user_id)->first();
		}
        
        /* If user is admin, check if a chat exists, if not create it */
        $user = User::where('id', $id)->first();
        

        $admin_user_ids = RoleUser::where('role_id', 1)->where('customer_id', $customer)->pluck('user_id');
/*
		$admins = User::whereIn('id', $admin_user_ids)->get();
*/
		
		$participants = array();
		
		if($booking_check) {
			$participants[] = $logged_in_user;
			$participants[] = $asset;
		} else {
			$participants[] = $logged_in_user;
			$participants[] = $user;
		}
		
/*
		foreach($admins as $admin) {
			$participants[] = $admin;
		}
*/
		
		$conversations = Chat::conversations()->setPaginationParams(['sorting' => 'desc'])->setParticipant($logged_in_user)->get();
		$conversation = null;
		
		foreach($conversations as $conversation_detail) {
			$conversation_detail = Chat::conversations()->getById($conversation_detail->id);
			foreach($conversation_detail->getParticipants() as $participant) {
				if(isset($participant->id) && $participant->id == $id && $participant->id) {
					$conversation = $conversation_detail;
				}
			}
		}
	        
        if($conversation == null) {
			$conversation = Chat::createConversation($participants)->makePrivate(true);
        }
        
        if($booking_check) {
	        $id = 'b'.$id;
        }
        
        return array('conversation' => $conversation, 'admin_user_ids' => $admin_user_ids, 'id' => $id);
    }
}
