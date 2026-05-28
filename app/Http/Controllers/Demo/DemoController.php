<?php

namespace App\Http\Controllers\Demo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

use Mollie\Laravel\Facades\Mollie;
use Mollie\Api\MollieApiClient;
use Google\Client as GoogleClient;
use Laravel\Socialite\Facades\Socialite;

use Spatie\LaravelPdf\Facades\Pdf;

use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

use FFMpeg\FFMpeg;
use FFMpeg\Format\Audio\Mp3;

use App\Models\Booking;
use App\Models\User;
use App\Models\Asset;
use App\Models\Setting;
use App\Models\Voucher;
use App\Models\BookingsAsset;
use App\Models\SettingsAgenda;
use App\Models\CustomerSetting;
use App\Models\MailSettings;
use App\Models\Category;
use App\Models\AssetCategory;
use App\Models\Meeting;
use App\Models\AssetLanguage;
use App\Models\Role;
use App\Models\RoleUser;

use App;
use Hash;
use Auth;
use DateTime;

use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\TeamleaderController;

class DemoController extends Controller {	
	public function add_hours() {
	    $assets = Asset::where('customer_id', 2)->get();
	
	    foreach ($assets as $asset) {
	        // Check if a settings agenda exists for the user
	        $settings_agenda = SettingsAgenda::where('user_id', $asset->user_id)->first();
	
	        if ($settings_agenda) {
	            echo 'FOUND ' . $asset->user_id;
	        } else {
	            // Get the first settings agenda as the base to duplicate
	            $first_settings_agenda = SettingsAgenda::where('id', 1)->first();
	
	            if ($first_settings_agenda) {
	                // Duplicate the row for the specific user_id
	                $new_settings_agenda = $first_settings_agenda->replicate();
	                $new_settings_agenda->user_id = $asset->user_id;
	                $new_settings_agenda->save();
	
	                echo 'ADDED for user_id: ' . $asset->user_id;
	            } else {
	                echo 'No base row found to duplicate.';
	            }
	        }
	    }
	}

	
	public function toInvoice() {
		$booking = Booking::where('status', 13)->inRandomOrder()->first();
		
		if($booking) {		
			var_dump($booking->id);
			
			if($booking->teamleader_feedback == NULL) {
				$teamleaderController = new TeamleaderController();
				$teamleaderController->createInvoice($booking->id);
			}
			
			/* Send mail */
			if($booking->teamleader_feedback != NULL && $booking->managing_user_id) {				
		        $user = User::find($booking->managing_user_id);
		        $reply_to = $user->email;
		        
		        $booking_assets = BookingsAsset::where('booking_id', $booking->id)->where('bookings_assets.booking_active', 1)->where('bookings_assets.customer_active', 1)->get();
				    
				foreach($booking_assets as $booking_asset) {	
				    $asset = Asset::where('id', $booking_asset->asset_id)->first();
				
			        $customer_settings = CustomerSetting::where('customer_id', $asset->customer_id)->first();
			        
			        $mailsettings = MailSettings::first();
			        $subject = $mailsettings->mail_final_booking_asset_subject_nl.' Ref: #'.$booking->id;
			        $mail_text = '<div style="text-align: left;">'.nl2br($mailsettings->mail_final_booking_asset_text_nl).'</div>';
			        $mail_text .= '<br>';				
					$mail_text .= '<br>';				
					$mail_text .= '<p>Fijne dag alvast!</p>';
					$mail_text .= '<p>'.$customer_settings->application_name.'</p>';
					
					// Define the string to substitute for [{offer_link}]
					$substitute_link = '<p>Klik hier om de call sheet te <a href="https://offer.readmylips.be/booking/'.$booking->id.'/cmVxdWVzdA==/'.$booking_asset->asset_id.'">openen</a>.</p>';
					
					// Check if [{offer_link}] exists in $mailtext
					if (strpos($mail_text, '[{offer_link}]') !== false) {
					    // Substitute [{offer_link}] with the actual link
					    $mail_text = str_replace('[{offer_link}]', $substitute_link, $mail_text);
					} else {
					    // Append the actual link at the end if [{offer_link}] is not found
					    $mail_text .= $substitute_link;
					}
				    
				    $htmlMessage = view('mail', compact('mail_text', 'user', 'customer_settings'))->render();
			        
			        if($asset->email) {
					    $to = $asset->email;
					    $cc = false;
					
						// Send email with attachment
						if($asset->email_assistant <> '') {
							$array_cc[] = $asset->email_assistant;
							
							$cc = $array_cc;
						}
						
						$fromName = $customer_settings->application_name;
						$name = strtolower($fromName);
						$name = preg_replace('/[^a-z0-9]/', '', $name);
						$fromEmail = 'contact@readmylips.be';
						
						if($cc) {
							Mail::send([], [], function ($message) use ($to, $cc, $subject, $htmlMessage, $reply_to, $fromEmail, $fromName) {
							    $message->to($to)
							    		->replyTo($reply_to)
							            ->cc($cc)
							            ->from($fromEmail, $fromName)
							            ->subject($subject)
							            ->html($htmlMessage, 'text/html');
							});
						} else {
							Mail::send([], [], function ($message) use ($to, $subject, $htmlMessage, $reply_to, $fromEmail, $fromName) {
							    $message->to($to)
							    		->replyTo($reply_to)
							            ->from($fromEmail, $fromName)
							            ->subject($subject)
							            ->html($htmlMessage, 'text/html');
							});
						}
					}
				}
				
				/* Finish booking */		
				$bookingController = new BookingController();
				$res = $bookingController->finish_booking($booking->id);
			}
		}
	}
	
	public function reminders_cron() {
		/* Bookings 48 hour reminder */
		$bookings_48 = Booking::where('customer_id', 1);
		$bookings_48->where('last_mail_send', '>', 0);
		$bookings_48->where('last_mail_send', '<', (time()-(86400*2)));
		$bookings_48->where('customer_refused', 0);
		$bookings_48->where('status', 10);
		$bookings_48 = $bookings_48->get();
		
		echo 'Bookings 48: '.count($bookings_48);
		
		foreach($bookings_48 as $booking) {
			if($booking->managing_user_id) {
				$bookingController = new BookingController();
				$res = $bookingController->mail_estimate_rml($booking->id, 83);
			}
		}
		
		/* Bookings 72 hour reminder */
		$bookings_72 = Booking::where('customer_id', 1);
		$bookings_72->where('last_mail_send', '>', 0);
		$bookings_72->where('last_mail_send', '<', (time()-(86400)));
		$bookings_72->where('customer_refused', 0);
		$bookings_72->where('status', 83);
		$bookings_72 = $bookings_72->get();
		
		echo 'Bookings 72: '.count($bookings_72);
		
		foreach($bookings_72 as $booking) {
			$booking->status = 81;
			$booking->save();
		}
	}
	
	public function intro_mail() {
		/* Get all assets who do not have a user_id yet */
		$asset = Asset::where('active', 1)->where('customer_id', 2)->whereNULL('user_id')->first();
		
		echo $asset->title;
		echo '<br>';
		
		/* Generate a new OTL if there is none */
		$user = User::where('email', $asset->email)->first();
		
		$token = Str::random(40);
		if($user) {
			echo 'User found <br>';
		} else {
			$user = new User;
			$user->password = Hash::make('12345678');
			$user->name = $asset->title;
			$user->email = $asset->email;
			
			echo 'User created <br>';
		}
		
		$user->one_time_token = $token;
		$user->save();
		
		$role = RoleUser::where('user_id', $user->id)->where('customer_id', $asset->customer_id)->first();
		
		if($role) {
			
		} else {
			$user->roles()->attach(Role::where('name', 'user')->first());
			    
		    $role = RoleUser::where('user_id', $user->id)->latest()->first();
		    $role->customer_id = $asset->customer_id;
		    $role->save();
	    }
		
		$asset->user_id = $user->id;
		$asset->save();
		
		$to = $asset->email;
		
		/* Generate the email for it and send it out */		        
        $fromName = 'Read My Lips Expert';
		$fromEmail = 'contact@readmylips.be';
		
        $subject = 'Login voor Read My Lips Expert';
        $mail_text = '<div style="text-align: left;">Beste Expert, <br><br>In navolging van de e-mail van vorige week over de aankondiging dat Xpertopinion zal opgaan in Readmylips (www.readmylips.be/vraag-een-expert), vind je hierbij de link naar het platform die je meteen zal inloggen en vragen je wachtwoord opnieuw in te stellen.
</div>';
        $mail_text .= '<p><a href="https://platform.readmylips.be/login?otl='.$token.'">Klik hier om in te loggen</a></p>';
		$mail_text .= '<br>';
		$mail_text .= 'In de settings kan je je beschikbaarheden kenbaar maken. Standaard zijn alle tijdsloten gedurende de werkdag beschikbaar, maar je kan dit beperken naar goedkeuring. Nieuw is dat je nu ook je Google agenda kan koppelen, zodat er een check gebeurt tijdens de aangegeven tijdsloten of je beschikbaar bent. Mocht je tijdens die momenten al een geplande meeting hebben, dan zal je beschikbaarheid op dat moment niet zichtbaar zijn. Er zal binnenkort ook een Outlook/O365-connector beschikbaar komen. Je kunt je agenda koppelen door op de knop te drukken in de beschikbaarheden.';
		$mail_text .= '<br>';
		$mail_text .= '<br>';
		$mail_text .= 'Het is mogelijk om op elk moment zelf je profieltekst aan te passen en een YouTube-video toe te voegen. In de nabije toekomst kan je ook documenten en presentaties toevoegen, hier later meer over.';
		$mail_text .= '<br>';
		$mail_text .= '<br>';
		$mail_text .= 'Bij een boeking kun je voortaan je factuur naar Readmylips sturen nadat de sessie heeft plaatsgevonden. De vergoeding blijft €200 per videogesprek.';
		$mail_text .= '<br>';
		$mail_text .= '<br>';
		$mail_text .= 'Een nieuwigheid is dat je na het gesprek een AI-gegenereerde samenvatting via e-mail ontvangt. Door op de link te klikken word je naar de boekingsomgeving gebracht, waar je deze naar wens kan aanpassen, opslaan en doorsturen naar de aanvrager en jezelf.';
		$mail_text .= '<br>';
		$mail_text .= '<br>';
		$mail_text .= 'We zijn zeer verheugd over deze volgende stap van ons vernieuwende aanbod, waarbij we meer dan ooit je tijd willen valoriseren. Voor Readmylips is dit een vernieuwend aanbod, waarmee ze naast het inspireren van organisaties nu ook ondersteuning willen bieden door middel van expertadvies. Een win-win voor iedereen.';
		$mail_text .= '<br>';
		$mail_text .= '<br>';
				
		$mail_text .= '<p>Met vriendelijke groet,</p>';
		$mail_text .= '<p>Read My Lips Experts</p>';
		
		$customer_settings = CustomerSetting::where('customer_id', $asset->customer_id)->first();
    
		$htmlMessage = view('mail', compact('mail_text', 'customer_settings'))->render();	
							
		// Send email with attachment
		Mail::mailer('rml_smtp')->send([], [], function ($message) use ($to, $subject, $htmlMessage, $fromEmail, $fromName) {
		    $message->to($to)
		            ->subject($subject)
					->from($fromEmail, $fromName)
		            ->html($htmlMessage, 'text/html');
		});
	}
	
