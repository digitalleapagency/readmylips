<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use App\Models\CustomerSetting;
use App\Models\Customer;

class CustomerSettingController extends Controller
{
    public function index()
    {		
	    $customer = \Request::header('Customer');
	    
        $settings = CustomerSetting::where('customer_id', $customer)->first();
        
        switch($settings->sorting) {
	        case 1:
	        	$settings->sorting = array('id' => 1, 'name' => 'Alfabetisch (A-Z)');
	        	break;
	        	
	        case 2:
	        	$settings->sorting = array('id' => 2, 'name' => 'Alfabetisch (Z-A)');
	        	break;
	        	
	        case 3:
	        	$settings->sorting = array('id' => 3, 'name' => 'Random');
	        	break;
        }
        
        switch($settings->booking_flow) {
	        case 1:
	        	$settings->booking_flow = array('id' => 1, 'name' => 'RML');
	        	break;
	        	
	        case 2:
	        	$settings->booking_flow = array('id' => 2, 'name' => 'Xpertbooking');
	        	break;
        }
        
        switch($settings->change_price) {
	        case 1:
	        	$settings->change_price = array('id' => 1, 'name' => 'Yes');
	        	break;
	        	
	        case 0:
	        	$settings->change_price = array('id' => 0, 'name' => 'No');
	        	break;
        }
        
        switch($settings->change_categories) {
	        case 1:
	        	$settings->change_categories = array('id' => 1, 'name' => 'Yes');
	        	break;
	        	
	        case 0:
	        	$settings->change_categories = array('id' => 0, 'name' => 'No');
	        	break;
        }
        
        switch($settings->check_explicit_content) {
	        case 1:
	        	$settings->check_explicit_content = array('id' => 1, 'name' => 'Yes');
	        	break;
	        	
	        case 0:
	        	$settings->check_explicit_content = array('id' => 0, 'name' => 'No');
	        	break;
        }
        
        switch($settings->change_languages) {
	        case 1:
	        	$settings->change_languages = array('id' => 1, 'name' => 'Yes');
	        	break;
	        	
	        case 0:
	        	$settings->change_languages = array('id' => 0, 'name' => 'No');
	        	break;
        }
        
        switch($settings->show_slider) {
	        case 1:
	        	$settings->show_slider = array('id' => 1, 'name' => 'Yes');
	        	break;
	        	
	        case 0:
	        	$settings->show_slider = array('id' => 0, 'name' => 'No');
	        	break;
        }
        
        switch($settings->show_pricing) {
	        case 1:
	        	$settings->show_pricing = array('id' => 1, 'name' => 'Yes');
	        	break;
	        	
	        case 0:
	        	$settings->show_pricing = array('id' => 0, 'name' => 'No');
	        	break;
        }
        
        switch($settings->show_language) {
	        case 1:
	        	$settings->show_language = array('id' => 1, 'name' => 'Yes');
	        	break;
	        	
	        case 0:
	        	$settings->show_language = array('id' => 0, 'name' => 'No');
	        	break;
        }
        
        switch($settings->show_rating) {
	        case 1:
	        	$settings->show_rating = array('id' => 1, 'name' => 'Yes');
	        	break;
	        	
	        case 0:
	        	$settings->show_rating = array('id' => 0, 'name' => 'No');
	        	break;
        }
        
        switch($settings->show_keynotes) {
	        case 1:
	        	$settings->show_keynotes = array('id' => 1, 'name' => 'Yes');
	        	break;
	        	
	        case 0:
	        	$settings->show_keynotes = array('id' => 0, 'name' => 'No');
	        	break;
        }
        
        switch($settings->markup_type) {
	        case 1:
	        	$settings->markup_type = array('id' => 1, 'name' => '€');
	        	break;
	        	
	        case 2:
	        	$settings->markup_type = array('id' => 2, 'name' => '%');
	        	break;
        }
        
        switch($settings->change_timespace) {
	        case 1:
	        	$settings->change_timespace = array('id' => 1, 'name' => 'Yes');
	        	break;
	        	
	        case 0:
	        	$settings->change_timespace = array('id' => 0, 'name' => 'No');
	        	break;
        }
        
        if($settings->default_timespace >= 60) {
	        $settings->default_timespace = array(
	        	'id' => $settings->default_timespace,
	        	'name' => $settings->default_timespace.' min'
	        );
	    } else {
		    $hours = $settings->default_timespace/60;
	        $settings->default_timespace = array(
	        	'id' => $settings->default_timespace,
	        	'name' => $hours.' hour(s)'
	        );
	    }
        
        $settings->reminder_for_estimate = array('id' => $settings->reminder_for_estimate, 'name' => $settings->reminder_for_estimate.'h');
        $settings->reminder_for_booking = array('id' => $settings->reminder_for_booking, 'name' => $settings->reminder_for_booking.'h');
        
        $settings->default_estimate_lines = json_decode($settings->default_estimate_lines);
        
        $settings->refused_reason_options = self::decode_refused_reason_options($settings->refused_reason_options);
        $settings->source_options = self::decode_source_options($settings->source_options);
        
        return response()->json(['settings' => $settings], 200);
    }
    
