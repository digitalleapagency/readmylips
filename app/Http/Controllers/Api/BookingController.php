<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;

use App\Models\Booking;
use App\Models\User;
use App\Models\Asset;
use App\Models\MailSettings;
use App\Models\CustomerSetting;
use App\Models\BookingsAsset;
use App\Models\RoleUser;
use App\Models\Customer;
use App\Models\Meeting;

use GuzzleHttp\Client;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Audio\Mp3;
use Carbon\Carbon;

use DB;
use Auth;
use DateTime;

class BookingController extends Controller
{
	public function bookingToInvoice($id) {
		$booking = Booking::where('id', $id)->first();
		
		$teamleaderController = new TeamleaderController();
		$teamleaderController->createInvoice($booking->id);
		
		$booking->status = 6;
		$booking->save();        
	}
	
	public function unseen_count() {
		$statuses = array(
			'requested', 'summarized', 'open', 'rejected', 'resend', 'toinvoice', 'extra', 'estimates', 'finished', 'date_request', 'refused', 'accepted', 'acceptedb', 'cancelled', 'info_needed', 'call', 'onhold', 'resendoffer'
		);
		$booking_count = array();
		
		foreach($statuses as $status) {
			$bookings = self::index($status, true, Auth::user()->id);
			$booking_count[$status] = count($bookings);
		}
		
		return response()->json(['counts' => $booking_count], 200);
	}
	
    public function index($type = false, $unread = false, $user_id = false)
    {
	    /* Set bookings who are expired to finished */
	    $bookings = Booking::where('customer_refused', 0);
    	$bookings->where('status', '!=', 6);
    	$bookings->where('status', '!=', 2);
    	$bookings->where('status', '!=', 99);
    	$bookings->where('status', '!=', 90);
    	$bookings->where('customer_id', 2);
    	$bookings = $bookings->get();
    	
	    foreach($bookings as $booking) {
		    if(strtotime($booking->date) < time()) {
			    $booking->status = 6;
			    $booking->save();
			}
	    }
	    
	    $customer = \Request::header('Customer');
	    $customer_settings = CustomerSetting::where('customer_id', $customer)->first();
	    
		$user = User::find(Auth::user()->id);
		
        $bookings = Booking::where('bookings.customer_id', $customer)->where('paid', 1)->orderBy('bookings.id', 'desc');
        
        if($user_id) {
	        $bookings->where('managing_user_id', $user_id);
        }
        
        if($type || isset($_GET['startDate'])) {
	        if(isset($_GET['status'])) {
		        $type = $_GET['status'];
	        }
	        
            switch($type) {
		        case 'requested':
		        	$bookings->where('status', 1);
		        	
		        	if($customer_settings->booking_flow == 2) {
			        	$bookings->join('meetings', 'bookings.id', '=', 'meetings.booking_id', 'left');
			        	$bookings->where('meetings.summarized', 0);
		        	}
		        	break;
		        case 'info_needed':
		        	$bookings->where('status', 80);
		        	break;
		        case 'call':
		        	$bookings->where('status', 81);
		        	break;
		        case 'onhold':
		        	$bookings->where('status', 82);
		        	break;
		        case 'resendoffer':
		        	$bookings->where('status', 83);
		        	break;
		        case 'open':
		        	$bookings->where('customer_refused', 0);
		        	$bookings->where('status', '!=', 6);
		        	$bookings->where('status', '!=', 13);
		        	$bookings->where('status', '!=', 2);
		        	$bookings->where('status', '!=', 99);
		        	$bookings->where('status', '!=', 90);
		        	break;
		        case 'summarized':
		        	$bookings->select('bookings.*');
		        	$bookings->join('meetings', 'bookings.id', '=', 'meetings.booking_id');
		        	$bookings->where('meetings.summarized', 1);
		        	break;
		        case 'rejected':
		        	$bookings->where('status', 2);
		        	break;
		        case 'resend':
		        	$bookings->where('customer_refused', 0);
		        	$bookings->where('status', 4);
		        	break;
		        case 'estimates':
		        	$bookings->where('customer_refused', 0);
		        	$bookings->where('status', 10);
		        	break;
		        case 'finished':
		        	$bookings->where('customer_refused', 0);
		        	$bookings->where('status', 6);
		        	break;
		        case 'date_request':
		        	$bookings->where('customer_refused', 0);
		        	$bookings->where('status', 12);
		        	break;
		        case 'toinvoice':
		        	$bookings->where('customer_refused', 0);
		        	$bookings->where('status', 13);
		        	break;
		        case 'refused':
		        	$bookings->where('customer_refused', 1);
		        	$bookings->where('status', '!=', 99);
		        	$bookings->where('status', '!=', 90);
		        	break;
		        case 'accepted':
		        	$bookings->where('status', 11);
		        	break;
		        case 'archive':
		        	$bookings->where('status', 90);
		        	break;
		        case 'acceptedb':
		        	$bookings->where('status', 3);
		        	
		        	$today = Carbon::now()->format('Y-m-d');

		        	$bookings->where(DB::raw("STR_TO_DATE(date, '%d-%m-%Y')"), '>=', $today);
		        	break;
		        case 'cancelled':
		        	$bookings->where('status', 99);
		        	break;
	        }
	    } else {
		    $asset = Asset::where('user_id', $user->id)->where('customer_id', $customer)->first();
		    $bookings->where('asset_id', $asset->id);
	    }
	    
	    if(isset($_GET['event_date_start']) && isset($_GET['event_date_end'])) {
		    $startDate = $_GET['event_date_start']; // Replace with your start date
			$endDate = $_GET['event_date_end'];   // Replace with your end date
			
			$startDateTime = DateTime::createFromFormat('Y-m-d', $startDate);
			$endDateTime = DateTime::createFromFormat('Y-m-d', $endDate);
			
			if (!$startDateTime || !$endDateTime) {
			    die("Invalid date format");
			}
			
			$interval = new \DateInterval('P1D');
			
			$dateRange = new \DatePeriod($startDateTime, $interval, $endDateTime->modify('+1 day'));
			$dates = array();
			
			foreach ($dateRange as $date) {
			    $dates[] = $date->format('d-m-Y');
			}
			
		    $bookings->whereIn('date', $dates);
	    }
	    
	    if(isset($_GET['request_date_start']) && isset($_GET['request_date_end'])) {
		    $startDate = $_GET['request_date_start']; // Replace with your start date
			$endDate = $_GET['request_date_end'];   // Replace with your end date
			
			$startDateTime = DateTime::createFromFormat('Y-m-d', $startDate);
			$endDateTime = DateTime::createFromFormat('Y-m-d', $endDate);
			
			if (!$startDateTime || !$endDateTime) {
			    die("Invalid date format");
			}
			
		    $bookings->where('created_at', '>=', $startDateTime->format('Y-m-d 00:00:00'));
			$bookings->where('created_at', '<=', $endDateTime->format('Y-m-d 23:59:59'));
	    }
	    
	    if(isset($_GET['company_name'])) {
		    $bookings->where('extra_info_json', 'like', '%' . $_GET['company_name'] . '%');
	    }
	    
	    if(isset($_GET['startDate'])) {
		    $startDate = $_GET['startDate']; // Replace with your start date
			$endDate = $_GET['endDate'];   // Replace with your end date
			
			// Convert string dates to DateTime objects
			$startDateTime = DateTime::createFromFormat('d-m-Y', $startDate);
			$endDateTime = DateTime::createFromFormat('d-m-Y', $endDate);
			
			// Check if the conversion was successful
			if (!$startDateTime || !$endDateTime) {
			    die("Invalid date format");
			}
			
			// Create an interval of 1 day
			$interval = new \DateInterval('P1D');
			
			// Create a date range
			$dateRange = new \DatePeriod($startDateTime, $interval, $endDateTime->modify('+1 day'));
			$dates = array();
			
			// Iterate through the dates
			foreach ($dateRange as $date) {
			    $dates[] = $date->format('d-m-Y');
			}
			
		    $bookings->whereIn('date', $dates);
		    
        	$bookings->where('customer_refused', 0);
        	$bookings->where('status', 6);
        	
		    $bookings->orderBy('asset_id');
		    
		    //$statusses = array(99, 2);		    
		    //$bookings->whereNotIn('status', $statusses);
		}
	    
	    if(isset($_GET['name'])) {		     
		    $assets = Asset::where('active', 1);
			$assets->where('title', 'like', '%' . $_GET['name'] . '%');
			$assets->orWhere('email', 'like', '%' . $_GET['name'] . '%');
			$asset_ids = $assets->pluck('id');
		    
		    if(count($asset_ids)) {
			    $booking_assets = BookingsAsset::whereIn('asset_id', $asset_ids)->pluck('booking_id');
			    
				$bookings->whereIn('id', $booking_assets);
			} else {
				$bookings->whereIn('bookings.id', 99999999);
			}
	    }
	    
	    if(isset($_GET['search'])) {
		    $bookingsSearch = Booking::orderBy('id', 'desc');
		    
		    $bookingsSearch->where('bookings.customer_id', $customer);
		    $bookingsSearch->where('email', 'like', '%' . $_GET['search'] . '%');
		    
		    $bookingsSearch->orWhere('bookings.customer_id', $customer);
		    $bookingsSearch->where('invoice_company', 'like', '%' . $_GET['search'] . '%');
		    
		    $bookingsSearch->orWhere('bookings.customer_id', $customer);
		    $bookingsSearch->where('invoice_name', 'like', '%' . $_GET['search'] . '%');
		    
		    $booking_ids = $bookingsSearch->pluck('id');
		    
		    $bookings->whereIn('bookings.id', $booking_ids);
	    }
	    
	    if(isset($_GET['id'])) {		     
		    $bookings->where('bookings.id', $_GET['id']);
	    }
	    
	    if(isset($_GET['claimed_by'])) {		     
		    $bookings->where('managing_user_id', $_GET['claimed_by']);
	    }
	    
	    if($unread) {
		    $bookings->where('booking_seen', 0);
	    }
	    
        $bookings = $bookings->get();        
        $bookings = $bookings->toArray();
        
        if($unread) {
			return $bookings;
		} else {        
	        foreach($bookings as $key => $booking) {
		        $booking_assets = BookingsAsset::where('booking_id', $booking['id'])->where('bookings_assets.booking_active', 1)->join('assets', 'assets.id', 'asset_id')->get()->toArray();
		        
		        $asset = Asset::where('id', $booking['asset_id'])->first();
		        
		        $bookings[$key]['meeting'] = Meeting::where('booking_id', $booking['id'])->first();
		        
		        $bookings[$key]['asset'] = $asset;
		        $bookings[$key]['assets'] = $booking_assets;
		        $bookings[$key]['date'] = date('d-m-Y', strtotime($bookings[$key]['date']));
	        }
			
	        return response()->json(['bookings' => $bookings], 200);
	    }
    }
    