	public function booking_converter() {
		$bookings = Booking::where('customer_id', 1)->orderBy('id', 'desc')->get();
		
		foreach($bookings as $booking) {
			$booking_asset = BookingsAsset::where('booking_id', $booking->id)->first();
			
			if($booking_asset) {
				
			} else {
				$booking_asset = new BookingsAsset();
				
				$booking_asset->booking_id = $booking->id;
				$booking_asset->asset_id = $booking->asset_id;
				
				$booking_asset->accepted = $booking->accepted;
				$booking_asset->refused_reason = $booking->customer_refused_reason;
				
				$booking_asset->customer_active = 1;
				$booking_asset->booking_active = 1;
				
				$estimate_lines = json_decode($booking->customer_estimate_lines);
				
				if($estimate_lines && !is_string($estimate_lines)) {
					$booking_lines = array();
					$asset_lines = array();
					
					foreach($estimate_lines as $estimate_line) {
						
						if(!isset($estimate_line->visible_speaker) || isset($estimate_line->visible_speaker) && $estimate_line->visible_speaker == 0) {
							$booking_lines[] = $estimate_line;
						} else {
							$asset_lines[] = $estimate_line;
						}
					}
					
					if(count($asset_lines)) {
						$booking_asset->estimate = json_encode($asset_lines);
					}
					
					if(count($booking_lines)) {
						$booking->customer_estimate_lines = json_encode($booking_lines);
					}
				}
				
				$booking_asset->save();
				$booking->save();
			}
		}	
	}
	
	public function scraper() {
		$client = new Client();
        $url = 'https://readmylips.be/sprekers';
        $response = $client->request('GET', $url);

        $crawler = new Crawler($response->getBody()->getContents());
        
        dd($crawler);

        $speakers = [];

        $crawler->filter('.speaker-profile-class') // Adjust the filter based on actual HTML structure
            ->each(function ($node) use (&$speakers) {
                $name = $node->filter('.speaker-name-class')->text(); // Adjust the filter based on actual HTML structure
                $link = $node->filter('a')->attr('href');
                $iframe = $node->filter('iframe')->attr('src');

                $speakers[] = [
                    'name' => $name,
                    'link' => $link,
                    'iframe' => $iframe,
                ];
            });

        // Log the scraped data (or you can save it to the database)
        foreach ($speakers as $speaker) {
            $this->info('Name: ' . $speaker['name']);
            $this->info('Link: ' . $speaker['link']);
            $this->info('Iframe: ' . $speaker['iframe']);
            $this->info('---');
        }	
	}
	
	public function create_users() {
		$assets = Asset::get();
		
		foreach($assets as $asset) {
			if($asset->email) {
				if($asset->user_id) {
					$user = User::where('id', $asset->user_id)->first();
				} else {
					$user = User::where('email', $asset->email)->first();
				}
			}
			
			echo $user->email;
			echo '<br>';
			
			/* Create user if not exist */
			if(!$user) {
				$user = new User();
			    $user->name = $asset->title;
			    $user->email = $asset->email;
			    $user->password = Hash::make('12345678');
			    $user->save();
			    
			    $asset->user_id = $user->id;
			    $asset->save();
			    
			    echo 'user created';
			} else {
				echo 'user exists';
			}
			
			echo '<br>';
			
			if($user) {
				$role = RoleUser::where('user_id', $user->id)->where('customer_id', $asset->customer_id)->first();
				
				if($role) {
					echo 'role exists';
				} else {
					echo 'role needs to be created';
					
					$user->roles()->attach(Role::where('name', 'user')->first());
		    
				    $role = RoleUser::where('user_id', $user->id)->latest()->first();
				    $role->customer_id = $asset->customer_id;
				    $role->save();
				}
			}
			
			echo '<br>';
			echo '<br>';
		}
	}
	
	public function link_head_cats() {
		$assets = Asset::get();
		$head_categories = Category::where('category_id', NULL)->get();
		$headcats = array();
		
		foreach($head_categories as $headcat) {
			$headcats[] = $headcat->id;
		}
		
		foreach($assets as $asset) {
			$asset_cats = AssetCategory::where('asset_id', $asset->id)->get();
			
			foreach($asset_cats as $cat) {
				if(!in_array($cat->category_id, $headcats)) {
					$catdetail = Category::where('id', $cat->category_id)->first();
					$has_cat = AssetCategory::where('asset_id', $asset->id)->where('category_id', $catdetail->category_id)->first();
					
					if($has_cat) {
						echo 'Already Exists <br>';
					} else {
						$assetcat = new AssetCategory;
						$assetcat->asset_id = $asset->id;
						$assetcat->category_id = $catdetail->category_id;
						$assetcat->save();
						
						echo 'Need to create <br>';
					}
				}
			}
		}
	}
	
	public function link_users_roles() {
		$users = User::get();
		
		foreach($users as $user) {
			$assets = Asset::where('user_id', $user->id)->get();
			
			foreach($assets as $asset) {
				$userRole = RoleUser::where('user_id', $user->id)->where('customer_id', $asset->customer_id)->first();
				
				if($userRole) {
					
				} else {
					$role = new RoleUser;
					
					$role->customer_id = $asset->customer_id;
					$role->user_id = $user->id;
					$role->role_id = 3;
					$role->save();
				}
			}
		}
	}
	
	public function get_json_format($fullText) {		
		// Find the position of the first '{' (start of JSON)
		$startPos = strpos($fullText, '{');
		
		// Find the position of the last '}' (end of JSON)
		$endPos = strrpos($fullText, '}');
		
		if ($startPos !== false && $endPos !== false) {
		    // Extract JSON substring
		    $jsonString = substr($fullText, $startPos, $endPos - $startPos + 1);
		    
		    // Decode JSON string
		    $jsonData = json_decode($jsonString, true); // true for associative array
		    
		    // Check if json_decode() was successful
		    if (json_last_error() === JSON_ERROR_NONE) {
		        return $jsonData;
		    } else {
		        return false;
		    }
		} else {
		        return false;
		}
	}
	
	function summarize_recordings($meetingId = false) {		
		if($meetingId) {
			$meeting = Meeting::where('id', $meetingId)->first();
		} else {
			$meeting = Meeting::whereNotNull('audio_link')->whereNotNull('text')->where('summarized', 0)->first();
		}
		
		if($meeting) {			
			$booking = Booking::where('id', $meeting->booking_id)->first();
			$date = 'A date that was not specified.';
			$between = '';
			$language = 'NL';
			$prompt = 'Maak een samenvatting van de meeting maar enkel in het nederlands. ';
			
			if($booking) {
				$date = $booking->date;	
				$asset = Asset::where('id', $booking->asset_id)->first();
				$language = $booking->language;
				
				if($language == 'nl') {
					$prompt = 'Maak een samenvatting van de meeting maar enkel in het nederlands. ';
					$between = 'De meeting werd gehouden tussen '.$asset->title.' en '.$booking->invoice_name.'.';
				} else {
					$prompt = 'Generate a summary of a meeting but only in english. ';
					$between = 'The meeting took place between '.$asset->title.' and '.$booking->invoice_name.'.';
				}
			}
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://api.edenai.run/v2/text/generation');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
			    'authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoiZjZhOWNiNTEtNjBlYy00ZjczLWE3NTktMDllMzFhZmQ4ZWI3IiwidHlwZSI6ImFwaV90b2tlbiJ9.ct5ZHDM2-iFDCCJbML0F4N5U5jFhPCvkDuqG6xXxzBc',
			    'content-type: application/json',
			]);
			
			curl_setopt($ch, CURLOPT_POSTFIELDS, "{\n  \"providers\": \"google/gemini-pro\",\n  \"text\": \"".$prompt." ".$date." ".$between.": ".$meeting->text." }\",\n  \"max_tokens\": 3000,\n   \"temperature\": 0.3 \n}");
			
			$response = curl_exec($ch);
			
			curl_close($ch);
			
			$result = (array)json_decode($response);
			