    public static function decode_refused_reason_options($raw) {
        $decoded = json_decode($raw, true);
        if (is_array($decoded) && count($decoded) > 0) {
            return array_values($decoded);
        }
        return [
            ['id' => 'budget', 'name' => 'Budget'],
            ['id' => 'event_cancelled', 'name' => 'Event afgelast'],
            ['id' => 'internal_speaker', 'name' => 'Interne spreker/moderator'],
            ['id' => 'other', 'name' => 'Andere'],
        ];
    }
    
    public static function decode_source_options($raw) {
        $decoded = json_decode($raw, true);
        if (is_array($decoded) && count($decoded) > 0) {
            return array_values($decoded);
        }
        return [
            ['id' => 'google', 'name' => 'Google/internet'],
            ['id' => 'linkedin', 'name' => 'LinkedIn'],
            ['id' => 'instagram', 'name' => 'Instagram'],
            ['id' => 'event', 'name' => 'Event/beurs'],
            ['id' => 'samenwerking', 'name' => 'Eerdere samenwerking'],
            ['id' => 'aanbeveling', 'name' => 'Aanbeveling'],
        ];
    }
    
    public function type(Request $request) {
	    $customer = \Request::header('Customer');
	    
        $settings = CustomerSetting::where('customer_id', $customer)->first();
	    
	    return response()->json(['type' => $settings->booking_flow], 200);
    }
    
    public function update(Request $request) {	
	    $customer = \Request::header('Customer');
	    
        $settings = CustomerSetting::where('customer_id', $customer)->first();
	    
	    $settings->booking_flow = $request->booking_flow['id'];
	    $settings->sorting = $request->sorting['id'];
	    $settings->asset_label_single = $request->asset_label_single;
	    $settings->asset_label_double = $request->asset_label_double;
	    $settings->asset_label_featured = $request->asset_label_featured;
	    $settings->asset_label_pricing = $request->asset_label_pricing;
	    $settings->asset_label_single_fr = $request->asset_label_single_fr;
	    $settings->asset_label_double_fr = $request->asset_label_double_fr;
	    $settings->asset_label_featured_fr = $request->asset_label_featured_fr;
	    $settings->asset_label_pricing_fr = $request->asset_label_pricing_fr;
	    $settings->asset_label_single_en = $request->asset_label_single_en;
	    $settings->asset_label_double_en = $request->asset_label_double_en;
	    $settings->asset_label_featured_en = $request->asset_label_featured_en;
	    $settings->asset_label_pricing_en = $request->asset_label_pricing_en;
	    $settings->length_appointment = $request->length_appointment;
	    $settings->show_rating = $request->show_rating['id'];
	    $settings->show_keynotes = $request->show_keynotes['id'];
	    $settings->show_pricing = $request->show_pricing['id'];
	    $settings->show_slider = $request->show_slider['id'];
	    $settings->check_explicit_content = $request->check_explicit_content['id'];
	    $settings->change_price = $request->change_price['id'];
	    $settings->change_categories = $request->change_categories['id'];
	    $settings->change_languages = $request->change_languages['id'];
	    $settings->markup = $request->markup;
	    $settings->markup_type = $request->markup_type['id'];
	    $settings->reminder_for_estimate = $request->reminder_for_estimate['id'];
	    $settings->reminder_for_booking = $request->reminder_for_booking['id'];
	    $settings->change_timespace = $request->change_timespace['id'];
	    $settings->default_timespace = $request->default_timespace['id'];
	    $settings->extra_info_invoice = $request->extra_info_invoice;
	    
	    $settings->privacy_url_nl = $request->privacy_url_nl;
	    $settings->privacy_url_fr = $request->privacy_url_fr;
	    $settings->privacy_url_en = $request->privacy_url_en;
	    
	    $settings->application_name = $request->application_name;
	    
	    if($request->logo <> $settings->logo) {
		    $dataUri = $request->logo;
		
		    list(, $base64Data) = explode(';', $dataUri);
		    list(, $base64Data) = explode(',', $base64Data);
		
		    $imageData = base64_decode($base64Data);
			$filename = uniqid('logo_') . '-'.$customer.'.png';
			$publicDirectory = public_path('customer');

		    if (!file_exists($publicDirectory)) {
		        mkdir($publicDirectory, 0777, true);
		    }
		
		    file_put_contents($publicDirectory . '/' . $filename, $imageData);
		
		    $imageUrl = asset('customer/' . $filename);
		
		    $settings->logo = $imageUrl;
	    }
	    
	    $settings->default_estimate_lines = json_encode($request->default_estimate_lines);
	    
	    if (is_array($request->refused_reason_options)) {
		    $settings->refused_reason_options = json_encode(array_values(array_filter($request->refused_reason_options, function ($opt) {
			    return is_array($opt) && !empty($opt['id']) && !empty($opt['name']);
		    })));
	    }
	    
	    if (is_array($request->source_options)) {
		    $settings->source_options = json_encode(array_values(array_filter($request->source_options, function ($opt) {
			    return is_array($opt) && !empty($opt['id']) && !empty($opt['name']);
		    })));
	    }
	    
	    $settings->save();
	    
	    $customer = Customer::where('id', $customer)->first();
	    $customer->name = $request->application_name;
	    $customer->save();
	    
	    return response()->json(['settings' => $settings], 200);
    }
}
