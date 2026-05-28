<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Session;

use DB;

use App\Models\Asset;
use App\Models\Booking;
use App\Models\BookingsAsset;
use App\Models\CustomerSetting;

class TeamleaderController extends Controller
{
    public function redirectToTeamleader() {
	    if($_GET['customer_id']) {
		    Session::put('customer_id', $_GET['customer_id']);
	
	        $query = http_build_query([
		        'client_id' => env('TEAMLEADER_CLIENT_ID'),
		        'redirect_uri' => env('TEAMLEADER_REDIRECT_URI'),
		        'response_type' => 'code',
		        'scope' => 'invoices contacts companies deals quotations', // Add required scopes
		    ]);
	
	        return redirect(env('TEAMLEADER_AUTH_URL') . '?' . $query);
        }
    }

    public function handleCallback(Request $request) {
        $code = $request->get('code');

        $response = $this->getAccessToken($code);

        if (isset($response['access_token'])) {
            // Store the access token in your database or session
            $accessToken = $response['access_token'];
            $refreshToken = $response['refresh_token'];

            // Now you can make API requests using the access token
            return response()->json(['access_token' => $accessToken, 'refreshToken' => $refreshToken]);
        } else {
            return response()->json(['error' => 'Failed to obtain access token']);
        }
    }
    
    public function getAccessToken($authCode) {
	    $curl = curl_init();
	
	    curl_setopt_array($curl, [
	        CURLOPT_URL => 'https://app.teamleader.eu/oauth2/access_token',
	        CURLOPT_RETURNTRANSFER => true,
	        CURLOPT_POST => true,
	        CURLOPT_POSTFIELDS => http_build_query([
	            'grant_type' => 'authorization_code',
	            'code' => $authCode,
	            'client_id' => env('TEAMLEADER_CLIENT_ID'),
	            'client_secret' => env('TEAMLEADER_CLIENT_SECRET'),
	            'redirect_uri' => env('TEAMLEADER_REDIRECT_URI'),
	        ]),
	        CURLOPT_HTTPHEADER => [
	            'Content-Type: application/x-www-form-urlencoded',
	        ],
	    ]);
	
	    $response = curl_exec($curl);
	    $err = curl_error($curl);
	
	    curl_close($curl);
	
	    if ($err) {
	        return response()->json(['error' => $err]);
	    } else {
	        $data = json_decode($response, true);
	
			$customer_id = Session::get('customer_id');

			if($customer_id) {
		        if (isset($data['access_token']) && isset($data['refresh_token'])) {
		            DB::table('customers')
		                ->where('id', $customer_id) // Assuming you always store in the first row
		                ->update([
		                    'teamleader_access_token' => $data['access_token'],
		                    'teamleader_refresh_token' => $data['refresh_token'],
		                ]);
		        }
		    }
	
	        return $data ?? null;
	    }
	}
	
	public static function refreshToken($customer_id) {
	    // Retrieve the current refresh token from the database
	    $refreshToken = DB::table('customers')
	        ->where('id', $customer_id)
	        ->value('teamleader_refresh_token');
	
	    if (!$refreshToken) {
	        return response()->json(['error' => 'No refresh token found.']);
	    }
	
	    $curl = curl_init();
	
	    curl_setopt_array($curl, [
	        CURLOPT_URL => 'https://app.teamleader.eu/oauth2/access_token',
	        CURLOPT_RETURNTRANSFER => true,
	        CURLOPT_POST => true,
	        CURLOPT_POSTFIELDS => http_build_query([
	            'grant_type' => 'refresh_token',
	            'refresh_token' => $refreshToken,
	            'client_id' => env('TEAMLEADER_CLIENT_ID'),
	            'client_secret' => env('TEAMLEADER_CLIENT_SECRET'),
	        ]),
	        CURLOPT_HTTPHEADER => [
	            'Content-Type: application/x-www-form-urlencoded',
	        ],
	    ]);
	
	    $response = curl_exec($curl);
	    $err = curl_error($curl);
	
	    curl_close($curl);
	
	    if ($err) {
	        return response()->json(['error' => $err]);
	    } else {
	        $data = json_decode($response, true);
	
	        if (isset($data['access_token'])) {
	            // Update the new access token and possibly the refresh token in the database
	            DB::table('customers')
	                ->where('id', $customer_id) // Assuming you always store in the first row
	                ->update([
	                    'teamleader_access_token' => $data['access_token'],
	                    'teamleader_refresh_token' => $data['refresh_token'], // Update if a new refresh token is provided
	                ]);
	
				return $data;
	        } else {
	            return response()->json(['error' => 'Failed to refresh access token']);
	        }
	    }
	}
	