			if(isset($result['google/gemini-pro']->generated_text)) {
				$meeting->summary = Str::markdown($result['google/gemini-pro']->generated_text);
				$meeting->summarized = 1;
				$meeting->save();
				
				if($meetingId) {
					return $meeting;
				} else {
					$booking = Booking::where('id', $meeting->booking_id)->first();
					$asset = Asset::where('id', $booking->asset_id)->first();
					
					if($asset->email) {
				        $to = $asset->email;
				        
				        $customer_settings = CustomerSetting::where('customer_id', $booking->customer_id)->first();
						
						$fromName = $customer_settings->application_name;
						$name = strtolower($fromName);
						$name = preg_replace('/[^a-z0-9]/', '', $name);
						//$fromEmail = $name . '@mlbx.email';
						$fromEmail = 'contact@readmylips.be';
					
				        $mailsettings = MailSettings::where('customer_id', $booking->customer_id)->first();
				        
				        $subject = $mailsettings->mail_summary_subject_nl;
				        $mail_text = '<div style="text-align: left;">'.$mailsettings->mail_summary_text_nl.'</div>';
				        $mail_text .= '<div style="text-align: left;"><p><a href="'.env('VUE_URL').'/meeting/'.$meeting->id.'?customer='.$asset->customer_id.'">Bekijk de samenvatting hier</a>.</p></div>';
						$mail_text .= '<br>';				
						$mail_text .= '<p>Have a great day!</p>';
						$mail_text .= '<p>'.$customer_settings->application_name.'</p>';
			        
						$htmlMessage = view('mail', compact('mail_text', 'customer_settings'))->render();	
											
						// Send email with attachment
						Mail::mailer('rml_smtp')->send([], [], function ($message) use ($to, $subject, $htmlMessage, $fromEmail, $fromName) {
						    $message->to($to)
						            ->subject($subject)
									->from($fromEmail, $fromName)
						            ->html($htmlMessage, 'text/html');
						});
					}
				}
			}
			
			
			die();
			
			curl_setopt($ch, CURLOPT_POSTFIELDS, "{\n  \"providers\": \"mistral, google, cohere, anthropic\",\n  \"text\": \"Generate a summary of this meeting text between 2 people and return me this in a json format. The output should be in the detected language of the meeting text. Meeting text: ".$meeting->text." Json format example: { 'keypoints': [ 'key point 1', 'key point 2', 'key point 3' ], 'actionitems': [ 'action item 1', 'action item 2', 'action item 3' ], 'nextsteps': [ 'next step 1' ], 'additionalnotes': [ 'additional note 1' ], 'additionalinfo': [ 'additional info 1' ], 'questions': [ 'question 1' ], 'whotookpart': [ 'person 1', 'person 2' ], 'fulltext': 'A full text summary of the meeting in at least 5 full sentences', 'topic': 'Topic of the meeting', 'conclusion': 'Conclusion text of the meeting' }\",\n  \"max_tokens\": 3000,\n   \"temperature\": 0 \n}");
			
			$response = curl_exec($ch);
			
			curl_close($ch);
			
			$result = json_decode($response);
			
			$summary_text = '';
			
			$providers = array('mistral', 'google', 'cohere', 'anthropic');
			
			foreach($providers as $provider) {
				if(isset($result->$provider->generated_text)) {
					$decoded = self::get_json_format($result->$provider->generated_text);
					
					if($decoded) {
						if(isset($decoded['topic'])) {	
							$summary_text .= '<h2>'.$decoded['topic'].' <small>by '.ucfirst($provider).'</small></h2>';
						}
							
						if(isset($decoded['fulltext'])) {
							$summary_text .= $decoded['fulltext'];
						}
						
						if(isset($decoded['keypoints'])) {
							$summary_text .= '<h4>Key points</h4>';
							$summary_text .= '<ul>';
							
							foreach($decoded['keypoints'] as $text) {
								$summary_text .= '<li>'.$text.'</li>';
							}
							$summary_text .= '</ul>';
						}
						
						if(isset($decoded['actionitems'])) {
							$summary_text .= '<h4>Action items</h4>';
							$summary_text .= '<ul>';
							
							foreach($decoded['actionitems'] as $text) {
								$summary_text .= '<li>'.$text.'</li>';
							}
							$summary_text .= '</ul>';
						}
						
						if(isset($decoded['nextsteps'])) {
							$summary_text .= '<h4>Next steps</h4>';
							$summary_text .= '<ul>';
							
							foreach($decoded['nextsteps'] as $text) {
								$summary_text .= '<li>'.$text.'</li>';
							}
							$summary_text .= '</ul>';
						}
						
/*
						if(isset($decoded['additionalnotes'])) {	
							$summary_text .= '<h4>Additional notes</h4>';
							$summary_text .= '<ul>';
							
							foreach($decoded['additionalnotes'] as $text) {
								$summary_text .= '<li>'.$text.'</li>';
							}
							$summary_text .= '</ul>';
						}
						
						if(isset($decoded['additionalinfo'])) {	
							$summary_text .= '<h4>Additional info</h4>';
							$summary_text .= '<ul>';
							
							foreach($decoded['additionalinfo'] as $text) {
								$summary_text .= '<li>'.$text.'</li>';
							}
							$summary_text .= '</ul>';
						}
*/
						
						if(isset($decoded['questions'])) {	
							$summary_text .= '<h4>Questions</h4>';
							$summary_text .= '<ul>';
						
							foreach($decoded['questions'] as $text) {
								$summary_text .= '<li>'.$text.'</li>';
							}
							$summary_text .= '</ul>';
						}
							
/*
						if(isset($decoded['conclusion'])) {
							$summary_text .= '<h4>Conclusion</h4>';
							$summary_text .= $decoded['conclusion'];	
						}
*/		
					}
				}
			}
			