    public function exportCsv()
    {
	    // Get type from query parameter or route, default to 'all'
	    $type = isset($_GET['type']) ? $_GET['type'] : (isset($_GET['status']) ? $_GET['status'] : 'all');
	    /* Set bookings who are expired to finished */
	    $bookings = Booking::where('customer_refused', 0);
    	$bookings->where('status', '!=', 6);
    	$bookings->where('status', '!=', 2);
    	$bookings->where('status', '!=', 99);
    	$bookings->where('status', '!=', 90);
    	$bookings->where('customer_id', 2);
    	$bookings = $bookings->get();
    	
	    foreach($bookings as $booking) {
		    if(strtotime($booking->date) < time()) {
			    $booking->status = 6;
			    $booking->save();
			}
	    }
	    
	    $customer = \Request::header('Customer');
	    $customer_settings = CustomerSetting::where('customer_id', $customer)->first();
	    
		$user = User::find(Auth::user()->id);
		
        $bookings = Booking::where('bookings.customer_id', $customer)->where('paid', 1)->orderBy('bookings.id', 'desc');
        
        if($type || isset($_GET['startDate'])) {
	        if(isset($_GET['status'])) {
		        $type = $_GET['status'];
	        }
	        
            switch($type) {
		        case 'requested':
		        	$bookings->where('status', 1);
		        	
		        	if($customer_settings->booking_flow == 2) {
			        	$bookings->join('meetings', 'bookings.id', '=', 'meetings.booking_id', 'left');
			        	$bookings->where('meetings.summarized', 0);
		        	}
		        	break;
		        case 'info_needed':
		        	$bookings->where('status', 80);
		        	break;
		        case 'call':
		        	$bookings->where('status', 81);
		        	break;
		        case 'onhold':
		        	$bookings->where('status', 82);
		        	break;
		        case 'resendoffer':
		        	$bookings->where('status', 83);
		        	break;
		        case 'open':
		        	$bookings->where('customer_refused', 0);
		        	$bookings->where('status', '!=', 6);
		        	$bookings->where('status', '!=', 13);
		        	$bookings->where('status', '!=', 2);
		        	$bookings->where('status', '!=', 99);
		        	$bookings->where('status', '!=', 90);
		        	break;
		        case 'summarized':
		        	$bookings->select('bookings.*');
		        	$bookings->join('meetings', 'bookings.id', '=', 'meetings.booking_id');
		        	$bookings->where('meetings.summarized', 1);
		        	break;
		        case 'rejected':
		        	$bookings->where('status', 2);
		        	break;
		        case 'resend':
		        	$bookings->where('customer_refused', 0);
		        	$bookings->where('status', 4);
		        	break;
		        case 'estimates':
		        	$bookings->where('customer_refused', 0);
		        	$bookings->where('status', 10);
		        	break;
		        case 'finished':
		        	$bookings->where('customer_refused', 0);
		        	$bookings->where('status', 6);
		        	break;
		        case 'date_request':
		        	$bookings->where('customer_refused', 0);
		        	$bookings->where('status', 12);
		        	break;
		        case 'toinvoice':
		        	$bookings->where('customer_refused', 0);
		        	$bookings->where('status', 13);
		        	break;
		        case 'refused':
		        	$bookings->where('customer_refused', 1);
		        	$bookings->where('status', '!=', 99);
		        	$bookings->where('status', '!=', 90);
		        	break;
		        case 'accepted':
		        	$bookings->where('status', 11);
		        	break;
		        case 'archive':
		        	$bookings->where('status', 90);
		        	break;
		        case 'acceptedb':
		        	$bookings->where('status', 3);
		        	
		        	$today = Carbon::now()->format('Y-m-d');

		        	$bookings->where(DB::raw("STR_TO_DATE(date, '%d-%m-%Y')"), '>=', $today);
		        	break;
		        case 'cancelled':
		        	$bookings->where('status', 99);
		        	break;
		        case 'all':
		        	// Show all bookings, no additional status filter
		        	break;
	        }
	    } else {
		    $asset = Asset::where('user_id', $user->id)->where('customer_id', $customer)->first();
		    if($asset) {
			    $bookings->where('asset_id', $asset->id);
		    }
	    }
	    
	    if(isset($_GET['event_date_start']) && isset($_GET['event_date_end'])) {
		    $startDate = $_GET['event_date_start'];
			$endDate = $_GET['event_date_end'];
			
			$startDateTime = DateTime::createFromFormat('Y-m-d', $startDate);
			$endDateTime = DateTime::createFromFormat('Y-m-d', $endDate);
			
			if (!$startDateTime || !$endDateTime) {
			    die("Invalid date format");
			}
			
			$interval = new \DateInterval('P1D');
			
			$dateRange = new \DatePeriod($startDateTime, $interval, $endDateTime->modify('+1 day'));
			$dates = array();
			
			foreach ($dateRange as $date) {
			    $dates[] = $date->format('d-m-Y');
			}
			
		    $bookings->whereIn('date', $dates);
	    }
	    
	    if(isset($_GET['request_date_start']) && isset($_GET['request_date_end'])) {
		    $startDate = $_GET['request_date_start'];
			$endDate = $_GET['request_date_end'];
			
			$startDateTime = DateTime::createFromFormat('Y-m-d', $startDate);
			$endDateTime = DateTime::createFromFormat('Y-m-d', $endDate);
			
			if (!$startDateTime || !$endDateTime) {
			    die("Invalid date format");
			}
			
		    $bookings->where('created_at', '>=', $startDateTime->format('Y-m-d 00:00:00'));
			$bookings->where('created_at', '<=', $endDateTime->format('Y-m-d 23:59:59'));
	    }
	    
	    if(isset($_GET['company_name'])) {
		    $bookings->where('extra_info_json', 'like', '%' . $_GET['company_name'] . '%');
	    }
	    
	    if(isset($_GET['startDate'])) {
		    $startDate = $_GET['startDate'];
			$endDate = $_GET['endDate'];
			
			$startDateTime = DateTime::createFromFormat('d-m-Y', $startDate);
			$endDateTime = DateTime::createFromFormat('d-m-Y', $endDate);
			
			if (!$startDateTime || !$endDateTime) {
			    die("Invalid date format");
			}
			
			$interval = new \DateInterval('P1D');
			
			$dateRange = new \DatePeriod($startDateTime, $interval, $endDateTime->modify('+1 day'));
			$dates = array();
			
			foreach ($dateRange as $date) {
			    $dates[] = $date->format('d-m-Y');
			}
			
		    $bookings->whereIn('date', $dates);
		    
        	$bookings->where('customer_refused', 0);
        	$bookings->where('status', 6);
        	
		    $bookings->orderBy('asset_id');
		}
	    
	    if(isset($_GET['name'])) {		     
		    $assets = Asset::where('active', 1);
			$assets->where('title', 'like', '%' . $_GET['name'] . '%');
			$assets->orWhere('email', 'like', '%' . $_GET['name'] . '%');
			$asset_ids = $assets->pluck('id');
		    
		    if(count($asset_ids)) {
			    $booking_assets = BookingsAsset::whereIn('asset_id', $asset_ids)->pluck('booking_id');
			    
				$bookings->whereIn('id', $booking_assets);
			} else {
				$bookings->whereIn('bookings.id', 99999999);
			}
	    }
	    
	    if(isset($_GET['search'])) {
		    $bookingsSearch = Booking::orderBy('id', 'desc');
		    
		    $bookingsSearch->where('bookings.customer_id', $customer);
		    $bookingsSearch->where('email', 'like', '%' . $_GET['search'] . '%');
		    
		    $bookingsSearch->orWhere('bookings.customer_id', $customer);
		    $bookingsSearch->where('invoice_company', 'like', '%' . $_GET['search'] . '%');
		    
		    $bookingsSearch->orWhere('bookings.customer_id', $customer);
		    $bookingsSearch->where('invoice_name', 'like', '%' . $_GET['search'] . '%');
		    
		    $booking_ids = $bookingsSearch->pluck('id');
		    
		    $bookings->whereIn('bookings.id', $booking_ids);
	    }
	    
	    if(isset($_GET['id'])) {		     
		    $bookings->where('bookings.id', $_GET['id']);
	    }
	    
	    if(isset($_GET['claimed_by'])) {		     
		    $bookings->where('managing_user_id', $_GET['claimed_by']);
	    }
        
        $bookings = $bookings->get();
        
        // Prepare CSV data
        $filename = 'bookings_export_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($bookings, $customer_settings) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8 Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // CSV Headers
            $headers = [
                'ID',
                'Date',
                'Status',
                'Invoice Name',
                'Invoice Company',
                'Email',
                'Mobile',
                'Location',
                'Amount of Visitors',
                'Invoice Address',
                'Invoice Postal',
                'Invoice City',
                'Speakers/Moderators',
                'Managing User',
                'Created At',
                'Remark'
            ];
            
            fputcsv($file, $headers, ';');
            
            // Status mapping
            $statusMap = [
                1 => '1. Request',
                2 => 'Booking refused',
                3 => '5. Speaker answer',
                4 => '6. Callsheet info',
                6 => '8. Final',
                10 => '2. Offer',
                11 => '3. Accepted offer',
                12 => '4. Agenda check',
                13 => '7. To invoice',
                80 => '2.1 Extra info needed',
                81 => '2.3 Call',
                82 => '2.4 On hold',
                83 => '2.2 Offer resend',
                90 => 'Archived',
                99 => 'Cancelled'
            ];
            
            foreach($bookings as $booking) {
                // Get assets
                $booking_assets = BookingsAsset::where('booking_id', $booking->id)
                    ->where('bookings_assets.booking_active', 1)
                    ->join('assets', 'assets.id', 'asset_id')
                    ->get();
                
                $assets_names = $booking_assets->pluck('title')->toArray();
                $assets_string = implode(', ', $assets_names);
                
                // Get managing user
                $managing_user_name = '';
                if($booking->managing_user_id) {
                    $managing_user = User::find($booking->managing_user_id);
                    if($managing_user) {
                        $managing_user_name = $managing_user->name;
                    }
                }
                
                // Format status
                $status_name = isset($statusMap[$booking->status]) ? $statusMap[$booking->status] : 'Unknown';
                
                // Format date
                $date_formatted = $booking->date ? date('d-m-Y', strtotime($booking->date)) : 'Unknown';
                
                $row = [
                    $booking->id,
                    $date_formatted,
                    $status_name,
                    $booking->invoice_name ?? '',
                    $booking->invoice_company ?? '',
                    $booking->email ?? '',
                    $booking->mobile ?? '',
                    $booking->location ?? '',
                    $booking->amount_of_visitors ?? '',
                    $booking->invoice_address ?? '',
                    $booking->invoice_postal ?? '',
                    $booking->invoice_city ?? '',
                    $assets_string,
                    $managing_user_name,
                    $booking->created_at ? date('d-m-Y H:i', strtotime($booking->created_at)) : '',
                    $booking->remark ?? ''
                ];
                
                fputcsv($file, $row, ';');
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    public function show($id)
    {
	    $customer = \Request::header('Customer');
	    $customer = Customer::where('id', $customer)->first();
	    $customer_settings = CustomerSetting::where('customer_id', $customer->id)->first();
	    
        /* Set seen as admin */
	    $user = User::find(Auth::user()->id);
	    $admin_user_ids = RoleUser::where('role_id', 1)->where('customer_id', $customer->id)->pluck('user_id')->toArray();
		
		if(in_array(Auth::user()->id, $admin_user_ids)) {
			$booking = Booking::where('id', $id)->where('customer_id', $customer->id)->first();
			$booking->booking_seen = 1;
			$booking->save();
		}
		 
        $booking = Booking::where('id', $id)->where('customer_id', $customer->id)->first()->toArray();
        
        $asset = Asset::where('id', $booking['asset_id'])->first();
		        
		$booking['meeting'] = Meeting::where('booking_id', $booking['id'])->first();
        
        $booking['asset'] = $asset;
        $booking['date'] = date('d-m-Y', strtotime($booking['date']));
        
        if($booking['date_unknown']) {
	        $booking['date'] = '';
        }
        
        $booking['extra_json'] = json_decode($booking['extra_info_json']);
        
        if($customer_settings->booking_flow == 1) {
	        switch($booking['status']) {
		        case 1:
		        	$booking['status'] = array(
			        	'id' => 1,
			        	'name' => '1. Request'
		        	);
		        	break;
		        case 2:
		        	$booking['status'] = array(
			        	'id' => 2,
			        	'name' => 'Booking refused'
		        	);
		        	break;
		        case 3:
		        	$booking['status'] = array(
			        	'id' => 3,
			        	'name' => '5. Speaker answer'
		        	);
		        	break;
		        case 4:
		        	$booking['status'] = array(
			        	'id' => 4,
			        	'name' => '6. Callsheet info'
		        	);
		        	break;
		        case 6:
		        	$booking['status'] = array(
			        	'id' => 6,
			        	'name' => '8. Final'
		        	);
		        	break;
		        case 13:
		        	$booking['status'] = array(
			        	'id' => 13,
			        	'name' => '7. To invoice'
		        	);
		        	break;
		        case 10:
		        	$booking['status'] = array(
			        	'id' => 10,
			        	'name' => '2. Offer'
		        	);
		        	break;
		        case 11:
		        	$booking['status'] = array(
			        	'id' => 11,
			        	'name' => '3. Accepted offer'
		        	);
		        	break;
		        case 12:
		        	$booking['status'] = array(
			        	'id' => 12,
			        	'name' => '4. Agenda check'
		        	);
		        	break;
		        case 80:
		        	$booking['status'] = array(
			        	'id' => 80,
			        	'name' => '2.1 Extra info needed'
		        	);
		        	break;
		        case 81:
		        	$booking['status'] = array(
			        	'id' => 81,
			        	'name' => '2.3 Call'
		        	);
		        	break;
		        case 82:
		        	$booking['status'] = array(
			        	'id' => 82,
			        	'name' => '2.4 On hold'
		        	);
		        	break;
		        case 83:
		        	$booking['status'] = array(
			        	'id' => 83,
			        	'name' => '2.2 Offer resend'
		        	);
		        	break;
		        case 99:
		        	$booking['status'] = array(
			        	'id' => 99,
			        	'name' => 'Cancelled'
		        	);
		        	break;
	        }
	    } else {
		    switch($booking['status']) {
		        case 3:
		        	$booking['status'] = array(
			        	'id' => 3,
			        	'name' => 'Booking'
		        	);
		        	break;
		        case 6:
		        	$booking['status'] = array(
			        	'id' => 6,
			        	'name' => 'Finished'
		        	);
		        	break;
		        case 99:
		        	$booking['status'] = array(
			        	'id' => 99,
			        	'name' => 'Cancelled'
		        	);
		        	break;
	        }
	    }
	    
	    if($booking['tags']) {
		    $booking['tags'] = array(
	        	'id' => $booking['tags'],
	        	'name' => ucfirst($booking['tags'])
        	);
	    }
	    
	    $source_options = \App\Http\Controllers\Api\CustomerSettingController::decode_source_options($customer_settings->source_options ?? null);
	    
	    if($booking['source']) {
		    $matched = null;
		    foreach($source_options as $opt) {
			    if($opt['id'] == $booking['source']) {
				    $matched = $opt;
				    break;
			    }
		    }
		    $booking['source'] = $matched ?: array(
	        	'id' => $booking['source'],
	        	'name' => ucfirst($booking['source'])
        	);
	    }
	    
	    $refused_reason_options = \App\Http\Controllers\Api\CustomerSettingController::decode_refused_reason_options($customer_settings->refused_reason_options ?? null);
	    
	    if(!empty($booking['customer_refused_reason_id'])) {
		    $matched = null;
		    foreach($refused_reason_options as $opt) {
			    if($opt['id'] == $booking['customer_refused_reason_id']) {
				    $matched = $opt;
				    break;
			    }
		    }
		    $booking['customer_refused_reason_id'] = $matched ?: array(
	        	'id' => $booking['customer_refused_reason_id'],
	        	'name' => ucfirst($booking['customer_refused_reason_id'])
        	);
	    } else if(!empty($booking['customer_refused_reason'])) {
		    $other = null;
		    foreach($refused_reason_options as $opt) {
			    if($opt['id'] == 'other') {
				    $other = $opt;
				    break;
			    }
		    }
		    if($other) {
			    $booking['customer_refused_reason_id'] = $other;
		    }
	    }
        
        $settings = CustomerSetting::where('customer_id', $customer->id)->first();
        
        /* Bugfix customer estimate lines */
        if($customer_settings->booking_flow == 1) {
	        if($booking['customer_estimate_lines'] != '') {
		        $booking_update = Booking::where('id', $id)->where('customer_id', $customer->id)->first();
		        
		        $original = $booking['customer_estimate_lines'];
		        
				$new = str_replace('\\', '', $original);
				$new = str_replace('"[', '[', $new);
				$new = str_replace(']"', ']', $new);
				$new = str_replace('""', '"', $new);
				
				$booking['customer_estimate_lines'] = $new;
				$booking_update->customer_estimate_lines = $booking['customer_estimate_lines'];
				$booking_update->save();			
	        }
	        /* Add new code for each speaker */
	        $speaker_lines = array();
	        $default_lines = json_decode($settings->default_estimate_lines);
	        
	        foreach($default_lines as $line) {
		        if(isset($line->visible_speaker) && $line->visible_speaker) {
			        $speaker_lines[] = $line;
		        }
	        }
		        
	        if($booking['customer_estimate_lines'] == '' || $booking['customer_estimate_lines'] == null) {
		        $booking['customer_estimate_lines'] = array();	        
		        
		        foreach($default_lines as $line) {
			        if(!$line->visible_speaker) {
				        $booking['customer_estimate_lines'][] = $line;
			        }
		        }
	        }
        
	        $booking_assets = BookingsAsset::select('assets.title', 'assets.price', 'bookings_assets.*')->where('booking_id', $booking['id'])->where('bookings_assets.booking_active', 1)->where('bookings_assets.customer_active', 1)->join('assets', 'assets.id', 'asset_id')->get();
	        $booking_inactive_assets = BookingsAsset::where('booking_id', $booking['id'])->where('bookings_assets.booking_active', 0)->orWhere('booking_id', $booking['id'])->where('bookings_assets.customer_active', 0)->join('assets', 'assets.id', 'asset_id')->get();
	        
	        foreach($booking_assets as $booking_asset) {	             
		        if($booking_asset->estimate == '' || $booking_asset->estimate == NULL) {		        
			        $lines_array = array();
			        
			        $lines_array[] = array(
				        'line' => 'Speaker fee',
				        'value' => $booking_asset->price/100,
				        'visible_speaker' => true,
				        'add_markup' => true
			        );
			        
			        foreach($speaker_lines as $line) {
				        $lines_array[] = $line;
			        }
			        
			        $booking_asset->estimate = json_encode($lines_array);
			        $booking_asset->save();
		        }
	        }
        
	        $booking['inactive_assets'] = $booking_inactive_assets->toArray();
	    }
	    
	    $booking_assets = BookingsAsset::select('assets.title', 'assets.price', 'bookings_assets.*', 'bookings_assets.asset_id as id', 'bookings_assets.asset_id')->where('booking_id', $booking['id'])->where('bookings_assets.booking_active', 1)->where('bookings_assets.customer_active', 1)->join('assets', 'assets.id', 'asset_id')->get()->toArray();
	        
        foreach($booking_assets as $key => $booking_asset) {
	        $booking_assets[$key]['estimate'] = json_decode($booking_asset['estimate']);
        }
        
		$booking['assets'] = $booking_assets;
	    
	    $result = array();
        
        /* Summarize */
        if($booking['video_link'] && $booking['audio_link'] == '') {
	        $client = new Client();
			$url = $booking['video_link'];
	        $response = $client->get($url);
	
	        // Check if the request was successful (status code 200)
	        if ($response->getStatusCode() === 200) {
	            // Save the video content to a local file
	            $videoPath = storage_path('../public/videos/video.mp4');
	            file_put_contents($videoPath, $response->getBody());
	            
	            /* Download audio */
	            $videoPath = public_path('videos/video.mp4');
				$audioPath = public_path('audios/audio-'.$booking['id'].'-'.time().'.mp3');
		
			    $ffmpeg = FFMpeg::create();
			    $video = $ffmpeg->open($videoPath);
			
			    // Extract audio in mp3 format
			    $format = new Mp3();
			    $video->save($format, $audioPath);
			    
		        $booking_object = Booking::where('id', $booking['id'])->first();
		        $booking_object->audio_link = $audioPath;
		        $booking_object->save();
	        }
        }
        
        if($booking['audio_link'] && $booking['text_to_audio_id'] == '') {
	        $booking['audio_link'] = str_replace('/home/1268001.cloudwaysapps.com/fzadmzuzcp/public_html/public/', 'https://phplaravel-1146384-3986140.cloudwaysapps.com/', $booking['audio_link']);
	        
	        $ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://api.edenai.run/v2/audio/speech_to_text_async');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
			    "authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoiZjZhOWNiNTEtNjBlYy00ZjczLWE3NTktMDllMzFhZmQ4ZWI3IiwidHlwZSI6ImFwaV90b2tlbiJ9.ct5ZHDM2-iFDCCJbML0F4N5U5jFhPCvkDuqG6xXxzBc",
			    'content-type: application/json',
			]);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "{\n  \"providers\": \"assembly\",\n  \"file_url\": \"".$booking['audio_link']."\",\n  \"language\": \"en\",\n  \"profanity_filter\": false,\n  \"custom_vocabulary\": \"\"\n}");
			
			$response = curl_exec($ch);
			
			curl_close($ch);
			
			$result = json_decode($response);
			
			$booking_object = Booking::where('id', $booking['id'])->first();
	        $booking_object->text_to_audio_id = $result->public_id;
	        $booking_object->save();
        }
        