	public function splitAddress($fullAddress) {
	    // First, check if there's a comma
	    if (strpos($fullAddress, ',') !== false) {
	        // Split by comma to separate the street/number from the postal code/city
	        $parts = explode(',', $fullAddress);
	        $streetAndNumber = trim($parts[0]);
	        $postalAndCity = trim($parts[1]);
	    } else {
	        // No comma, try to separate based on common postal code format
	        // Assume the last part is postal code and city if there are 2 or more words at the end
	        preg_match('/^(.+)\s+(\d{4,})\s+(.+)$/', $fullAddress, $matches);
	        if (count($matches) === 4) {
	            $streetAndNumber = trim($matches[1]);
	            $postalAndCity = $matches[2] . ' ' . $matches[3];
	        } else {
	            return ['street' => '', 'number' => '', 'postal_code' => '', 'city' => ''];
	        }
	    }
	
	    // Split street and number by extracting the last part as the number with optional suffix
	    preg_match('/^(.*?)(\d{1,4}(?:\s*[A-Za-z]+)?(?:\s+Bus\s+\d+)?)$/i', $streetAndNumber, $streetMatches);
	    
	    if (count($streetMatches) !== 3) {
	        return ['street' => '', 'number' => '', 'postal_code' => '', 'city' => ''];
	    }
	
	    $street = trim($streetMatches[1]);
	    $number = trim($streetMatches[2]);
	
	    // Split postal code and city
	    preg_match('/^(\d{4})\s+(.+)$/', $postalAndCity, $postalMatches);
	
	    if (count($postalMatches) !== 3) {
	        return ['street' => '', 'number' => '', 'postal_code' => '', 'city' => ''];
	    }
	
	    $postalCode = $postalMatches[1];
	    $city = $postalMatches[2];
	
	    return [
	        'street' => $street,
	        'number' => $number,
	        'postal_code' => $postalCode,
	        'city' => $city
	    ];
	}
	
	public function splitName($fullName) {
	    // Trim the name to remove unnecessary spaces
	    $fullName = trim($fullName);
	    
	    // Split the name by spaces into an array
	    $nameParts = explode(' ', $fullName);
	    
	    // If only one word is given, treat it as the first name and leave the last name empty
	    if (count($nameParts) === 1) {
	        return [
	            'first_name' => $nameParts[0],
	            'last_name' => '-'
	        ];
	    }
	    
	    // First name is the first word
	    $firstName = array_shift($nameParts);
	    
	    // Last name is the remaining part (implode to handle multiple words in the last name)
	    $lastName = implode(' ', $nameParts);
	    
	    return [
	        'first_name' => $firstName,
	        'last_name' => $lastName
	    ];
	}
	