			if($summary_text) {
				$meeting->summary = Str::markdown($summary_text);
				$meeting->summarized = 1;
				$meeting->save();
				
				$booking = Booking::where('id', $meeting->booking_id)->first();
				$asset = Asset::where('id', $booking->asset_id)->first();
				
				if($asset->email) {
			        $to = $asset->email;
				
			        $mailsettings = MailSettings::first();
			        $subject = 'New summary available';
			        $mail_text = '<div style="text-align: left;">There is a new summary available, you can edit it and send it to the customer from inside the XpertBooking platform.</div>';
			        $mail_text .= '<div style="text-align: left;"><p>Click to view the summary <a href="'.env('VUE_URL').'/meeting/'.$meeting->id.'">here</a>.</p></div>';
					$mail_text .= '<br>';				
					$mail_text .= '<p>Have a great day!</p>';
					$mail_text .= '<p>XpertBooking!</p>';
					
					$customer_settings = CustomerSetting::where('customer_id', $asset->customer_id)->first();
		        
					$htmlMessage = view('mail', compact('mail_text', 'customer_settings'))->render();
					
					// Send email with attachment
					Mail::mailer('rml_smtp')->send([], [], function ($message) use ($to, $subject, $htmlMessage) {
					    $message->to($to)
					            ->subject($subject)
						        ->from('contact@readmylips.be', 'Xpertbooking')
					            ->html($htmlMessage, 'text/html');
					});
				}
			}
		}
	}
	
	function splitMp3File($filePath, $outputDir, $baseUrl, $maxFileSizeMB = 5) {
	    $maxFileSizeBytes = $maxFileSizeMB * 1024 * 1024;
	    $outputFiles = [];
	    
	    if (!file_exists($filePath)) {
	        die("File does not exist.");
	    }
	    
	    if (!is_dir($outputDir)) {
	        mkdir($outputDir, 0777, true);
	    }
	
	    // Get the original file name without the directory path
	    $originalFileName = pathinfo($filePath, PATHINFO_FILENAME);
	    $originalFileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
	
	    $inputFile = fopen($filePath, "rb");
	    if ($inputFile === false) {
	        die("Failed to open input file.");
	    }
	    
	    $partNumber = 1;
	    $currentOutputFile = null;
	    $currentOutputFileSize = 0;
	
	    while (!feof($inputFile)) {
	        // Read a chunk of data from the input file
	        $buffer = fread($inputFile, 8192);
	        if ($buffer === false) {
	            die("Failed to read from input file.");
	        }
	
	        // If the current output file size plus buffer size exceeds the limit, close the current file
	        if ($currentOutputFileSize + strlen($buffer) > $maxFileSizeBytes) {
	            fclose($currentOutputFile);
	            $currentOutputFile = null;
	            $currentOutputFileSize = 0;
	            $partNumber++;
	        }
	
	        // If no current output file, create a new one
	        if ($currentOutputFile === null) {
	            $currentOutputFilePath = sprintf("%s/%s_part%d.%s", rtrim($outputDir, "/"), $originalFileName, $partNumber, $originalFileExtension);
	            $currentOutputFile = fopen($currentOutputFilePath, "wb");
	            if ($currentOutputFile === false) {
	                die("Failed to create output file: " . $currentOutputFilePath);
	            }
	            // Create the public URL and add to the array
	            $publicUrl = sprintf("%s/%s_part%d.%s", rtrim($baseUrl, "/"), $originalFileName, $partNumber, $originalFileExtension);
	            $outputFiles[] = $publicUrl;
	        }
	
	        // Write the buffer to the current output file
	        $written = fwrite($currentOutputFile, $buffer);
	        if ($written === false) {
	            die("Failed to write to output file.");
	        }
	
	        $currentOutputFileSize += $written;
	    }
	
	    // Close any open files
	    if ($currentOutputFile !== null) {
	        fclose($currentOutputFile);
	    }
	    
	    fclose($inputFile);
	
	    return $outputFiles;
	}	
	
	function to_text_recordings() {		
		$meeting = Meeting::whereNotNull('audio_link')->whereNull('text')->first();
		
		if($meeting && $meeting->text == '') {
			$local_file = str_replace(url('/'), '', $meeting->audio_link);
			
			// Use the public_path() helper to get the full path to the file
	        $filePath = public_path($local_file);
			$filesize = false;
			
	        // Check if the file exists
	        if (file_exists($filePath)) {
	            // Get the file size
	            $filesize = filesize($filePath);
	            $filesize /= 1048576;
	        }
	        
	        if($filesize && $filesize > 10) {
				$outputDir = public_path('audios/');
				$baseUrl = url('/audios');
				
				$splits = self::splitMp3File($filePath, $outputDir, $baseUrl);
	        } else {
		        $splits[] = $meeting->audio_link;
	        }
	        
	        $text = '';
			
			foreach($splits as $split) {
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, 'https://api.edenai.run/v2/audio/speech_to_text_async');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
				curl_setopt($ch, CURLOPT_HTTPHEADER, [
				    "authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoiZjZhOWNiNTEtNjBlYy00ZjczLWE3NTktMDllMzFhZmQ4ZWI3IiwidHlwZSI6ImFwaV90b2tlbiJ9.ct5ZHDM2-iFDCCJbML0F4N5U5jFhPCvkDuqG6xXxzBc",
				    'content-type: application/json',
				]);
				curl_setopt($ch, CURLOPT_POSTFIELDS, "{\n  \"providers\": \"openai\",\n  \"file_url\": \"".$split."?v=".time()."\",\n  \"speakers\": 2,\n \"profanity_filter\": false,\n  \"custom_vocabulary\": \"\"\n}");
				
				$response = curl_exec($ch);
				
				curl_close($ch);
				
				$result = json_decode($response);
				
				var_dump($result);
				
				if(isset($result->results->openai->text)) {
					$text .= $result->results->openai->text;
				}
			}
			
			$meeting->text = $text;
			$meeting->save();
		}
	}
	
	function get_recordings() {
		$api_key = env('WHEREBY_KEY');
	    
	    $headers = [
            'Authorization: Bearer ' . $api_key,
            'Content-Type: application/json',
        ];

        // First, get all meeting rooms
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.whereby.dev/v1/recordings');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        curl_close($ch);

        $responseData = json_decode($response, true);
        $count = 0;
        
        foreach($responseData['results'] as $recording) {
	        $meeting = Meeting::where('roomName', $recording['roomName'])->first();
	        	        
	        if($meeting && $meeting->audio_link == '' && $count == 0) {
		        $count++;		        
		        $roomname = str_replace('/', '', $recording['roomName']);
		        
		        $headers = [
		            'Authorization: Bearer ' . $api_key,
		            'Content-Type: application/json',
		        ];
		
		        // First, get all meeting rooms
		        $ch = curl_init();
		        curl_setopt($ch, CURLOPT_URL, 'https://api.whereby.dev/v1/recordings/'.$recording['recordingId'].'/access-link');
		        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		
		        $response = curl_exec($ch);
		        curl_close($ch);
		
		        $recording_detail = json_decode($response, true);
		        
		        if(isset($recording_detail['accessLink'])) {
			        $client = new Client();
					$url = $recording_detail['accessLink'];
			        $response = $client->get($url);
			
			        // Check if the request was successful (status code 200)
			        if ($response->getStatusCode() === 200) {
				        $video_file = 'video '.time();
			            // Save the video content to a local file
			            $videoPath = storage_path('../public/videos/'.$video_file.'.mp4');
			            file_put_contents($videoPath, $response->getBody());
			            
			            /* Video to MP3 */
			            $videoPath = public_path('videos/'.$video_file.'.mp4');
						$audioPath = public_path('audios/'.$roomname.'.mp3');
				
				        try {
						    $ffmpeg = FFMpeg::create();
						    $video = $ffmpeg->open($videoPath);
						
						    // Extract audio in mp3 format
						    $format = new Mp3();
						    $video->save($format, $audioPath);
						    
						    $meeting->audio_link = url('audios/'.$roomname.'.mp3');
						    $meeting->save();
						} catch (\Exception $e) {
						    return response()->json(['error' => 'Encoding failed', 'message' => $e->getMessage()], 500);
						}
			        }
			    }
			}
        }
	}
	
	function import_profiles() {
		// Define the path to your CSV file
		$csvFilePath = 'listings-export-data.csv';
		
		// Open the CSV file for reading
		$file = fopen($csvFilePath, 'r');
		
		// Check if the file opened successfully
		if (!$file) {
		    die('Error opening file!');
		}
		
		// Initialize an empty array to store the data
		$data = [];
		
		// Read each line from the CSV file and store it in the array
		while (($line = fgetcsv($file, 0, ',')) !== false) {
		    $data[] = $line;
		}
		
		// Close the file
		fclose($file);
		
		foreach ($data as $key => $row) {
			if($key > 0) {
				$asset = Asset::where('title', $row[2])->first();
				
				if($asset) {
					$asset->image = $row[8];
				} else {
					$image = explode(',', $row[8]);
					$row[8] = $image[0];
					
					$asset = new Asset;
				    $asset->fill([
					    'title' => $row[2],
					    'subtitle' => '',
					    'description' => strip_tags($row[3]),
					    'title_fr' => $row[2],
					    'subtitle_fr' => '',
					    'description_fr' => strip_tags($row[3]),
					    'title_en' => $row[2],
					    'subtitle_en' => '',
					    'description_en' => strip_tags($row[3]),
					    'image' => $row[8],
					    'asset_type_id' => 1,
					    'customer_id' => 2,
					    'price' => 20000
				    ]);
				}
				
				$asset->save();
				
				$row[5] = str_replace('&amp;', '&', $row[5]);
				$topics = explode(',', $row[5]);
				
				foreach($topics as $topic) {
					$category = Category::where('name_en', trim($topic))->where('customer_id', 2)->first();
					
					if($category) {
						
					} else {
						echo 'Category created';
						echo '<br><br>';
						$category = new Category;
						$category->name = trim($topic);
						$category->name_fr = trim($topic);
						$category->name_en = trim($topic);
						$category->customer_id = 2;
						$category->save();
					}
					
					$asset_cat = AssetCategory::where('asset_id', $asset->id)->where('category_id', $category->id)->first();
					
					if($asset_cat) {
						echo 'Linked';
						echo '<br><br>';
					} else {
						echo 'Linking now';
						echo '<br><br>';
						
						$asset_cat = new AssetCategory;
						$asset_cat->asset_id = $asset->id;
						$asset_cat->category_id = $category->id;
						$asset_cat->save();								
					}
				}
			}
		}
	}
	
	function import_profiles_rml() {		
		// Define the path to your CSV file
		$csvFilePath = 'readmylips-speakers.csv';
		
		// Open the CSV file for reading
		$file = fopen($csvFilePath, 'r');
		
		// Check if the file opened successfully
		if (!$file) {
		    die('Error opening file!');
		}
		
		// Initialize an empty array to store the data
		$data = [];
		
		// Read each line from the CSV file and store it in the array
		while (($line = fgetcsv($file, 0, ';')) !== false) {
		    $data[] = $line;
		}
		
		// Close the file
		fclose($file);
		
		// Now $data contains your CSV data. You can process it as needed.
		// For example, you can iterate through each row and display it:
		
		foreach ($data as $key => $row) {
			if($key > 0) {
				$assets = Asset::where('title', $row[1])->get();
				
				foreach($assets as $asset) {	
					if($row[7] == 'English') {
						$row[8] = str_replace('&amp;', '&', $row[8]);
						$topics = explode(',', $row[8]);
						
						foreach($topics as $topic) {
							$category = Category::where('name_en', trim($topic))->where('customer_id', 1)->first();
							
							if($category) {
								
							} else {
								echo 'Category created';
								echo '<br><br>';
								$category = new Category;
								$category->name = trim($topic);
								$category->name_fr = trim($topic);
								$category->name_en = trim($topic);
								$category->customer_id = 1;
								$category->save();
							}
							
							$asset_cat = AssetCategory::where('asset_id', $asset->id)->where('category_id', $category->id)->first();
							
							if($asset_cat) {
								echo 'Linked';
								echo '<br><br>';
							} else {
								echo 'Linking now';
								echo '<br><br>';
								
								$asset_cat = new AssetCategory;
								$asset_cat->asset_id = $asset->id;
								$asset_cat->category_id = $category->id;
								$asset_cat->save();								
							}
						}
						
						$languages = explode(',', $row[4]);
						
						foreach($languages as $language) {
							switch($language) {
								case 'Dutch':
									$lang = 1;
									break;
								case 'French':
									$lang = 2;
									break;
								case 'English':
									$lang = 3;
									break;
							}
							
							var_dump($language);
							
							$asset_lang = AssetLanguage::where('asset_id', $asset->id)->where('language_id', $lang)->first();
							
							if($asset_lang) {
								echo 'Language linked';
								echo '<br><br>';
							} else {
								echo 'Linking language now';
								echo '<br><br>';
								
								$asset_lang = new AssetLanguage;
								$asset_lang->asset_id = $asset->id;
								$asset_lang->language_id = $lang;
								$asset_lang->save();								
							}
						}
						
						$asset->description_en = nl2br($row[11]);
					}
					
					if($row[7] == 'French') {
						$asset->description_fr = nl2br($row[11]);
					}
					
					if($row[7] == 'Dutch') {
						$asset->description = nl2br($row[11]);
					}
					
					$asset->save();
				}
			}
		}
	}
	
	public function import_prices() {		
		// Define the path to your CSV file
		$csvFilePath = 'Export mobile.csv';
		
		// Open the CSV file for reading
		$file = fopen($csvFilePath, 'r');
		
		// Check if the file opened successfully
		if (!$file) {
		    die('Error opening file!');
		}
		
		// Initialize an empty array to store the data
		$data = [];
		
		// Read each line from the CSV file and store it in the array
		while (($line = fgetcsv($file, 0, ';')) !== false) {
		    $data[] = $line;
		}
		
		// Close the file
		fclose($file);
		
		// Now $data contains your CSV data. You can process it as needed.
		// For example, you can iterate through each row and display it:
		
		foreach ($data as $row) {
		    $asset = Asset::where('title', $row[0])->first();
		    
		    if($asset && $row[2] != 'steven@lowimpactman.be' && $row[2] != 'saskia.van.uffelen@telenet.be') {
/*
			    $asset->email = $row[2];
			    $asset->price = intval($row[1])*100;
			    $asset->notes = strip_tags($row[3]);
			    
*/
				$asset->phone = strip_tags($row[3]);
			    $asset->save();
		    }
		}
	}
	
	public function print_estimate($id) {
		$booking = Booking::where('id', $id)->first();
		$booking_assets = BookingsAsset::where('booking_id', $id)->where('booking_active', 1)->join('assets', 'assets.id', 'asset_id')->get();
		
		$asset = Asset::where('id', $booking->asset_id)->first();
		$customer_settings = CustomerSetting::where('customer_id', $asset->customer_id)->first();
		$speaker = false;
		$print = false;
		
		if($booking->customer_estimate_lines != '') {	        
	        $original = $booking->customer_estimate_lines;
	        
			$new = str_replace('\\', '', $original);
			$new = str_replace('"[', '[', $new);
			$new = str_replace(']"', ']', $new);
			$new = str_replace('""', '"', $new);
			
			$booking->customer_estimate_lines = $new;
			$booking->save();			
        }
		
		if($booking->managing_user_id) {
			$user = User::where('id', $booking->managing_user_id)->first();
			$user_email = $user->email;
		} else {
			$user_email = 'contact@readmylips.be';
		}
		
		App::setLocale($booking->language);

		return Pdf::view('estimate', compact('booking', 'booking_assets', 'asset', 'speaker', 'customer_settings', 'user_email', 'print'));
	}
	
	public function booking_estimate($id, $print = false) {
		$booking = Booking::where('id', $id)->first();
		$booking_assets = BookingsAsset::where('booking_id', $id)->where('booking_active', 1)->join('assets', 'assets.id', 'asset_id')->get();
		
		$asset = Asset::where('id', $booking->asset_id)->first();
		$customer_settings = CustomerSetting::where('customer_id', $asset->customer_id)->first();
		$speaker = false;
		
		if($booking->customer_estimate_lines != '') {	        
	        $original = $booking->customer_estimate_lines;
	        
			$new = str_replace('\\', '', $original);
			$new = str_replace('"[', '[', $new);
			$new = str_replace(']"', ']', $new);
			$new = str_replace('""', '"', $new);
			
			$booking->customer_estimate_lines = $new;
			$booking->save();			
        }
		
		if($booking->managing_user_id) {
			$user = User::where('id', $booking->managing_user_id)->first();
			$user_email = $user->email;
		} else {
			$user_email = 'contact@readmylips.be';
		}
		
		App::setLocale($booking->language);

		return view('estimate', compact('booking', 'booking_assets', 'asset', 'speaker', 'customer_settings', 'user_email', 'print'));
	}
	
	public function print_booking($id, $asset_id) {
		$booking = Booking::where('id', $id)->first();
		$asset = Asset::where('id', $asset_id)->first();
		$customer_settings = CustomerSetting::where('customer_id', $asset->customer_id)->first();
		$speaker = true;
		$print = true;
		
		$booking_assets = BookingsAsset::where('booking_id', $id)->where('bookings_assets.booking_active', 1)->where('bookings_assets.customer_active', 1)->where('asset_id', $asset_id)->get();
		
		return Pdf::view('estimate', compact('booking', 'asset', 'speaker', 'customer_settings', 'booking_assets', 'print'));
	}
	
	public function booking_request($id, $asset_id = false, $print = false) {
		$booking = Booking::where('id', $id)->first();
		
		if($asset_id) {
			$asset = Asset::where('id', $asset_id)->first();
		} else {
			$booking_asset = BookingsAsset::where('booking_id', $id)->where('bookings_assets.booking_active', 1)->where('bookings_assets.customer_active', 1)->first();
			$asset = Asset::where('id', $booking_asset->asset_id)->first();
			$asset_id = $booking_asset->asset_id;
		}
		
		$customer_settings = CustomerSetting::where('customer_id', $asset->customer_id)->first();
		$speaker = true;
		
		$booking_assets = BookingsAsset::where('booking_id', $id)->where('bookings_assets.booking_active', 1)->where('bookings_assets.customer_active', 1)->where('asset_id', $asset_id)->get();
		
		return view('estimate', compact('booking', 'asset', 'speaker', 'customer_settings', 'booking_assets', 'print'));
	}
    
    public function test() {
	    /* Test whatsapp */
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://api.twilio.com/2010-04-01/Accounts/ACf52bd93b42e7e6dfed07e758ba4e915a/Messages.json');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
		    'Content-Type: application/x-www-form-urlencoded',
		]);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, 'ACf52bd93b42e7e6dfed07e758ba4e915a:06f342cfb772f1ef6e4d2d169e54a400');
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'To=whatsapp%3A%2B32472377593&From=whatsapp%3A%2B16509895047&"ContentSid=HX5cf4a9a3f4bf994a923ceb3f3c06b4ad&"ContentVariables={"1":"coupon_code"}');
		
		/* Disable Twilio */
		$response = curl_exec($ch);
		
		curl_close($ch);
		
		var_dump($response);
    }
    
    public function whereby() {
	    $api_key = env('WHEREBY_KEY');
	    $roomId = 85458130;
	    
	    $headers = [
            'Authorization: Bearer ' . $api_key,
            'Content-Type: application/json',
        ];

        // First, get all meeting rooms
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.whereby.dev/v1/recordings');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        curl_close($ch);

        $responseData = json_decode($response, true);
        
        dd($responseData);
	    
	    die();
	    /* Get a specific room */
	    $headers = [
            'Authorization: Bearer ' . $api_key,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.whereby.dev/v1/meetings/' . $roomId);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        curl_close($ch);

        $responseData = json_decode($response, true);
        
	    dd($responseData);
	    
	    die();
	    /* Get all meeting rooms */
        $headers = [
            'Authorization: Bearer ' . $api_key,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.whereby.dev/v1/meetings');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        curl_close($ch);

        $responseData = json_decode($response, true);

        // Handle the response, $responseData now contains all the rooms
        dd($responseData);
	    
	    
	    die();
	    /* Create a new room */
	    $headers = [
            'Authorization: Bearer '.$api_key,
            'Content-Type: application/json',
        ];

        $data = [
            'startDate' => date('c'),
            'endDate' => date('c', strtotime('+21 days')), // Adjust as needed
            'fields' => [
                "hostRoomUrl", "viewerRoomUrl"
            ],
        ];
        
        var_dump($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.whereby.dev/v1/meetings');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        curl_close($ch);

        $responseData = json_decode($response, true);
        
        dd($responseData);

        if(isset($responseData['roomUrl'])) {
            // Room created successfully, handle the response as needed
            return redirect()->away($responseData['roomUrl']);
        } else {
            // Handle error
            return response()->json(['error' => 'Failed to create room'], 500);
        }
    }
	
    public function experts()
    {
        return view('experts');
    }
    
    public function expert_details()
    {
        return view('expertdetails');
    }
    
    public function set_estimate_status($id) {
	    $booking = Booking::where('id', $id)->first();
	    
	    if(isset($_POST['contact'])) {
		    $status = 'contact';
		} else {
		    if(isset($_POST['accept'])) {
			    $status = 'accept';
		    } else {
			    $status = 'refuse';
		    }
		}
		
		/* Set booking assets status */
		if(isset($_POST['booking_assets'])) {
			$booking_asset_accepted = array_keys($_POST['booking_assets']);
			$booking_assets = BookingsAsset::where('booking_id', $id)->get();
			
			foreach($booking_assets as $booking_asset) {
				if(in_array($booking_asset->asset_id, $booking_asset_accepted)) {
					$booking_asset->customer_active = 1;
					$booking_asset->booking_active = 1;
				} else {
					$booking_asset->customer_active = 0;
					$booking_asset->booking_active = 0;
				}
				$booking_asset->save();
			}
		}
		
		if($status == 'contact') {
			$booking->status = 80;
		} else {	    
		    if($booking->status == 4) {
			    if($status == 'accept') {
				    $booking->status = 13;
				    $booking->customer_accepted = 1;
				    $booking->customer_refused = 0;
			    } else {
				    $booking->customer_accepted = 0;
				    $booking->customer_refused = 1;
				    $booking->status = 2;
				    
				    if(isset($_POST['refuse_reason'])) {
				    	$booking->customer_refused_reason = $_POST['refuse_reason'];
				    }
			    }
			} else {
			    if($status == 'accept') {
				    $booking->status = 11;
				    $booking->customer_accepted = 1;
				    $booking->customer_refused = 0;
			    } else {
				    $booking->customer_accepted = 0;
				    $booking->customer_refused = 1;
				    $booking->status = 2;
				    
				    if(isset($_POST['refuse_reason'])) {
				    	$booking->customer_refused_reason = $_POST['refuse_reason'];
				    }
			    }
			}
			
			if(isset($_POST['signature']) && $_POST['signature']) {			
			    $booking->customer_signature = $_POST['signature'];
			    
			    if(isset($_POST['extra_info'])) {
			    	$booking->extra_info_json = json_encode($_POST['extra_info']);
			    }
			}
		}
		    
		$booking->last_action_customer = time();
        
        $booking->booking_seen = 0;
	    $booking->save();
	    
	    $asset = Asset::where('id', $booking->asset_id)->first();
	    
	    $customer_settings = CustomerSetting::where('customer_id', $asset->customer_id)->first();
        		
		$url = url()->previous();
	    
	    return redirect($url, 303);
    }
    
	public function set_booking_status($id, $asset_id) {
	    $booking = Booking::where('id', $id)->first();
        
        $booking_asset = BookingsAsset::where('booking_id', $id)->where('asset_id', $asset_id)->first();
	    
	    if(isset($_POST['accept'])) {
		    $status = 'accept';
	    } else {
		    if(isset($_POST['refuse_reason'])) {
		    	$booking_asset->refused_reason = $_POST['refuse_reason'];
		    }
		    
		    $status = 'refuse';
	    }
        
	    if($status == 'accept') {
		    $booking_asset->accepted = 1;
		    $booking_asset->refused = 0;
	    } else {
		    $booking_asset->accepted = 0;
		    $booking_asset->refused = 1;
		    $booking_asset->customer_active = 0;
			$booking_asset->booking_active = 0;
		    
		    //$booking->status = 2;
	    }
	    
	    $booking_asset->save();
	    
	    $asset = Asset::where('id', $asset_id)->first();
	    
	    $booking_assets = BookingsAsset::where('booking_id', $id)->where('booking_active', 1)->where('customer_active', 1)->get();
	    
	    $accepted_total = true;
	    
	    foreach($booking_assets as $booking_asset) {
		    if($booking_asset->refused == 1 || $booking_asset->accepted == 0) {
			    $accepted_total = false;
		    }
	    }
	    
	    if($accepted_total) {
		    $booking->status = 3;
			$booking->accepted = 1;
	    }
	    
		$booking->booking_seen = 0;
	    $booking->save();
        		
		$url = 'https://offer.readmylips.be/booking/'.$id.'/cmVxdWVzdA==/'.$asset_id;
	    
	    return redirect($url, 303);
    }

    
    public function mollie(Request $request) {
	    $asset = Asset::where('id', $request->id)->first();
	    
	    /* Booking create */
	    $booking = new Booking;
		$booking->title = '';
		$booking->calendar_type = 1;
		$booking->calendar_id = null;
		$booking->mollie_feedback = '';
		
		$booking->last_action_customer = time();
		
		if ($request->date_unknown) {
		    $request->date = '';
		} else {
		    $request->date = date('d-m-Y', strtotime($request->date));
		}
	    
	    if($request->tags) {
			$booking->tags = $request->tags['id'];
	    } else {
		    $booking->tags = '';
	    }
	    
	    if($request->source) {
			$booking->source = $request->source['id'];
	    } else {
		    $booking->source = '';
	    }
		
		$asset_ids = array();
		
		if (is_array($request->id)) {
		    foreach($request->id as $asset_details) {
			    $asset_ids[] = $asset_details['id'];
		    }
		} else {
		    $asset_ids[] = $request->id;
		}

		$booking->asset_id = $asset_ids[0];
		$booking->customer_id = $asset->customer_id;
		$booking->category_id = 1;
		$booking->booking_type = 1;
		$booking->price = $asset->price;
		$booking->paid = 0;
		$booking->email = $request->email;
		$booking->mobile = $request->mobile;
		$booking->invoice_company = (isset($request->invoice_company))?$request->invoice_company:$request->invoice_name;
		$booking->invoice_name = $request->invoice_name;
		$booking->invoice_address = $request->invoice_address;
		$booking->invoice_postal = $request->invoice_postal;
		$booking->invoice_city = $request->invoice_city;
		$booking->invoice_email = $request->email;
		$booking->invoice_vat = $request->invoice_vat;
		$booking->amount_of_visitors = ($request->amount_of_visitors)?$request->amount_of_visitors:0;
		$booking->date = $request->date;
		$booking->date_hour_start = ($request->date_start)?$request->date_start:'09:00';
		$booking->date_hour_end = ($request->date_end)?$request->date_end:'20:00';
		$booking->date_2 = $request->date_2;
		$booking->date_2_hour_start = $request->date_2_start;
		$booking->date_2_hour_end = $request->date_2_end;
		$booking->json = json_encode($request->all());
		$booking->booking_flow = $request->booking_flow;
		$booking->location = $request->location;
		$booking->keynote_subject = (isset($request->keynote_subject['keynote']))?$request->keynote_subject['keynote']:'';
		$booking->remark = preg_replace('/[^\p{L}\p{N}\p{P}\p{Zs}]+/u', '', $request->remark);
		$booking->date_unknown = ($request->date_unknown)?$request->date_unknown:0;
		$booking->accepted = 1;
		$booking->finished = 1;
		$booking->status = 1;
		$booking->subscribe_newsletter = ($request->subscribe_newsletter)?1:0;
		$booking->language = $request->language;
        $booking->booking_seen = 0;
		
		$booking->save();
		
		foreach($asset_ids as $asset_details) {
			$booking_asset = new BookingsAsset;
			$booking_asset->booking_id = $booking->id;
			$booking_asset->asset_id = $asset_details;
			$booking_asset->save();
		}
		
		$customer_settings = CustomerSetting::where('customer_id', $asset->customer_id)->first();
		
		/* Calculate price */
		$discount = 0;
		
		if(isset($request->voucher) && $request->voucher) {
			$vouchercode = strtoupper($request->voucher);
			
			$voucher = Voucher::where('code', $vouchercode)->first();
			
			if($voucher) {
				$discount = $voucher->value;
			}
		}
		
		$price = $asset->price;
		
		/* Changes for booking for 2 */
		if($booking->booking_flow == 1) {
			if($request->notfound) {
				$booking->finished = 1;
				$booking->accepted = 1;
				$booking->status = 6;
			} else {
				$booking->finished = 0;
				$booking->accepted = 0;
			}
			
			$booking->booking_seen = 0;
			$booking->save();
			
			if($request->notfound) {
				self::book_event($booking->id);
			}
			
			/* Add extra cost */		
			$booking->price = $price/100;
			
			$booking->save();
			
			$success_url = url('/expert/success/'.$booking->id);
			
			return response()->json(['mollie_url' => $success_url], 200);
		} else {
			$success_url = url('/expert/success/'.$booking->id);
			
			/* Add extra cost */
			if($customer_settings->markup_type == 1) {
			    $customer_settings->markup = $customer_settings->markup*100;
			    $price = $price+$customer_settings->markup;
		    } else {
			    $price = $price * (1+($customer_settings->markup/100));
		    }
			
			$price = ($price-$discount)/100;
			
			$booking->price = $price;
			
			$booking->save();
		    
		    if($asset->price == 0 || $asset->price_hidden == 1) {
				return response()->json(['mollie_url' => $success_url], 200);
		    } else {			    
			    if($price > 0) {
				    $price = number_format((float)$price, 2, '.', '');
				    
				    $mollie = new MollieApiClient();
				    
				    if($asset->customer_id != 1 && $asset->customer_id != 2) {
					    $mollie->setApiKey('test_Fgau5Rz8PNBDs7Q5K4jmnu2PEpK2yQ');
				    } else {
					    $mollie->setApiKey('live_6JwGFMdRUQ6278prjQhDtSCWpDQJCT');
				    }
				    
				    $payment = $mollie->payments->create([
				        "amount" => [
				            "currency" => "EUR",
				            "value" => $price // You must send the correct number of decimals, thus we enforce the use of strings
				        ],
				        "description" => "Order #".time(),
				        "redirectUrl" => $success_url,
				        "webhookUrl" => $success_url,
				        "metadata" => [
				            "order_id" => time(),
				        ],
				    ]);
				
				    // redirect customer to Mollie checkout page
					return response()->json(['mollie_url' => $payment->getCheckoutUrl()], 200);
				} else {
					return response()->json(['mollie_url' => $success_url], 200);
				}
			}
		}
    }
    
    public function success(Request $request, $booking_id) {
	    $booking = Booking::where('id', $booking_id)->first();
	    
	    if($booking->booking_flow == 2) {
		    $asset = Asset::where('id', $booking->asset_id)->first();
	    	
	    	$booking->customer_id = $asset->customer_id;
	    	$booking->status = 3;
			$booking->accepted = 1;
			$booking->booking_seen = 0;
	    	$booking->save();
	    }
	    
	    $customer_settings = CustomerSetting::where('customer_id', $booking->customer_id)->first();
		$mailsettings = MailSettings::where('customer_id', $booking->customer_id)->first();
	    
	    if($booking->paid == 0) {
		    if($booking->booking_flow == 1) {
			    $asset = Asset::where('id', $booking->asset_id)->first();
			    
			    $to = 'contact@readmylips.be';
			    
		    	$subject = 'Nieuwe boeking ReadMyLips Ref: #'.$booking->id.' Datum: '.date('d-m-Y', strtotime($booking->date)).' Gegevens klant: '.$booking->invoice_name;
		    	$mailtext = 'Er werd een nieuwe boeking gemaakt, je kan deze in het platform terugvinden:<br><br> [{extra_details}]';
		    	
		    	// Define the string to substitute for [{extra_details}]
				$substitute_extra_details = '<p>Locatie aanvraag: '.$booking->location.'</p>';
				$substitute_extra_details = '<p>Voertaal event: '.$booking->language.'</p>';
		        $substitute_extra_details .= '<p>Aantal bezoekers: '.$booking->amount_of_visitors.'</p>';
		        $substitute_extra_details .= '<p>Beschrijving aanvraag: '.$booking->remark.'</p>';
		        $substitute_extra_details .= '<p>Datum aanvraag: '.date('d-m-Y', strtotime($booking->date)).'</p>';
		        $substitute_extra_details .= '<p>Aanvraag door: '.$booking->invoice_name.'</p>';
		        $substitute_extra_details .= '<p>Adres gegevens: '.$booking->invoice_address.', '.$booking->invoice_postal.' '.$booking->invoice_city.'</p>';
		        $substitute_extra_details .= '<p>Email: '.$booking->invoice_email.'</p>';
		        $substitute_extra_details .= '<p>GSM Nummer: '.$booking->mobile.'</p>';
				
				// Check if [{offer_link}] exists in $mailtext
				if (strpos($mailtext, '[{extra_details}]') !== false) {
				    // Substitute [{extra_details}] with the actual text
				    $mailtext = str_replace('[{extra_details}]', $substitute_extra_details, $mailtext);
				} else {
				    // Append the actual link at the end if [{extra_details}] is not found
				    $mailtext .= $substitute_extra_details;
				}
		    	
		    	$mail_text = '<div style="text-align: left;">'.$mailtext.'</div>';
	        
		        $htmlMessage = view('mail', compact('mail_text', 'customer_settings'))->render();
			    
				$fromName = $customer_settings->application_name;
				$name = strtolower($fromName);
				$name = preg_replace('/[^a-z0-9]/', '', $name);
				//$fromEmail = $name . '@mlbx.email';
				$fromEmail = 'contact@readmylips.be';
		
				Mail::send([], [], function ($message) use ($to, $subject, $htmlMessage, $fromEmail, $fromName) {
				    $message->to($to)
				            ->subject($subject)
				            ->from($fromEmail, $fromName)
				            ->html($htmlMessage, 'text/html');
				});
			}
			
			/* Create a new whereby room */
			if($customer_settings->booking_flow == 2) {
				$api_key = env('WHEREBY_KEY');
				
			    $headers = [
		            'Authorization: Bearer '.$api_key,
		            'Content-Type: application/json',
		        ];
		
		        $data = [
		            'startDate' => date('c'),
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
		        
		        $start_time = $booking->date.' '.$booking->date_hour_start;
				$end_time = $booking->date.' '.$booking->date_hour_end;
		        
		        $meeting = new Meeting;
			    $meeting->fill([
				    'booking_id' => $booking->id,
				    'customer_id' => $asset->customer_id,
				    'title' => 'Meeting '.$booking->invoice_name,
				    'start_date' => strtotime($start_time),
				    'end_date' => strtotime($end_time),
				    'user_id' => NULL,
				    'meetingId' => $responseData['meetingId'],
				    'roomName' => $responseData['roomName'],
				    'roomUrl' => $responseData['roomUrl'],
				    'hostRoomUrl' => $responseData['hostRoomUrl'],
				    'viewerRoomUrl' => $responseData['viewerRoomUrl']
			    ]);
			    
			    $meeting->save();
			    
			    $to = $asset->email;
			    $reply_to = $booking->email;
			    
			    $customer_settings = CustomerSetting::where('customer_id', $booking->customer_id)->first();
			    
			    if($mailsettings->mail_booking_confirm_text_nl) {
				    $subject = $mailsettings->mail_booking_confirm_subject_nl.' Ref: #'.$booking->id;
			    	$mailtext = nl2br($mailsettings->mail_booking_confirm_text_nl);
			    	
			    	// Define the string to substitute for [{extra_details}]
					$substitute_extra_details = '<p>Datum: '.$booking->date.' om '.$booking->date_hour_start.'</p>';
			        $substitute_extra_details .= '<p>Beschrijving aanvraag: '.$booking->remark.'</p>';
			        $substitute_extra_details .= '<p>Aanvraag door: '.$booking->invoice_name.'</p>';
			        $substitute_extra_details .= '<p>Adres gegevens: '.$booking->invoice_address.', '.$booking->invoice_postal.' '.$booking->invoice_city.'</p>';
			        $substitute_extra_details .= '<p>Email: '.$booking->invoice_email.'</p>';
			        $substitute_extra_details .= '<p>GSM Nummer: '.$booking->mobile.'</p>';
					
					// Check if [{offer_link}] exists in $mailtext
					if (strpos($mailtext, '[{extra_details}]') !== false) {
					    // Substitute [{extra_details}] with the actual text
					    $mailtext = str_replace('[{extra_details}]', $substitute_extra_details, $mailtext);
					} else {
					    // Append the actual link at the end if [{extra_details}] is not found
					    $mailtext .= $substitute_extra_details;
					}
			    	
			    	// Define the string to substitute for [{extra_details}]
					$substitute_extra_details = '<div style="text-align: left;">Om naar de virtuele meeting te gaan klik deze link:<br><a href="'.$responseData['roomUrl'].'" target="_blank">Naar de meeting room</a>.</div>';
					$substitute_extra_details .= '<br>';				
			        $substitute_extra_details  .= '<div style="text-align: left;">Kan je niet op de link klikken? Kopieer hem hier:<br><strong>'.$responseData['roomUrl'].'</strong></div>';
					
					// Check if [{offer_link}] exists in $mailtext
					if (strpos($mailtext, '[{meeting_link}]') !== false) {
					    // Substitute [{extra_details}] with the actual text
					    $mailtext = str_replace('[{meeting_link}]', $substitute_extra_details, $mailtext);
					} else {
					    // Append the actual link at the end if [{extra_details}] is not found
					    $mailtext .= $substitute_extra_details;
					}
					
					$mail_text = $mailtext;
					$mail_text .= '<br>';				
					$mail_text .= '<p>Happy meeting!</p>';
					$mail_text .= '<p>'.$customer_settings->application_name.'</p>';
		        
					$htmlMessage = view('mail', compact('mail_text', 'customer_settings'))->render();
					
					$date = $booking->date;
				    $start_time = $date.' '.$booking->date_hour_start;
				    $end_time = $date.' '.$booking->date_hour_end;
					
					// Generate iCal content
					$title = ($booking->keynote_subject)?$booking->keynote_subject:$booking->invoice_name.' meets '.$asset->title;
					
					$icalContent = Calendar::create($title)
					    ->event(
					        Event::create($title)
					            ->startsAt(new DateTime($start_time))
					            ->endsAt(new DateTime($end_time))
					            ->organizer($asset->email, $asset->title)
					            ->url($responseData['roomUrl'])
								->attendee($asset->email, $asset->title)
								->attendee($booking->email, $booking->invoice_name)
					            ->description('Om naar de meeting te gaan kan je deze link gebruiken: ' . $responseData['roomUrl'])
								->address($responseData['roomUrl'])
					    )
					    ->get();
					
					// Save iCal content to a file
					$fileName = 'event.ics'; // Choose a file name
					Storage::put($fileName, $icalContent);
					
					$fromName = $customer_settings->application_name;
					$name = strtolower($fromName);
					$name = preg_replace('/[^a-z0-9]/', '', $name);
					//$fromEmail = $name . '@mlbx.email';
					$fromEmail = 'contact@readmylips.be';
					$to = $asset->email;
					
					// Send email with attachment
					Mail::mailer('rml_smtp')->send([], [], function ($message) use ($to, $reply_to, $subject, $htmlMessage, $fileName, $fromEmail, $fromName) {
					    $message->to($to)
				    			->cc($reply_to)
				    			->replyTo($reply_to)
					            ->subject($subject)
					            ->from($fromEmail, $fromName)
					            ->html($htmlMessage, 'text/html') // Set the content type to HTML
					            ->attach(Storage::path($fileName), ['as' => 'event.ics', 'mime' => 'text/calendar']);
					});
					
					// Delete the iCal file after sending the email if needed
					Storage::delete($fileName);
				}
				
				/* Auto book in calendar */
			    self::book_event($booking->id);
			    
			    /* Create invoice */
			    if($booking->customer_id == 2) {
			        $teamleaderController = new TeamleaderController();
			        $teamleaderController->createInvoice($booking->id);
		        }
		    }
		}
		    
	    $booking->paid = 1;
	    $booking->save();
	    
        return redirect(env('VUE_URL').'/success/'.$booking_id.'?customer='.$booking->customer_id.'&lang='.$booking->language, 303);
    }
    
    public function microsoft_auth() {
		return Socialite::driver('msgraph')->redirect();
    }
    
    public function google_unlink() {
	    if($_GET['uuid']) {
	    	Session::put('uuid', $_GET['uuid']);
	    }
	    
	    $uuid = $_GET['uuid'];
	    
		$currentUser = User::find($uuid);
		
		$settings = Setting::where('user_id', $currentUser->id)->first();
		
		$settings->token = '';
		$settings->refresh_token = '';
		$settings->save();
		
		return redirect(env('VUE_URL').'/settings', 303);
    }
    
    public function google_auth() {
	    if($_GET['uuid']) {
	    	Session::put('uuid', $_GET['uuid']);
	    }

		return Socialite::driver('google')->with(["access_type" => "offline", "prompt" => "consent select_account"])->scopes(['https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/calendar.events.freebusy', 'https://www.googleapis.com/auth/calendar.events', 'https://www.googleapis.com/auth/calendar.events.owned'])->redirect();
    }
    
    public function google_callback() {
		$currentUser = User::find(Session::get('uuid'));
		
		$user = Socialite::driver('google')->user();
		
		$settings = Setting::where('user_id', $currentUser->id)->first();

	    if(!$settings) {
	        // If settings don't exist, create a new record
	        $settings = Setting::create([
	            'user_id' => $currentUser->id,
	            'token' => $user->token,
	            'refresh_token' => $user->refreshToken,
	            'agenda_provider' => 'google',
	            'active' => 1
	        ]);
	    } else {
	        // If settings exist, update the fields as needed
	        $settings->update([
	            'token' => $user->token,
	            'refresh_token' => $user->refreshToken,
	            'agenda_provider' => 'google',
	            'active' => 1
	        ]);
	    }
	    
	    return redirect(env('VUE_URL').'/settings', 303);
    }
    
    public function google_refresh($uuid) {
		$currentUser = User::find($uuid);
		
		if($currentUser && isset($currentUser->id)) {
			$settings = Setting::where('user_id', $currentUser->id)->first();
			
			if($settings && $settings->refresh_token) { 
			    $client = new GoogleClient();
			    $client->setClientId('357979522926-8c0dqiurqm89c98i4noif4c11as94v4j.apps.googleusercontent.com');
			    $client->setClientSecret('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');
			    $client->setAccessType('offline');
			
			    if ($client->isAccessTokenExpired()) {
			        $client->fetchAccessTokenWithRefreshToken($settings->refresh_token);
			    }
			
			    // Get the refreshed access token
			    $accessToken = $client->getAccessToken();
			    
			    if(isset($accessToken['access_token'])) {
				    $settings->update([
			            'token' => $accessToken['access_token'],
			            'agenda_provider' => 'google',
			            'active' => 1
			        ]);
		        
					return $accessToken['access_token'];
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
    }
    
    public function google_events($date, $timespace = 60) {
	    if(isset($_GET['iframe'])) {
			$asset = Asset::where('drupal_id', $_GET['asset_id'])->first();
			$_GET['asset_id'] = $asset->id;
	    }
		
		$asset = Asset::where('id', $_GET['asset_id'])->first();
		    
	    $settings = CustomerSetting::where('customer_id', $asset->customer_id)->first();
	    
	    $day = strtolower(date('l', strtotime($date)));
	    $day_start = $day.'_hour_start';
	    $day_end = $day.'_hour_end';
	    $date = date('Y-m-d', strtotime($date));
	    date_default_timezone_set('Europe/Brussels');
	    
	    $available = true;
	    $start_hour = '09:00';
	    $end_hour = '17:00';
	    
	    if($_GET['asset_id']) {
		    $asset = Asset::where('id', $_GET['asset_id'])->first();
		    
		    if(isset($asset->user_id)) {
		    	$settings_agenda = SettingsAgenda::where('user_id', $asset->user_id)->first();
		    
			    if($settings_agenda) {		    
				   	$available = $settings_agenda->$day;
				   	$start_hour = $settings_agenda->$day_start;
				   	$end_hour = $settings_agenda->$day_end;
				   	$timespace = $settings_agenda->timespace;
			    }
			}
		}
					    
	    if($settings->change_timespace == 0) {
		    $timespace = $settings->default_timespace;
	    }
	    
	    $event_array = array();
	    $events = array();
	    
	    if(strtotime($date) > time() && $available) {
		    if($settings->booking_flow == 2) {
		        // Desired day and time range
				$desired_day = date('d-m-Y', strtotime($date));
				$start_time = strtotime($desired_day . " " . $start_hour);
				$end_time = strtotime($desired_day . " " . $end_hour);
						    
			    $googleToken = self::google_refresh($asset->user_id);
			    
				$events = array();
				
			    if($googleToken) {
				    $client = new GoogleClient();
				    $client->setClientId('357979522926-8c0dqiurqm89c98i4noif4c11as94v4j.apps.googleusercontent.com');
				    $client->setClientSecret('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');
				    $client->setAccessType('offline');
					
					// Authorize using OAuth 2.0 (you may need to implement your own OAuth flow)
					$client->setAccessToken($googleToken);
			
			        $service = new \Google\Service\Calendar($client);
					
			        // $calendarId = 'primary';
			        $optParams = array(
			            'timeMin' => $date . 'T00:00:00Z',
					    'timeMax' => $date . 'T23:59:59Z',
			        );
			
			        $event_array = $service->events->listEvents('primary', $optParams);
				
			        foreach ($event_array->getItems() as $event) {
				        if(date('dmY', strtotime($event->start->dateTime)) == date('dmY', strtotime($date))) {
			            $events[] = array(
				            'start' => date('d-m-Y H:i', strtotime($event->start->dateTime)),
				            'start_ms' => strtotime($event->start->dateTime),
				            'start_date' => date('d-m-Y', strtotime($event->start->dateTime)),
				            'start_hour' => date('H:i', strtotime($event->start->dateTime)),
				            'end' => date('d-m-Y H:i', strtotime($event->end->dateTime)),
				            'end_ms' => strtotime($event->end->dateTime),
				            'end_date' => date('d-m-Y', strtotime($event->start->dateTime)),
				            'end_hour' => date('H:i', strtotime($event->start->dateTime)),
			            );
			            }
			        }   
			    } 
	        
		        // Desired day and time range
				$desired_day = date('d-m-Y', strtotime($date));
				$start_time = strtotime($desired_day . " " . $start_hour);
				$end_time = strtotime($desired_day . " " . $end_hour);
		    } else {
			    $events = array();
			    $timespace = 60*12;
			    
		        // Desired day and time range
				$desired_day = date('d-m-Y', strtotime($date));
				$start_time = strtotime($desired_day . " 08:00");
				$end_time = strtotime($desired_day . " 20:00");
		    }
			
			// Initialize an array to store available slots
			$available_slots = [];
			
			// Sort the events by their start time
			usort($events, function ($a, $b) {
			    return $a['start_ms'] - $b['start_ms'];
			});
			
			$last_event_end = $start_time;
			
			// Iterate through events
/*
			foreach ($events as $event) {
			    $event_start_time = strtotime($event["start"]);
			    $event_end_time = strtotime($event["end"]);
			
			    // Check if there's a gap between the last event and the current event
			    if ($event_start_time > $last_event_end && $last_event_end < $end_time) {
			        $available_slots[] = [
			            "start" => date("H:i", $last_event_end),
			            "end" => date("H:i", $event_start_time),
			        ];
			    }
			
			    // Update the last event's end time
			    $last_event_end = max($last_event_end, $event_end_time);
			}
*/
			
			// Check if there's a gap between the last event and the end time
			if ($last_event_end < $end_time) {
			    $available_slots[] = [
			        "start" => date("H:i", $last_event_end),
			        "end" => date("H:i", $end_time),
			    ];
			}
			
			// Create 30-minute slots from available slots
			$slots = [];
			foreach ($available_slots as $slot) {
			    $slot_start = strtotime($slot["start"]);
			    $slot_end = strtotime($slot["end"]);
			
			    while ($slot_start < $slot_end) {
			        $slot_end_time = strtotime("+".$timespace." minutes", $slot_start);
			
			        // Make sure the slot doesn't extend beyond the available slot
			        if ($slot_end_time <= $slot_end && $slot_start < $end_time) {
			            $slots[] = [
			                "start" => date("H:i", $slot_start),
			                "end" => date("H:i", $slot_end_time),
			            ];
			
			            // Move to the next 30-minute slot
			            $slot_start = $slot_end_time;
			        } else {
			            break; // Break if the remaining time is less than 30 minutes
			        }
			    }
			}
			
			/* Remove slots if they are found inside the events array */
			foreach($slots as $key => $slot) {
			    $slot_start_time = strtotime(date('d-m-Y', strtotime($date)) . ' ' . $slot['start']);
			    $slot_end_time = strtotime(date('d-m-Y', strtotime($date)) . ' ' . $slot['end']);
			    
			    foreach($events as $event) {
			        $event_start_time = $event['start_ms'];
			        $event_end_time = $event['end_ms'];
			
			        // Check for any overlap between the slot and the event
			        if (($slot_start_time < $event_end_time && $slot_end_time > $event_start_time)) {
			            unset($slots[$key]);
			        }
			    }
			}
			
			sort($slots);
		} else {
			$slots = array();
		}
        
        return response()->json(['slots' => $slots, 'events' => $events], 200);
    }
    
	public function book_event($id, $afspraak_type = 1) {
	    $booking = Booking::where('id', $id)->first();
	    $asset = Asset::where('id', $booking->asset_id)->first();
	    
	    $date = $booking->date;
	    $start_time = $booking->date_hour_start;
	    $end_time = $booking->date_hour_end;
	    
	    $googleToken = self::google_refresh($asset->user_id);
	    
	    if($googleToken) {
			$pre = ($booking->keynote_subject) ? $booking->keynote_subject : $booking->invoice_name . ' meets ' . $asset->title;

			$date = date('Y-m-d', strtotime($date));
			
			$eventData = [
			    'anyoneCanAddSelf' => true,
			    'summary' => $pre . ' - ' . $booking->location,
			    'start' => [
			        'dateTime' => $date . 'T' . $start_time . ':00',
			        'timeZone' => 'Europe/Brussels',
			    ],
			    'end' => [
			        'dateTime' => $date . 'T' . $end_time . ':00',
			        'timeZone' => 'Europe/Brussels',
			    ],
			    'attendees' => [
			        ['email' => $booking->email],
			        ['email' => $asset->email],
			    ],
			];
			
			// Fetch the meeting room URL from the database
			$meeting = Meeting::where('booking_id', $booking->id)->first();
			
			if ($meeting) {
			    $meetingUrl = $meeting->roomUrl;
			    
			    // Add the meeting URL to the event description
			    $eventData['description'] = "Join the meeting here: " . $meetingUrl;
			}

			
			$apiUrl = 'https://www.googleapis.com/calendar/v3/calendars/primary/events?conferenceDataVersion=1';
			
			$ch = curl_init($apiUrl);
			
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
			    'Authorization: Bearer ' . $googleToken,
			    'Content-Type: application/json',
			]);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($eventData));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			$response = curl_exec($ch);
			
			if (curl_errno($ch)) {
			    echo 'Error: ' . curl_error($ch);
			}
			
			curl_close($ch);
			
			// Handle the API response
			$jsonResponse = json_decode($response, true);
			
			if (isset($jsonResponse['id'])) {
			    //echo 'Event created successfully! Event ID: ' . $jsonResponse['id'];
			} else {
			    echo 'Error creating event: ' . print_r($jsonResponse, true);
			}
			
			if(isset($jsonResponse['id'])) {
				$booking->calendar_id = $jsonResponse['id'];
			}
		}
		
		$booking->save();
    }
}