        if($booking['text_to_audio_id'] && $booking['meeting_text'] == '') {
	        $ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://api.edenai.run/v2/audio/speech_to_text_async/a751e20f-9034-4a31-8009-d4b9601d8752');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
			    "authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoiZjZhOWNiNTEtNjBlYy00ZjczLWE3NTktMDllMzFhZmQ4ZWI3IiwidHlwZSI6ImFwaV90b2tlbiJ9.ct5ZHDM2-iFDCCJbML0F4N5U5jFhPCvkDuqG6xXxzBc",
			    'content-type: application/json',
			]);
			
			$response = curl_exec($ch);
			
			curl_close($ch);
			
			$result = json_decode($response);
			
			if($result->status == 'finished') {
				$booking_object = Booking::where('id', $booking['id'])->first();
		        $booking_object->meeting_text = $result->results->assembly->text;
		        $booking_object->save();
			}
        }
        
        if($booking['meeting_text'] && $booking['summary'] == '') {
	        $jsonString = json_encode(array(
			  "providers" => "openai",
			  "text" => 'Make me a summary of what is being told here in this discussion. Summarise and also add the most important notions from the meeting in a list at the bottom: '.$booking['meeting_text'].' "}]',
			  "max_tokens" => 1000,
			  "temperature" => 0.3,
			  "fallback_providers" => ""
			));
	
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://api.edenai.run/v2/text/generation');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
			    "authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoiZjZhOWNiNTEtNjBlYy00ZjczLWE3NTktMDllMzFhZmQ4ZWI3IiwidHlwZSI6ImFwaV90b2tlbiJ9.ct5ZHDM2-iFDCCJbML0F4N5U5jFhPCvkDuqG6xXxzBc",
			    'content-type: application/json',
			]);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonString);
			
			$response = curl_exec($ch);
			$result = json_decode($response);
			
			curl_close($ch);
			
			if(isset($result->openai->generated_text)) {
				$summary = trim($result->openai->generated_text);
				
				$booking_object = Booking::where('id', $booking['id'])->first();
		        $booking_object->summary = $summary;
		        $booking_object->save();
		    }
	    }

        return response()->json(['booking' => $booking, 'result' => $result ], 200);
    }
    
    public function update_reason(Request $request, $id) {
	    $booking = Booking::where('id', $id)->first();
	    $booking->refused_reason = $request->refused_reason;
	    $booking->save();
	    
	    return response()->json(['booking' => $booking], 200);
    }
    
    public function update(Request $request, $id)
    {
	    $booking = Booking::where('id', $id)->first();
	    
        $currentStatus = $booking->status;
	    
		$booking->status = $request->status['id'];
		$booking->refused_reason = $request->refused_reason;
		
		$booking->last_mail_send = NULL;
	    
	    if($booking->status == 6) {
		    $booking->finished = 1;
	    } else {
		    $booking->finished = 0;
	    }
	    
	    if($booking->status == 3) {
		    $booking->accepted = 1;
	    }
	    
	    if($booking->status == 2) {
		    $booking->accepted = 0;
	    }
	    
	    if($request->estimate) {
		    $booking->estimate = $request->estimate;
	    }
	    
	    if($request->estimate_extra_details) {
		    $booking->estimate_extra_details = $request->estimate_extra_details;
	    }
	    
	    if($request->speaker_extra_details) {
		    $booking->speaker_extra_details = $request->speaker_extra_details;
	    }
	    
	    if($request->extra_doc) {
		    $booking->extra_doc = $request->extra_doc;
	    }
	    
	    $booking->customer_estimate_lines = json_encode($request->customer_estimate_lines);
	    
	    if($booking->date <> $request->date) {
		    $booking->date_unknown = 0;
	    }
	    
	    if($request->date && $request->date != '') {
	    	$booking->date = $request->date;
	    } else {
		    $booking->date_unknown = 1;
	    }
	    
	    /* Send mails */
	    if($booking->status <> $currentStatus) {
		    if($booking->status == 4) {
			    self::mail_extra_details_rml($booking->id);
	    }
	    }
	    
	    $booking->date_hour_start = $request->date_hour_start;
	    $booking->date_hour_end = $request->date_hour_end;
	    
	    $booking->last_action_admin = time();
	    
	    if($request->invoice_company) {
		    $booking->invoice_company = $request->invoice_company;
	    }
	    
	    if($request->invoice_name) {
		    $booking->invoice_name = $request->invoice_name;
	    }
	    
	    if($request->amount_of_visitors) {
		    $booking->amount_of_visitors = $request->amount_of_visitors;
	    }
	    
	    if($request->remark) {
		    $booking->remark = $request->remark;
	    }
	    
	    if($request->invoice_address) {
		    $booking->invoice_address = $request->invoice_address;
	    }
	    
	    if($request->invoice_postal) {
		    $booking->invoice_postal = $request->invoice_postal;
	    }
	    
	    if($request->invoice_city) {
		    $booking->invoice_city = $request->invoice_city;
	    }
	    
	    if($request->email) {
		    $booking->email = $request->email;
	    }
	    
	    if($request->mobile) {
		    $booking->mobile = $request->mobile;
	    }
	    
	    if($request->extra_json) {
			$booking->extra_info_json = json_encode($request->extra_json);    
	    }
	    
	    if($request->notes) {
			$booking->notes = $request->notes;    
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
	    
	    if($request->has('customer_refused_reason_id')) {
		    if(is_array($request->customer_refused_reason_id) && isset($request->customer_refused_reason_id['id'])) {
				$booking->customer_refused_reason_id = $request->customer_refused_reason_id['id'];
		    } else if(is_string($request->customer_refused_reason_id) && $request->customer_refused_reason_id !== '') {
				$booking->customer_refused_reason_id = $request->customer_refused_reason_id;
		    } else {
			    $booking->customer_refused_reason_id = null;
		    }
	    }
	    
	    if($request->has('customer_refused_reason')) {
		    $booking->customer_refused_reason = $request->customer_refused_reason;
	    }
	    
	    $booking->save();
	    
        $booking = Booking::where('id', $id)->first()->toArray();
        
        // Get the array of asset IDs that should be active from the request and inactivate the others
	    $newAssetsArray = $request->assets;
	    $newAssets = array();
	    
	    foreach($newAssetsArray as $newAsset) {
		    if(isset($newAsset['asset_id'])) {
		    	$newAssets[] = $newAsset['asset_id'];
		    } else {
		    	$newAssets[] = $newAsset['id'];
		    }
	    }
	
	    $currentAssets = BookingsAsset::where('booking_id', $booking['id'])->where('booking_active', 1)->where('customer_active', 1)->pluck('asset_id')->toArray();
	    $currentInactiveAssets = BookingsAsset::where('booking_id', $booking['id'])->where('booking_active', 0)->pluck('asset_id')->toArray();
	
	    $assetsToAdd = array_diff($newAssets, $currentAssets);
	    $assetsToInactivate = array_diff($currentAssets, $newAssets);
	
	    // Start a transaction to ensure data consistency
	    DB::transaction(function () use ($booking, $assetsToAdd, $assetsToInactivate, $newAssets, $currentInactiveAssets) {
	        // Inactivate the old assets
	        if (!empty($assetsToInactivate)) {
	            BookingsAsset::where('booking_id', $booking['id'])->whereIn('asset_id', $assetsToInactivate)->update(['booking_active' => 0, 'customer_active' => 0]);
	        }
	
	        // Add new assets and set them as active
	        foreach ($assetsToAdd as $assetId) {
		        if(in_array($assetId, $currentInactiveAssets)) {
			        BookingsAsset::where('booking_id', $booking['id'])->where('asset_id', $assetId)->update(['booking_active' => 1, 'customer_active' => 1]);
		        } else {
			        BookingsAsset::insert([
		                'booking_id' => $booking['id'],
		                'asset_id' => $assetId,
		                'booking_active' => 1,
		                'customer_active' => 1,
		            ]);
		        }
	        }
	
	        // For assets that are already present in the $newAssets but might have been deactivated, reactivate them
	        BookingsAsset::where('booking_id', $booking['id'])->whereIn('asset_id', $newAssets)->update(['booking_active' => 1, 'customer_active' => 1]);
	    });
	    
	    /* Insert the estimate in there */
	    
	    foreach($request->assets as $booking_asset) {		    
		    if(isset($booking_asset['asset_id'])) {
		    	BookingsAsset::where('booking_id', $booking['id'])->where('asset_id', $booking_asset['asset_id'])->update(['estimate' => json_encode($booking_asset['estimate'])]);
		    }
	    }
        
        $asset = Asset::where('id', $booking['asset_id'])->first();
        
        $booking['asset'] = $asset;
        $booking['date'] = date('d-m-Y', strtotime($booking['date']));
        
        switch($booking['status']) {
	        case 1:
	        	$booking['status'] = array(
		        	'id' => 1,
		        	'name' => '1. Request'
	        	);
	        	break;
	        case 2:
	        	$booking['status'] = array(
		        	'id' => 2,
		        	'name' => 'Booking refused'
	        	);
	        	break;
	        case 3:
	        	$booking['status'] = array(
		        	'id' => 3,
		        	'name' => '5. Speaker answer'
	        	);
	        	break;
	        case 4:
	        	$booking['status'] = array(
		        	'id' => 4,
		        	'name' => '6. Callsheet info'
	        	);
	        	break;
	        case 6:
	        	$booking['status'] = array(
		        	'id' => 6,
		        	'name' => '8. Final'
	        	);
	        	break;
	        case 10:
	        	$booking['status'] = array(
		        	'id' => 10,
		        	'name' => '2. Offer'
	        	);
	        	break;
	        case 11:
	        	$booking['status'] = array(
		        	'id' => 11,
		        	'name' => '3. Accepted offer'
	        	);
	        	break;
	        case 12:
	        	$booking['status'] = array(
		        	'id' => 12,
		        	'name' => '4. Agenda check'
	        	);
	        	break;
	        case 80:
	        	$booking['status'] = array(
		        	'id' => 80,
		        	'name' => '2.1 Extra info needed'
	        	);
	        	break;
	        case 81:
	        	$booking['status'] = array(
		        	'id' => 81,
		        	'name' => '2.3 Call'
	        	);
	        	break;
	        case 82:
	        	$booking['status'] = array(
		        	'id' => 82,
		        	'name' => '2.4 On hold'
	        	);
	        	break;
	        case 83:
	        	$booking['status'] = array(
		        	'id' => 83,
		        	'name' => '2.2 Offer resend'
	        	);
	        	break;
	        case 99:
	        	$booking['status'] = array(
		        	'id' => 99,
		        	'name' => 'Cancelled'
	        	);
	        	break;
        }
        
        /* If new speaker is set, duplicate and start a new booking */
	    if($request->newspeaker && isset($request->newspeaker['id'])) {
		    $booking = Booking::where('id', $id)->first();
		    $newBooking = $booking->replicate();
		    $newBooking->status = 1;
		    $newBooking->asset_id = $request->newspeaker['id'];
		    $newBooking->accepted = 0;
		    $newBooking->finished = 0;
		    $newBooking->customer_viewed = 0;
		    $newBooking->customer_accepted = 0;
		    $newBooking->customer_estimate_lines = '';
		    $newBooking->save();
		    
		    $asset = Asset::where('id', $newBooking->asset_id)->first();
	    }

        return response()->json(['booking' => $booking], 200);
    }
    
    public function extractMimeAndData($dataUri) {
	    $parts = explode(',', $dataUri);
	    if (count($parts) === 2) {
	        $header = $parts[0];
	        $data = $parts[1];
	        $matches = [];
	        if (preg_match('/^data:(.*?);base64/', $header, $matches)) {
	            return [
	                'mime' => $matches[1],
	                'data' => $data,
	            ];
	        }
	    }
	    return null;
	}
	
	public function unclaimed_count() {
	    $customer = \Request::header('Customer');
	    
	    $customer_settings = CustomerSetting::where('customer_id', $customer)->first();
	    
	    if($customer_settings->booking_flow == 2) {
		    $today = Carbon::now()->format('Y-m-d');
		        	
		    $bookingsCount = Booking::where('customer_id', $customer)
			    ->where('paid', 1)
			    ->where('customer_refused', 0)
			    ->where(DB::raw("STR_TO_DATE(date, '%d-%m-%Y')"), '>=', $today)
			    ->whereIn('status', [3]) // Exclude statuses 2, 6, and 99
			    ->count();
		} else {	    
			$bookingsCount = Booking::where('customer_id', $customer)
				->whereNull('managing_user_id')
			    ->where('paid', 1)
			    ->where('customer_refused', 0)
			    ->whereNotIn('status', [2, 6, 99, 90]) // Exclude statuses 2, 6, and 99
			    ->count();
			}
    	
    	return response()->json(['unreadCountBookings' => $bookingsCount], 200);
	}
    
    public function claim_booking($id)
    {
        $booking = Booking::where('id', $id)->first();
        $user = User::find(Auth::user()->id);
        
        $words = explode(' ', $user->name);
	    $initials = '';
	    
	    // Extract the first letter of each word
	    foreach ($words as $word) {
	        $initials .= strtoupper(substr($word, 0, 1));
	    }
	    
	    $booking->managing_user_initials = $initials;
	    $booking->managing_user_id = Auth::user()->id;
	    
		$booking->last_action_admin = time();
	    
	    $booking->save();        

        return response()->json(['booking' => $booking], 200);
	}
    
    public function finish_booking($id)
    {
        $booking = Booking::where('id', $id)->first();
        
        /* Mail to owner to send_mail_extra_details */
        $to = $booking->email;
        
        $user = User::find($booking->managing_user_id);
        $reply_to = $user->email;
        
        $booking_assets = BookingsAsset::where('booking_id', $id)->where('refused', 0)->where('booking_active', 1)->where('customer_active', 1)->get(); /* Here we need to add an extra check */
		    
		foreach($booking_assets as $booking_asset) {	
		    $asset = Asset::where('id', $booking_asset->asset_id)->first();
		
	        $customer_settings = CustomerSetting::where('customer_id', $asset->customer_id)->first();
	        
	        $mailsettings = MailSettings::first();
	        $subject = $mailsettings->mail_final_booking_subject_nl.' Ref: #'.$booking->id;
	        $mail_text = '<div style="text-align: left;">'.nl2br($mailsettings->mail_final_booking_text_nl).'</div>';
	        $mail_text .= '<br>';				
			$mail_text .= '<br>';				
			$mail_text .= '<p>Fijne dag alvast!</p>';
			$mail_text .= '<p>'.$customer_settings->application_name.'</p>';
		    
		    $htmlMessage = view('mail', compact('mail_text', 'user', 'customer_settings'))->render();
	        
	        // Generate iCal content
	        $date = $booking->date;
			$start_time = $date.' '.$booking->date_hour_start;
			$end_time = $date.' '.$booking->date_hour_end;
			    
			$icalContent = Calendar::create('Event '.$booking->invoice_company.' Read My Lips Ref: #'.$id)
			    ->event(
			        Event::create('Event '.$booking->invoice_company.' Read My Lips Ref: #'.$id)
			            ->startsAt(new DateTime($start_time))
			            ->endsAt(new DateTime($end_time))
			    )
			    ->get();
			
			// Save iCal content to a file
			$fileName = 'event.ics'; // Choose a file name
			Storage::put($fileName, $icalContent);
			
			if($asset->email) {
			    $cc = $asset->email;
		    } else {
			    $cc = 'contact@readmylips.be';
		    }
			
			// Send email with attachment
			if($asset->email_assistant <> '') {
				$array_cc[] = $cc;
				$array_cc[] = $asset->email_assistant;
				
				$cc = $array_cc;
			}
			
			$fromName = $customer_settings->application_name;
			$name = strtolower($fromName);
			$name = preg_replace('/[^a-z0-9]/', '', $name);
			//$fromEmail = $name . '@mlbx.email';
			$fromEmail = 'contact@readmylips.be';
			
			Mail::send([], [], function ($message) use ($to, $cc, $subject, $htmlMessage, $fileName, $reply_to, $fromEmail, $fromName) {
			    $message->to($to)
			    		->replyTo($reply_to)
			            ->cc($cc)
			            ->from($fromEmail, $fromName)
			            ->subject($subject)
			            ->html($htmlMessage, 'text/html') // Set the content type to HTML
			            ->attach(Storage::path($fileName), ['as' => 'event.ics', 'mime' => 'text/calendar']);
			});
		}
		
        $booking->status = 6;
        		    
	    $booking->last_mail_send = time();
	    $booking->last_action_admin = time();
		    
        $booking->save();
        
        /* Fetch booking new */
        $booking = Booking::where('id', $id)->first()->toArray();
        
        $asset = Asset::where('id', $booking['asset_id'])->first();
        
        $booking['asset'] = $asset;
        $booking['date'] = date('d-m-Y', strtotime($booking['date']));
        
        switch($booking['status']) {
	        case 1:
	        	$booking['status'] = array(
		        	'id' => 1,
		        	'name' => '1. Request'
	        	);
	        	break;
	        case 2:
	        	$booking['status'] = array(
		        	'id' => 2,
		        	'name' => 'Booking refused'
	        	);
	        	break;
	        case 3:
	        	$booking['status'] = array(
		        	'id' => 3,
		        	'name' => '5. Speaker answer'
	        	);
	        	break;
	        case 4:
	        	$booking['status'] = array(
		        	'id' => 4,
		        	'name' => '6. Callsheet info'
	        	);
	        	break;
	        case 6:
	        	$booking['status'] = array(
		        	'id' => 6,
		        	'name' => '8. Final'
	        	);
	        	break;
	        case 10:
	        	$booking['status'] = array(
		        	'id' => 10,
		        	'name' => '2. Offer'
	        	);
	        	break;
	        case 11:
	        	$booking['status'] = array(
		        	'id' => 11,
		        	'name' => '3. Accepted offer'
	        	);
	        	break;
	        case 12:
	        	$booking['status'] = array(
		        	'id' => 12,
		        	'name' => '4. Agenda check'
	        	);
	        	break;
	        case 80:
	        	$booking['status'] = array(
		        	'id' => 80,
		        	'name' => '2.1 Extra info needed'
	        	);
	        	break;
	        case 81:
	        	$booking['status'] = array(
		        	'id' => 81,
		        	'name' => '2.3 Call'
	        	);
	        	break;
	        case 82:
	        	$booking['status'] = array(
		        	'id' => 82,
		        	'name' => '2.4 On hold'
	        	);
	        	break;
	        case 83:
	        	$booking['status'] = array(
		        	'id' => 83,
		        	'name' => '2.2 Offer resend'
	        	);
	        	break;
	        case 99:
	        	$booking['status'] = array(
		        	'id' => 99,
		        	'name' => 'Cancelled'
	        	);
	        	break;
        }

        return response()->json(['booking' => $booking], 200);
	}
    
    public function mail_estimate_rml($id, $status = 10)
    {
        $booking = Booking::where('id', $id)->first();
        
        /* Mail to owner to send_mail_extra_details */
        $to = $booking->email;
        
        if($booking->managing_user_id) {
        	$user = User::find($booking->managing_user_id);
        } else {
        	$user = User::find(Auth::user()->id);
        }
        
        $reply_to = $user->email;
	    
	    $asset = Asset::where('id', $booking->asset_id)->first();
	    $customer_settings = CustomerSetting::where('customer_id', $asset->customer_id)->first();
	    
	    $mailsettings = MailSettings::first();
	    
	    if($status == 83) {
	    	$mailtext = nl2br($mailsettings->mail_reminder_estimate_text_nl);
	    } else {
	    	$mailtext = nl2br($mailsettings->mail_estimate_text_nl);
	    }

		// Define the string to substitute for [{offer_link}]
		$substitute_link = '<p>Klik hier om de offerte te <a href="https://offer.readmylips.be/booking/'.$booking->id.'/ZXN0aW1hdGU=">openen</a>.</p>';
		
		// Check if [{offer_link}] exists in $mailtext
		if (strpos($mailtext, '[{offer_link}]') !== false) {
		    // Substitute [{offer_link}] with the actual link
		    $mailtext = str_replace('[{offer_link}]', $substitute_link, $mailtext);
		} else {
		    // Append the actual link at the end if [{offer_link}] is not found
		    $mailtext .= $substitute_link;
		}
		
		// Define the string to substitute for [{extra_details}]
		$substitute_extra_details = '<div style="text-align: left;">'.nl2br($booking->estimate_extra_details).'</div>';
		
		// Check if [{offer_link}] exists in $mailtext
		if (strpos($mailtext, '[{extra_details}]') !== false) {
		    // Substitute [{extra_details}] with the actual text
		    $mailtext = str_replace('[{extra_details}]', $substitute_extra_details, $mailtext);
		} else {
		    // Append the actual link at the end if [{extra_details}] is not found
		    $mailtext .= $substitute_extra_details;
		}
	    
	    if($status == 83) {
        	$subject = $mailsettings->mail_reminder_estimate_subject_nl.' Ref: #'.$booking->id;
        } else {
	    	$subject = $mailsettings->mail_estimate_subject_nl.' Ref: #'.$booking->id;    
        }
        
        $mail_text = '<div style="text-align: left;">'.$mailtext.'</div>';
		$mail_text .= '<br>';				
		$mail_text .= '<br>';				
		$mail_text .= '<p>Fijne dag alvast!</p>';
		$mail_text .= '<p>'.$customer_settings->application_name.'</p>';
	    
	    $htmlMessage = view('mail', compact('mail_text', 'user', 'customer_settings'))->render();
	    
	    $fromName = $customer_settings->application_name;
		$name = strtolower($fromName);
		$name = preg_replace('/[^a-z0-9]/', '', $name);
		//$fromEmail = $name . '@mlbx.email';
		$fromEmail = 'contact@readmylips.be';
        
        // Attach both 'estimate' and 'extra_doc' files to the email
        if($to) {
			Mail::send([], [], function ($message) use ($to, $subject, $htmlMessage, $reply_to, $fromEmail, $fromName) {
	            $message->to($to)
	            	->replyTo($reply_to)
	                ->from($fromEmail, $fromName)
	                ->subject($subject)
	                ->html($htmlMessage, 'text/html');
		    });
		    
		    $booking->last_mail_send = time();
	    }
		
		$booking->last_action_admin = time();
		
	    $booking->customer_accepted = 0;
	    $booking->customer_refused = 0;
	    
	    if($status == 10) {
		    if($booking->status == 1) {
			    $booking->status = 10;
		    } else {
			    $booking->status = 83;
		    }
	    } else {
			$booking->status = $status;   
	    }
        
        $booking->booking_seen = 1;
        $booking->save();
        
        /* Fetch booking new */
        $booking = Booking::where('id', $id)->first()->toArray();
        
        $asset = Asset::where('id', $booking['asset_id'])->first();
        
        $booking['asset'] = $asset;
        $booking['date'] = date('d-m-Y', strtotime($booking['date']));
        
        switch($booking['status']) {
	        case 1:
	        	$booking['status'] = array(
		        	'id' => 1,
		        	'name' => '1. Request'
	        	);
	        	break;
	        case 2:
	        	$booking['status'] = array(
		        	'id' => 2,
		        	'name' => 'Booking refused'
	        	);
	        	break;
	        case 3:
	        	$booking['status'] = array(
		        	'id' => 3,
		        	'name' => '5. Speaker answer'
	        	);
	        	break;
	        case 4:
	        	$booking['status'] = array(
		        	'id' => 4,
		        	'name' => '6. Callsheet info'
	        	);
	        	break;
	        case 6:
	        	$booking['status'] = array(
		        	'id' => 6,
		        	'name' => '8. Final'
	        	);
	        	break;
	        case 10:
	        	$booking['status'] = array(
		        	'id' => 10,
		        	'name' => '2. Offer'
	        	);
	        	break;
	        case 11:
	        	$booking['status'] = array(
		        	'id' => 11,
		        	'name' => '3. Accepted offer'
	        	);
	        	break;
	        case 12:
	        	$booking['status'] = array(
		        	'id' => 12,
		        	'name' => '4. Agenda check'
	        	);
	        	break;
	        case 80:
	        	$booking['status'] = array(
		        	'id' => 80,
		        	'name' => '2.1 Extra info needed'
	        	);
	        	break;
	        case 81:
	        	$booking['status'] = array(
		        	'id' => 81,
		        	'name' => '2.3 Call'
	        	);
	        	break;
	        case 82:
	        	$booking['status'] = array(
		        	'id' => 82,
		        	'name' => '2.4 On hold'
	        	);
	        	break;
	        case 83:
	        	$booking['status'] = array(
		        	'id' => 83,
		        	'name' => '2.2 Offer resend'
	        	);
	        	break;
	        case 99:
	        	$booking['status'] = array(
		        	'id' => 99,
		        	'name' => 'Cancelled'
	        	);
	        	break;
        }

        return response()->json(['booking' => $booking], 200);
    }
    
    public function remove($id) {
	    $booking = Booking::where('id', $id)->first();
        $booking->status = 90;
        $booking->save();
    }
    
    public function mail_extra_details_rml($id)
    {
        $booking = Booking::where('id', $id)->first();
        
        $booking->customer_accepted = 0;
        $booking->customer_refused = 0;
        $booking->last_mail_send = time();
        $booking->last_action_admin = time();
        
        $booking->save();
        
        /* Mail to owner to send_mail_extra_details */
        $to = $booking->email;
        
        $customer_settings = CustomerSetting::where('customer_id', $booking->customer_id)->first();
        
        $mailsettings = MailSettings::first();
        $subject = $mailsettings->mail_extra_subject_nl.' Ref: #'.$booking->id;
        
        $mailtext = nl2br($mailsettings->mail_extra_text_nl);
        
        // Define the string to substitute for [{offer_link}]
		$substitute_link = '<p>Klik hier om de offerte te <a href="https://offer.readmylips.be/booking/'.$booking->id.'/ZXN0aW1hdGU=">openen</a>.</p>';
		
		// Check if [{offer_link}] exists in $mailtext
		if (strpos($mailtext, '[{offer_link}]') !== false) {
		    // Substitute [{offer_link}] with the actual link
		    $mailtext = str_replace('[{offer_link}]', $substitute_link, $mailtext);
		} else {
		    // Append the actual link at the end if [{offer_link}] is not found
		    $mailtext .= $substitute_link;
		}
		
		// Define the string to substitute for [{extra_details}]
		$substitute_extra_details = '<div style="text-align: left;">'.nl2br($booking->estimate_extra_details).'</div>';
		
		// Check if [{offer_link}] exists in $mailtext
		if (strpos($mailtext, '[{extra_details}]') !== false) {
		    // Substitute [{extra_details}] with the actual text
		    $mailtext = str_replace('[{extra_details}]', $substitute_extra_details, $mailtext);
		} else {
		    // Append the actual link at the end if [{extra_details}] is not found
		    $mailtext .= $substitute_extra_details;
		}
        
        $mail_text = '<div style="text-align: left;">'.$mailtext.'</div>';	
		$mail_text .= '<br>';				
		$mail_text .= '<br>';				
		$mail_text .= '<p>Fijne dag alvast!</p>';
		$mail_text .= '<p>'.$customer_settings->application_name.'</p>';
	    
	    $htmlMessage = view('mail', compact('mail_text', 'customer_settings'))->render();
	    
	    $user = User::find(Auth::user()->id);
        $reply_to = $user->email;
        
        $fromName = $customer_settings->application_name;
		$name = strtolower($fromName);
		$name = preg_replace('/[^a-z0-9]/', '', $name);
		//$fromEmail = $name . '@mlbx.email';
		$fromEmail = 'contact@readmylips.be';
        
        // Attach both 'estimate' and 'extra_doc' files to the email
        Mail::send([], [], function ($message) use ($to, $subject, $htmlMessage, $reply_to, $fromEmail, $fromName) {
            $message->to($to)
            	->replyTo($reply_to)
                ->subject($subject)
                ->from($fromEmail, $fromName)
                ->html($htmlMessage, 'text/html');
        });
		
        $booking->status = 4;
        $booking->booking_seen = 0;
        $booking->save();
        
        /* Fetch booking new */
        $booking = Booking::where('id', $id)->first()->toArray();
        
        $asset = Asset::where('id', $booking['asset_id'])->first();
        
        $booking['asset'] = $asset;
        $booking['date'] = date('d-m-Y', strtotime($booking['date']));
        
        switch($booking['status']) {
	        case 1:
	        	$booking['status'] = array(
		        	'id' => 1,
		        	'name' => '1. Request'
	        	);
	        	break;
	        case 2:
	        	$booking['status'] = array(
		        	'id' => 2,
		        	'name' => 'Booking refused'
	        	);
	        	break;
	        case 3:
	        	$booking['status'] = array(
		        	'id' => 3,
		        	'name' => '5. Speaker answer'
	        	);
	        	break;
	        case 4:
	        	$booking['status'] = array(
		        	'id' => 4,
		        	'name' => '6. Callsheet info'
	        	);
	        	break;
	        case 6:
	        	$booking['status'] = array(
		        	'id' => 6,
		        	'name' => '8. Final'
	        	);
	        	break;
	        case 10:
	        	$booking['status'] = array(
		        	'id' => 10,
		        	'name' => '2. Offer'
	        	);
	        	break;
	        case 11:
	        	$booking['status'] = array(
		        	'id' => 11,
		        	'name' => '3. Accepted offer'
	        	);
	        	break;
	        case 12:
	        	$booking['status'] = array(
		        	'id' => 12,
		        	'name' => '4. Agenda check'
	        	);
	        	break;
	        case 80:
	        	$booking['status'] = array(
		        	'id' => 80,
		        	'name' => '2.1 Extra info needed'
	        	);
	        	break;
	        case 81:
	        	$booking['status'] = array(
		        	'id' => 81,
		        	'name' => '2.3 Call'
	        	);
	        	break;
	        case 82:
	        	$booking['status'] = array(
		        	'id' => 82,
		        	'name' => '2.4 On hold'
	        	);
	        	break;
	        case 83:
	        	$booking['status'] = array(
		        	'id' => 83,
		        	'name' => '2.2 Offer resend'
	        	);
	        	break;
	        case 99:
	        	$booking['status'] = array(
		        	'id' => 99,
		        	'name' => 'Cancelled'
	        	);
	        	break;
        }

        return response()->json(['booking' => $booking], 200);
    }
    
    public function mail_date_request($id)
    {
		$booking = Booking::where('id', $id)->first();
	    $asset = Asset::where('id', $booking->asset_id)->first();
	    
	    $booking_assets = BookingsAsset::where('booking_id', $id)->where('bookings_assets.booking_active', 1)->where('bookings_assets.accepted', 0)->where('bookings_assets.refused', 0)->where('bookings_assets.booking_active', 1)->where('bookings_assets.customer_active', 1)->join('assets', 'assets.id', 'asset_id')->get();
		    
		foreach($booking_assets as $booking_asset) {	
		    $asset = Asset::where('id', $booking_asset->asset_id)->first();
		    
		    if($asset->email) {
			    $to = $asset->email;
			    $cc = 'contact@readmylips.be';
		    } else {
			    $to = 'contact@readmylips.be';
			    $cc = false;
		    }
	        
	        $user = User::find(Auth::user()->id);
	        $reply_to = $user->email;
	
	        // HTML content for the email
	        $mailsettings = MailSettings::first();
	        $mailtext = nl2br($mailsettings->mail_newbooking_text_nl);
	        
	        /* Substitute offer*/
			$substitute_link = '<p>Klik hier om de boeking te <a href="https://offer.readmylips.be/booking/'.$booking->id.'/cmVxdWVzdA==/'.$booking_asset->asset_id.'">openen</a>.</p>';
			
			// Check if [{offer_link}] exists in $mailtext
			if (strpos($mailtext, '[{offer_link}]') !== false) {
			    // Substitute [{offer_link}] with the actual link
			    $mailtext = str_replace('[{offer_link}]', $substitute_link, $mailtext);
			} else {
			    // Append the actual link at the end if [{offer_link}] is not found
			    $mailtext .= $substitute_link;
			}
	        
	        // Define the string to substitute for [{extra_details}]
			$substitute_extra_details = '<p>Locatie aanvraag: '.$booking->location.'</p>';
	        $substitute_extra_details .= '<p>Aantal bezoekers: '.$booking->amount_of_visitors.'</p>';
	        $substitute_extra_details .= '<p>Beschrijving aanvraag: '.$booking->remark.'</p>';
	        $substitute_extra_details .= '<p>Datum aanvraag: '.date('d-m-Y', strtotime($booking->date)).'</p>';
	        $substitute_extra_details .= '<p>Aanvraag door: '.$booking->invoice_name.'</p>';
	        $substitute_extra_details .= '<p>Adres gegevens: '.$booking->invoice_address.', '.$booking->invoice_postal.' '.$booking->invoice_city.'</p>';
	        $substitute_extra_details .= '<p>Email: '.$booking->invoice_email.'</p>';
	        $substitute_extra_details .= '<p>GSM Nummer: '.$booking->mobile.'</p>';
	        
	        if($booking->speaker_extra_details) {
	        	$substitute_extra_details .= '<p>'.$booking->speaker_extra_details.'</p>';
	        }
			
			// Check if [{offer_link}] exists in $mailtext
			if (strpos($mailtext, '[{extra_details}]') !== false) {
			    // Substitute [{extra_details}] with the actual text
			    $mailtext = str_replace('[{extra_details}]', $substitute_extra_details, $mailtext);
			} else {
			    // Append the actual link at the end if [{extra_details}] is not found
			    $mailtext .= $substitute_extra_details;
			}
	        
	        $mail_text = '<div style="text-align: left;">'.$mailtext.'</div>';
	        
	        $customer_settings = CustomerSetting::where('customer_id', $asset->customer_id)->first();
		    
	        $htmlMessage = view('mail', compact('mail_text', 'user', 'customer_settings'))->render();
		    $subject = $mailsettings->mail_newbooking_subject_nl.' Ref: #'.$booking->id;
		    
		    if($asset->email_assistant) {
			    $array_cc[] = $cc;
			    $array_cc[] = $asset->email_assistant;
			    
			    $cc = $array_cc;
		    }
		    
		    $fromName = $customer_settings->application_name;
			$name = strtolower($fromName);
			$name = preg_replace('/[^a-z0-9]/', '', $name);
			//$fromEmail = $name . '@mlbx.email';
			$fromEmail = 'contact@readmylips.be';
	
			if($cc) {
		        Mail::send([], [], function ($message) use ($to, $subject, $htmlMessage, $cc, $reply_to, $fromEmail, $fromName) {
		            $message->to($to)
		            		->replyTo($reply_to)
		            		->cc($cc)
		                    ->subject($subject)
		                    ->html($htmlMessage, 'text/html'); // Set the content type to HTML
		        });
			} else {
		        Mail::send([], [], function ($message) use ($to, $subject, $htmlMessage, $reply_to, $fromEmail, $fromName) {
		            $message->to($to)
		            		->replyTo($reply_to)
		                    ->subject($subject)
		                    ->html($htmlMessage, 'text/html'); // Set the content type to HTML
		        });
			}
		}
		
		$booking->accepted = 0;
        $booking->status = 12;
        $booking->booking_seen = 0;
        
        $booking->last_mail_send = time();
        $booking->last_action_admin = time();
        
        $booking->save();
        
        /* Fetch booking new */
        $booking = Booking::where('id', $id)->first()->toArray();
        
        $asset = Asset::where('id', $booking['asset_id'])->first();
        
        $booking['asset'] = $asset;
        $booking['date'] = date('d-m-Y', strtotime($booking['date']));
        
        switch($booking['status']) {
	        case 1:
	        	$booking['status'] = array(
		        	'id' => 1,
		        	'name' => '1. Request'
	        	);
	        	break;
	        case 2:
	        	$booking['status'] = array(
		        	'id' => 2,
		        	'name' => 'Booking refused'
	        	);
	        	break;
	        case 3:
	        	$booking['status'] = array(
		        	'id' => 3,
		        	'name' => '5. Speaker answer'
	        	);
	        	break;
	        case 4:
	        	$booking['status'] = array(
		        	'id' => 4,
		        	'name' => '6. Callsheet info'
	        	);
	        	break;
	        case 6:
	        	$booking['status'] = array(
		        	'id' => 6,
		        	'name' => '8. Final'
	        	);
	        	break;
	        case 10:
	        	$booking['status'] = array(
		        	'id' => 10,
		        	'name' => '2. Offer'
	        	);
	        	break;
	        case 11:
	        	$booking['status'] = array(
		        	'id' => 11,
		        	'name' => '3. Accepted offer'
	        	);
	        	break;
	        case 12:
	        	$booking['status'] = array(
		        	'id' => 12,
		        	'name' => '4. Agenda check'
	        	);
	        	break;
	        case 80:
	        	$booking['status'] = array(
		        	'id' => 80,
		        	'name' => '2.1 Extra info needed'
	        	);
	        	break;
	        case 81:
	        	$booking['status'] = array(
		        	'id' => 81,
		        	'name' => '2.3 Call'
	        	);
	        	break;
	        case 82:
	        	$booking['status'] = array(
		        	'id' => 82,
		        	'name' => '2.4 On hold'
	        	);
	        	break;
	        case 83:
	        	$booking['status'] = array(
		        	'id' => 83,
		        	'name' => '2.2 Offer resend'
	        	);
	        	break;
	        case 99:
	        	$booking['status'] = array(
		        	'id' => 99,
		        	'name' => 'Cancelled'
	        	);
	        	break;
        }

        return response()->json(['booking' => $booking], 200);
    }
}