	public function createInvoice($booking_id) {
		$booking = Booking::where('id', $booking_id)->first();
		
		if($booking && $booking->extra_info_json != null && $booking->customer_id == 1 || $booking && $booking->customer_id != 1) {
			$data = self::refreshToken($booking->customer_id);
			
			if($data['access_token']) {
				$accessToken = $data['access_token'];
				$apiUrl = 'https://api.teamleader.eu/';
				
/*
				// Define parameters to filter for deal custom fields
				$data = [
				    "object_type" => "deal",  // Specify "deal" to get custom fields for deals
				    "page" => [
				        "size" => 50,  // Adjust if you want to retrieve more or fewer fields at once
				        "number" => 1
				    ]
				];
				
				// Initialize cURL
				$ch = curl_init($apiUrl.'customFieldDefinitions.list');
				
				// Set options for cURL
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, [
				    "Authorization: Bearer $accessToken",
				    "Content-Type: application/json"
				]);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
				
				// Execute the request
				$response = curl_exec($ch);
				
				// Check for errors
				if (curl_errno($ch)) {
				    echo 'Error:' . curl_error($ch);
				} else {
				    echo 'Response:' . $response;
				}
				
				// Close cURL session
				curl_close($ch);
*/
				
				if($booking->customer_id != 1) {			
					$invoice_details = array(
						'vat_number' => $booking->invoice_vat,
						'email' => $booking->invoice_email,
						'payment_time_end_month' => 0
					);
					
					$invoice_details['street'] = $booking->invoice_address;
					$invoice_details['number'] = '';
					$invoice_details['postal_code'] = $booking->invoice_postal;
					$invoice_details['city'] = $booking->invoice_city;
					$invoice_details['name'] = $booking->invoice_name;
					$invoice_details['company'] = $booking->invoice_company;
					$invoice_details['mobile'] = '';
				} else {
					$extra_info_json = (array)json_decode($booking->extra_info_json);
					
					$invoice_details = array(
						'vat_number' => $extra_info_json['vat_number'],
						'email' => $extra_info_json['invoice_email'],
						'po_ref_number' => $extra_info_json['po_ref_number'],
						'payment_time_end_month' => $extra_info_json['payment_time_end_month'],
						'address' => $extra_info_json['invoicing_address']
					);
					
					$address = self::splitAddress($invoice_details['address']);
					
					$invoice_details['street'] = $address['street'];
					$invoice_details['number'] = $address['number'];
					$invoice_details['postal_code'] = $address['postal_code'];
					$invoice_details['city'] = $address['city'];
					$invoice_details['name'] = $booking->invoice_name;
					$invoice_details['company'] = $booking->invoice_company;
					$invoice_details['mobile'] = $booking->mobile;
				}
				
				// Step 1: Search for the company
				$companyName = $invoice_details['company'];
				$companyVatNumber = $invoice_details['vat_number'];  // VAT number
				
				// Remove everything that is not a digit
				$digits = preg_replace('/\D/', '', $companyVatNumber);
				
				// Belgian VAT numbers should be 10 digits (often start with 0)
				if (strlen($digits) === 9) {
				    $digits = '0' . $digits;
				}
				
				if (strlen($digits) !== 10) {
				    return false; // Invalid VAT number
				}
				
				// Format to BE 0xxx.xxx.xxx
				$companyVatNumber = 'BE ' . substr($digits, 0, 4) . '.' . substr($digits, 4, 3) . '.' . substr($digits, 7, 3);
				
				$searchCompanyData = [
				    'filter' => [
				        'vat_number' => $companyVatNumber
				    ]
				];
				
				$curl = curl_init();
				$headers = [
				    'Authorization: Bearer ' . $accessToken,
				    'Content-Type: application/json',
				];
				
				curl_setopt($curl, CURLOPT_URL, $apiUrl . 'companies.list');
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($searchCompanyData));
				
				$companyResponse = curl_exec($curl);
				$companyResponseData = json_decode($companyResponse, true);
				curl_close($curl);
				
				if(isset($companyResponseData['data'][0]['id'])) {
					$companyId = $companyResponseData['data'][0]['id'];
					
					$createCompanyData = [
						'id' => $companyId,
				        'name' => $companyName,
				        'emails' => [
				            ['type' => 'primary', 'email' => $invoice_details['email']]
				        ],
				        'addresses' => [
				            [
				                'type' => 'primary',
				                'address' => [
				                    'street' => $invoice_details['street'],
				                    'number' => $invoice_details['number'],
				                    'postal_code' => $invoice_details['postal_code'],
				                    'city' => $invoice_details['city'],
				                    'country' => 'BE'
				                ]
				            ]
				        ],
				        'marketing_mails_consent' => ($booking->subscribe_newsletter)?true:false
				    ];
				    
				    if($booking->tags) {
					    $createCompanyData['tags'] = [ $booking->tags ];
				    }
				    
				    if($invoice_details['mobile'] == '') {
					    unset($createCompanyData['telephones']);
				    }
				    
				    $curl = curl_init();
				    curl_setopt($curl, CURLOPT_URL, $apiUrl . 'companies.update');
				    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
				    curl_setopt($curl, CURLOPT_POST, true);
				    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($createCompanyData));
				
				    $createCompanyResponse = curl_exec($curl);
				    $createCompanyResponseData = json_decode($createCompanyResponse, true);
				    curl_close($curl);
				} else {
				    // Step 2: Create the company if it doesn't exist
				    $createCompanyData = [
				        'name' => $companyName,
				        'emails' => [
				            ['type' => 'primary', 'email' => $invoice_details['email']]
				        ],
				        'addresses' => [
				            [
				                'type' => 'primary',
				                'address' => [
				                    'street' => $invoice_details['street'],
				                    'number' => $invoice_details['number'],
				                    'postal_code' => $invoice_details['postal_code'],
				                    'city' => $invoice_details['city'],
				                    'country' => 'BE'
				                ]
				            ]
				        ],
				        'marketing_mails_consent' => ($booking->subscribe_newsletter)?true:false
				    ];
				    
				    if($booking->tags) {
					    $createCompanyData['tags'] = [ $booking->tags ];
				    }
				    
				    if($invoice_details['mobile'] == '') {
					    unset($createCompanyData['telephones']);
				    }
				
				    $curl = curl_init();
				    curl_setopt($curl, CURLOPT_URL, $apiUrl . 'companies.add');
				    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
				    curl_setopt($curl, CURLOPT_POST, true);
				    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($createCompanyData));
				
				    $createCompanyResponse = curl_exec($curl);
				    $createCompanyResponseData = json_decode($createCompanyResponse, true);
				    curl_close($curl);
				    
				    var_dump($createCompanyResponseData);
				
					if(isset($createCompanyResponseData['data']['id'])) {
				    	$companyId = $createCompanyResponseData['data']['id'];
				    } else {
					    echo $booking->id;
					    die();
				    }
				    
				    /* Update company with VAT numer */
					$data = [
						'id' => $companyId,
					    "vat_number" => $companyVatNumber // Replace with the new VAT number
					];
					
					// Initialize cURL
					$ch = curl_init($apiUrl.'companies.update');
					
					// Set options for cURL
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_HTTPHEADER, [
					    "Authorization: Bearer $accessToken",
					    "Content-Type: application/json"
					]);
					curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
					
