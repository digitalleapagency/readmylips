<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Hash;

use Auth;

use App\Models\User;
use App\Models\Asset;
use App\Models\Booking;
use App\Models\Meeting;
use App\Models\CustomerSetting;

use Carbon\Carbon;

class StatsController extends Controller
{	
	public function index() {
	    $customer = \Request::header('Customer');
	    
	    // Get the date one year ago from today
        $oneYearAgo = Carbon::now()->subYear();
	    $customer_settings = CustomerSetting::where('customer_id', $customer)->first();

        // Retrieve all bookings made over the last year
        $bookings = Booking::where('customer_id', $customer)
            ->where('created_at', '>', $oneYearAgo)
            ->where('paid', 1)
            ->get();

        // 1. Amount of bookings per asset
        $bookingsPerAsset = $bookings->groupBy('asset_id')->map(function ($group) {
            return $group->count();
        })->sortDesc()->take(10);

        // Get asset titles
        $assets = Asset::whereIn('id', $bookingsPerAsset->keys())->get()->pluck('title', 'id');
        
        $bookingsPerAssetArray = array();
        foreach($bookingsPerAsset as $key => $value) {
	        $bookingsPerAssetArray[$assets[$key]] = $value;
        }

        // 2. Amount of bookings per month
        $bookingsPerMonth = $bookings->groupBy(function ($date) {
            return Carbon::parse($date->created_at)->format('Y-m');
        })->map(function ($group) {
            return $group->count();
        });

        // 3. Amount of bookings per year
        $bookingsPerYear = $bookings->groupBy(function ($date) {
            return Carbon::parse($date->created_at)->format('Y');
        })->map(function ($group) {
            return $group->count();
        });
        
        if($customer_settings->booking_flow == 1) {   
	        // Bookings per status
	        $bookingsPerStatus = array(
		        'New' => 0,
		        'Offer' => 0,
		        'Offer accepted' => 0,
		        'Agenda check' => 0,
		        'Speaker accepted' => 0,
		        'Offer resend' => 0,
		        'To invoice' => 0,
		        'Finished' => 0,
		        'Cancelled' => 0,
		        'Refused booking' => 0,
		        'Refused estimate' => 0,
	        );
	        
	        foreach($bookings as $booking) {
		        if($booking->status == 1) {
			        $bookingsPerStatus['New'] += 1;
		        } else if($booking->status == 2) {
			        $bookingsPerStatus['Refused estimate'] += 1;
		        } else if($booking->customer_refused == 0 && $booking->status == 4) {
			        $bookingsPerStatus['Offer resend'] += 1;
		        } else if($booking->customer_refused == 0 && $booking->status == 10) {
			        $bookingsPerStatus['Offer'] += 1;
		        } else if($booking->customer_refused == 0 && $booking->status == 6) {
			        $bookingsPerStatus['Finished'] += 1;
		        } else if($booking->customer_refused == 0 && $booking->status == 12) {
			        $bookingsPerStatus['Agenda check'] += 1;
		        } else if($booking->customer_refused == 0 && $booking->status == 13) {
			        $bookingsPerStatus['To invoice'] += 1;
		        } else if($booking->customer_refused == 1 && $booking->status != 99) {
			        $bookingsPerStatus['Refused booking'] += 1;
		        } else if($booking->customer_refused == 0 && $booking->customer_accepted == 1 && $booking->status != 2 && $booking->status != 6 && $booking->status != 13 && $booking->status != 99 && $booking->status != 12) {
			        $bookingsPerStatus['Offer accepted'] += 1;
		        } else if($booking->customer_refused == 0 && $booking->status == 3) {
			        $bookingsPerStatus['Speaker accepted'] += 1;
		        } else if($booking->status == 99) {
			        $bookingsPerStatus['Cancelled'] += 1;
		        }
	        }
	    } else {
		    $bookingsPerStatus = array(
		        'New' => 0,
		        'Summarized' => 0,
		        'Finished' => 0,
	        );
	        
	        foreach($bookings as $booking) {
	        	$meeting = Meeting::where('booking_id', $booking->id)->first();
		        
		        if($booking->status == 1) {
			        $bookingsPerStatus['New'] += 1;
		        } else if($booking->customer_refused == 0 && $booking->status == 6) {
			        $bookingsPerStatus['Finished'] += 1;
		        } 
		        
		        if($meeting) {
			        if($meeting->summarized == 1) {
				        $bookingsPerStatus['Summarized'] += 1;
			        }
		        } 
		    }
	    }

        // 4. Bookings that got accepted
        $acceptedBookingsCount = $bookings->where('status', 'accepted')->count();

        // 5. Bookings that got accepted by customer
        $customerAcceptedBookingsCount = $bookings->where('customer_accepted', true)->count();

        // 6. Bookings per user (managing_user_credentials)
        $bookingsPerUser = $bookings->groupBy('managing_user_initials')->map(function ($group) {
            return $group->count();
        });

        return response()->json([
            'bookingsPerAsset' => $bookingsPerAssetArray,
            'bookingsPerStatus' => $bookingsPerStatus,
            'assets' => $assets,
            'bookingsPerMonth' => $bookingsPerMonth,
            'bookingsPerYear' => $bookingsPerYear->sum(),
            'acceptedBookingsCount' => $acceptedBookingsCount,
            'customerAcceptedBookingsCount' => $customerAcceptedBookingsCount,
            'bookingsPerUser' => $bookingsPerUser,
        ]);
	}
}