					// Execute the request
					$response = curl_exec($ch);
					
					// Check for errors
					if (curl_errno($ch)) {
					    echo 'Error:' . curl_error($ch);
					} else {
					    echo 'Response:' . $response;
					}
					
					// Close cURL session
					curl_close($ch);
				}
				
				echo '<br>Company ID: '.$companyId;
				
				// Step 3: Search for the contact
				$contactEmail = $invoice_details['email'];
				
				$searchContactData = [
				    'filter' => [
				        'email' => [
				        	'email' => $contactEmail,
				        	'type' => 'primary'
				        ]
				    ]
				];
				
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, $apiUrl . 'contacts.list');
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($searchContactData));
				
				$contactResponse = curl_exec($curl);
				$contactResponseData = json_decode($contactResponse, true);
				curl_close($curl);
				
				$splitted_name = self::splitName($invoice_details['name']);
				
				if (!empty($contactResponseData['data'])) {
				    $contactId = $contactResponseData['data'][0]['id'];
				    
				    $createContactData = [
					    'id' => $contactId,
				        'first_name' => $splitted_name['first_name'],
				        'last_name' => $splitted_name['last_name'],
				        'marketing_mails_consent' => ($booking->subscribe_newsletter)?true:false,
				        'emails' => [['type' => 'primary', 'email' => $contactEmail]],
				        'company_id' => $companyId
				    ];
				    
				    if($booking->tags) {
					    $createContactData['tags'] = [ $booking->tags ];
				    }
				    
				    $curl = curl_init();
				    curl_setopt($curl, CURLOPT_URL, $apiUrl . 'contacts.update');
				    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
				    curl_setopt($curl, CURLOPT_POST, true);
				    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($createContactData));
				
				    $createContactResponse = curl_exec($curl);
				    $createContactResponseData = json_decode($createContactResponse, true);
				    curl_close($curl);
				} else {
				    // Step 4: Create the contact if it doesn't exist
				    $createContactData = [
				        'first_name' => $splitted_name['first_name'],
				        'last_name' => $splitted_name['last_name'],
				        'marketing_mails_consent' => ($booking->subscribe_newsletter)?true:false,
				        'emails' => [['type' => 'primary', 'email' => $contactEmail]],
				        'company_id' => $companyId
				    ];
				    
				    if($booking->tags) {
					    $createContactData['tags'] = [ $booking->tags ];
				    }
				    
				    if($invoice_details['mobile'] == '') {
					    unset($createContactData['telephones']);
				    }
				
				    $curl = curl_init();
				    curl_setopt($curl, CURLOPT_URL, $apiUrl . 'contacts.add');
				    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
				    curl_setopt($curl, CURLOPT_POST, true);
				    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($createContactData));
				
				    $createContactResponse = curl_exec($curl);
				    $createContactResponseData = json_decode($createContactResponse, true);
				    curl_close($curl);
				
				    $contactId = $createContactResponseData['data']['id'];
				}
				
				echo '<br>Contact ID: '.$contactId;
				
				// Step 4.5: Get tax rates
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $apiUrl . 'taxRates.list');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, [
				    'Authorization: Bearer ' . $accessToken,
				    'Content-Type: application/json',
				]);
				
				// Execute the request
				$response = curl_exec($ch);
				curl_close($ch);
				
				// Decode the response
				$taxRates = json_decode($response, true);
				$taxRateId = false;
				
				// Check and display the results
				if (isset($taxRates['data'])) {
				    foreach ($taxRates['data'] as $taxRate) {
				        if($taxRate['rate'] == '0.21') {
					        $taxRateId = $taxRate['id'];
		        		}
		    		}
				}
				
				// Get deal phases
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $apiUrl . 'dealPhases.list');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, [
				    'Authorization: Bearer ' . $accessToken,
				    'Content-Type: application/json',
				]);
				
				// Execute the request
				$response = curl_exec($ch);
				curl_close($ch);
				
				// Decode the response
				$dealPhases = json_decode($response, true);
				$dealPhaseId = '';
				
				foreach($dealPhases['data'] as $phase) {
					if($phase['status'] == 'won') {
						$dealPhaseId = $phase['id'];
					}
				}
				
				echo '<br>Deal Phase ID: '.$dealPhaseId;
				echo '<br>Tax Rate ID: '.$taxRateId;
				
				// Step 5: Create a draft invoice
				if($taxRateId) {
					// Create an invoice if it is not customer_id 1, otherwise create a deal and a quotation
					if($booking->customer_id == 1) {		
						$booking_assets = BookingsAsset::where('booking_id', $booking->id)->where('booking_active', 1)->where('customer_active', 1)->get();
						
						$deal_names = '';
						
						$booking_lines = array();
						
						$lines = json_decode($booking->customer_estimate_lines);
						
						if(isset($lines[0])) {
							$booking_lines['Gedeelde kosten'][] = $lines[0];
						}
						
						foreach($booking_assets as $booking_asset) {
							$asset = Asset::where('id', $booking_asset->asset_id)->first();
							$lines = json_decode($booking_asset->estimate);
							
							$booking_lines[$asset->title] = $lines;
							
							if($deal_names != '') {
								$deal_names .= ', ';
							}
							
							$deal_names .= $asset->title;
						}
										
						// Step 1: Create a Deal
						$customFields = array();
						
						$customFields[] = array(
							'id' => '57b06631-92d3-0817-b149-6d846c3c5e69',
							'value' => date('Y-m-d', strtotime($booking->date))
						);
						
						$dealData = [
							'lead' => [
								'customer' => [
									'type' => 'company',
									'id' => $companyId
								],
								'contact_person_id' => $contactId
							],
						    'title' => $deal_names.' #'.$booking->id,
						    'summary' => 'PO: '.$extra_info_json['po_ref_number'].' - Booking #'.$booking->id, // Optional: ID of the source (like a campaign or referrer)
						    'phase_id' => $dealPhaseId, // The ID of the phase/stage the deal is in
						    'estimated_value' => [
						    	'amount' => 1000, // Value of the deal in the currency set in Teamleader
								'currency' => 'EUR' // Currency of the deal
							],
							'custom_fields' => $customFields
						];
						
						// Initialize cURL request to create the deal
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, $apiUrl . 'deals.create');
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_POST, true);
						curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dealData));
						curl_setopt($ch, CURLOPT_HTTPHEADER, [
						    'Authorization: Bearer ' . $accessToken,
						    'Content-Type: application/json',
						]);
						
						// Execute the request to create the deal
						$response = curl_exec($ch);
						curl_close($ch);
						
						// Decode the response to get the deal ID
						$dealResponse = json_decode($response, true);
						
						if (isset($dealResponse['data']['id'])) {
						    $dealId = $dealResponse['data']['id'];
						    echo '<br>Deal ID: '.$dealId;
						 	
						 	//Step 2: Create the estimate(s)
						 	$quotationData = [
							    'deal_id' => $dealId,
							    'title' => $booking->invoice_name.' #'.$booking->id.' at '.$booking->location,
							    'grouped_lines' => array()
							];

							$customer_settings = CustomerSetting::where('customer_id', $booking->customer_id)->first();
							
						foreach($booking_lines as $section => $lines) {
							$line_items = array();
							
							foreach($lines as $line) {
								$price_incl = $price = $line->value;
								
								if(isset($line->add_markup) && $line->add_markup && $customer_settings->markup) {
									if($customer_settings->markup_type == 1) {
										if(isset($line->markup)) {
											$price_incl += $line->markup;
										} else {
											$price_incl += $customer_settings->markup;
										}
									} else {
										if(isset($line->markup)) {
											$price_incl = $price/100*(100+$line->markup);
										} else {
											$price_incl = $price/100*(100+$customer_settings->markup);
										}
									}
								}
												
								$line_items[] = array(
									'quantity' => 1,
									'description' => $line->line,
									'unit_price' => [
										'amount' => $price_incl,
										'tax' => 'excluding'
									],
									'tax_rate_id' => $taxRateId,
									'purchase_price' => [
										'amount' => $price,
										'currency' => 'EUR'
									]
								);
							}
							
							$glines = array(
								'section' => [
									'title' => $section
								],
								'line_items' => $line_items
							);
							
							$quotationData['grouped_lines'][] = $glines;
						}
							
							// Initialize cURL request to create the quotation
							$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, $apiUrl . 'quotations.create');
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_POST, true);
							curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($quotationData));
							curl_setopt($ch, CURLOPT_HTTPHEADER, [
							    'Authorization: Bearer ' . $accessToken,
							    'Content-Type: application/json',
							]);
							
							// Execute the request to create the quotation
							$quotationResponse = curl_exec($ch);
							curl_close($ch);
							
							// Decode the response
							$quotationResponseData = json_decode($quotationResponse, true);
							
							if (isset($quotationResponseData['data']['id'])) {
							    $quotationId = $quotationResponseData['data']['id'];
							    echo "Quotation created with ID: $quotationId for deal $dealId\n";
							}
							
							// Accept the first estimate
							$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, $apiUrl . 'quotations.accept');
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_POST, true);
							curl_setopt($ch, CURLOPT_POSTFIELDS, "{
							  \"id\": \"".$quotationId."\"
							}");
							curl_setopt($ch, CURLOPT_HTTPHEADER, [
							    'Authorization: Bearer ' . $accessToken,
							    'Content-Type: application/json',
							]);
							
							// Execute the request to create the quotation
							$quotationResponse = curl_exec($ch);
							curl_close($ch);
							
							/* Create 2nd estimate */
							$quotationData = [
							    'deal_id' => $dealId,
							    'title' => $booking->invoice_name.' #'.$booking->id.' at '.$booking->location,
							    'grouped_lines' => array()
							];

							$customer_settings = CustomerSetting::where('customer_id', $booking->customer_id)->first();
							
							foreach($booking_lines as $section => $lines) {
								$line_items = array();
								
								foreach($lines as $line) {
									if(isset($line->visible_speaker) && $line->visible_speaker) {
										$price_incl = $price = $line->value;
														
										$line_items[] = array(
											'quantity' => 1,
											'description' => $line->line,
											'unit_price' => [
												'amount' => $price_incl,
												'tax' => 'excluding'
											],
											'tax_rate_id' => $taxRateId,
											'purchase_price' => [
												'amount' => $price,
												'currency' => 'EUR'
											]
										);
									}
								}
								
								if(count($line_items)) {
									$glines = array(
										'section' => [
											'title' => $section
										],
										'line_items' => $line_items
									);
									
									$quotationData['grouped_lines'][] = $glines;
								}
							}
							
							// Initialize cURL request to create the quotation
							$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, $apiUrl . 'quotations.create');
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_POST, true);
							curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($quotationData));
							curl_setopt($ch, CURLOPT_HTTPHEADER, [
							    'Authorization: Bearer ' . $accessToken,
							    'Content-Type: application/json',
							]);
							
							// Execute the request to create the quotation
							$quotationResponse = curl_exec($ch);
							curl_close($ch);
							
							// Decode the response
							$quotationResponseData = json_decode($quotationResponse, true);
							
							if (isset($quotationResponseData['data']['id'])) {
							    $quotationId = $quotationResponseData['data']['id'];
							    echo "Quotation created with ID: $quotationId for deal $dealId\n";
							}
							
							$booking->teamleader_feedback = $quotationResponse;
							$booking->save();
						}
					} else {
						$price = $booking->price;
						$price_excl = $price/121*100;
						$price_excl = str_replace(',', '.', $price_excl);
						
						$asset = Asset::where('id', $booking['asset_id'])->first();
	
						$invoiceData = [
							'invoicee' => [
								'customer' => [
									'type' => 'company',
									'id' => $companyId
								]	
							],
						    'grouped_lines' => [
							    [
								    'line_items' => [
								        [
								            'description' => 'Meeting '.$asset->title,
								            'quantity' => 1,
								            'unit_price' => [
								            	'amount' => $price_excl,
								            	'tax' => 'excluding'
								            ],
								            'tax_rate_id' => $taxRateId
	
								        ]
							        ]
						        ]
						    ],
						    'payment_term' => [
						        'type' => 'end_of_month',
						        'days' => 30
						    ]
						];
						
						if($invoice_details['payment_time_end_month'] == 0) {
							$invoiceData['payment_term']['type'] = 'cash';
							unset($invoiceData['payment_term']['days']);
						}
						
						if(isset($invoice_details['po_ref_number']) && $invoice_details['po_ref_number']) {
							$invoiceData['po_ref_number'] = $invoice_details['purchase_order_number'];
						}
						
						$curl = curl_init();
						curl_setopt($curl, CURLOPT_URL, $apiUrl . 'invoices.draft');
						curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
						curl_setopt($curl, CURLOPT_POST, true);
						curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($invoiceData));
						
						$invoiceResponse = curl_exec($curl);
						$invoiceResponseData = json_decode($invoiceResponse, true);
						curl_close($curl);
						
						$booking->teamleader_feedback = $invoiceResponse;
						$booking->save();
					
						// Step 6: Book the draft invoice
						$invoiceData = [
							'id' => $invoiceResponseData['data']['id'],
							'on' => date('Y-m-d')
						];
						
						$curl = curl_init();
						curl_setopt($curl, CURLOPT_URL, $apiUrl . 'invoices.book');
						curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
						curl_setopt($curl, CURLOPT_POST, true);
						curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($invoiceData));
						
						$invoiceBookResponse = curl_exec($curl);
						$invoiceBookResponseData = json_decode($invoiceBookResponse, true);
						curl_close($curl);
						
						if (isset($invoiceResponseData['data']['id'])) {
						    $invoiceId = $invoiceResponseData['data']['id'];
						    echo "Invoice created with ID: $invoiceId\n";
						    
						    // Send the invoice via email
						    $apiUrlSendInvoice = 'https://api.teamleader.eu/invoices.send';
						
						    // Data for sending the invoice (you can customize the message or other options)
						    $sendInvoiceData = [
						        'id' => $invoiceId,
						        'delivery_method' => 'email', // Sending via email
						        'recipients' => [
							        'to' => [
								        [
								        	'customer' => [
								        		'type' => 'contact',
								        		'id' => $contactId, // Customer's email address
								        	],
								        	'email' => $contactEmail,
								        ]
								    ]
						        ],
						        'content' => [
						            'subject' => 'Je Read My Lips factuur',
						            'body' => 'Beste klant,
						            
		Alvast bedankt voor je bestelling. In bijlage kan je je factuur terugvinden.
						            
		Fijne dag!'
						        ]
						    ];
						
						    // Initialize cURL request to send the invoice
						    $ch = curl_init();
						    curl_setopt($ch, CURLOPT_URL, $apiUrlSendInvoice);
						    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						    curl_setopt($ch, CURLOPT_POST, true);
						    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($sendInvoiceData));
						    curl_setopt($ch, CURLOPT_HTTPHEADER, [
						        'Authorization: Bearer ' . $accessToken,
						        'Content-Type: application/json',
						    ]);
						
						    // Execute the request
						    $sendResponse = curl_exec($ch);
						    curl_close($ch);
						
						    // Check response
						    $sendInvoiceResponse = json_decode($sendResponse, true);
						} else {
						    echo "Error creating invoice: " . $invoiceResponseData['message'] . "\n";
						}
					}					
				}
			}
		}
	}
}
