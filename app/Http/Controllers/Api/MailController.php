<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use Auth;

use App\Models\User;
use App\Models\MailSettings;

class MailController extends Controller
{
    public function index()
    {		
	    $customer = \Request::header('Customer');
	    
        $mailsettings = MailSettings::where('customer_id', $customer)->first();

        return response()->json(['mailsettings' => $mailsettings], 200);
    }
    
    public function update(Request $request) {		
	    $customer = \Request::header('Customer');
	    
        $mailsettings = MailSettings::where('customer_id', $customer)->first();
        $mailsettings->mail_newbooking_subject_nl = ($request->mail_newbooking_subject_nl != '')?$request->mail_newbooking_subject_nl:'';
        $mailsettings->mail_newbooking_subject_fr = ($request->mail_newbooking_subject_fr != '')?$request->mail_newbooking_subject_fr:'';
        $mailsettings->mail_newbooking_subject_en = ($request->mail_newbooking_subject_en != '')?$request->mail_newbooking_subject_en:'';
        $mailsettings->mail_newbooking_text_nl = ($request->mail_newbooking_text_nl != '')?$request->mail_newbooking_text_nl:'';
        $mailsettings->mail_newbooking_text_fr = ($request->mail_newbooking_text_fr != '')?$request->mail_newbooking_text_fr:'';
        $mailsettings->mail_newbooking_text_en = ($request->mail_newbooking_text_en != '')?$request->mail_newbooking_text_en:'';
        $mailsettings->mail_gettoknow_subject_nl = ($request->mail_gettoknow_subject_nl != '')?$request->mail_gettoknow_subject_nl:'';
        $mailsettings->mail_gettoknow_subject_fr = ($request->mail_gettoknow_subject_fr != '')?$request->mail_gettoknow_subject_fr:'';
        $mailsettings->mail_gettoknow_subject_en = ($request->mail_gettoknow_subject_en != '')?$request->mail_gettoknow_subject_en:'';
        $mailsettings->mail_gettoknow_text_nl = ($request->mail_gettoknow_text_nl != '')?$request->mail_gettoknow_text_nl:'';
        $mailsettings->mail_gettoknow_text_fr = ($request->mail_gettoknow_text_fr != '')?$request->mail_gettoknow_text_fr:'';
        $mailsettings->mail_gettoknow_text_en = ($request->mail_gettoknow_text_en != '')?$request->mail_gettoknow_text_en:'';
        $mailsettings->mail_estimate_subject_nl = ($request->mail_estimate_subject_nl != '')?$request->mail_estimate_subject_nl:'';
        $mailsettings->mail_estimate_subject_fr = ($request->mail_estimate_subject_fr != '')?$request->mail_estimate_subject_fr:'';
        $mailsettings->mail_estimate_subject_en = ($request->mail_estimate_subject_en != '')?$request->mail_estimate_subject_en:'';
        $mailsettings->mail_estimate_text_nl = ($request->mail_estimate_text_nl != '')?$request->mail_estimate_text_nl:'';
        $mailsettings->mail_estimate_text_fr = ($request->mail_estimate_text_fr != '')?$request->mail_estimate_text_fr:'';
        $mailsettings->mail_estimate_text_en = ($request->mail_estimate_text_en != '')?$request->mail_estimate_text_en:'';
        $mailsettings->mail_newmessage_subject_nl = ($request->mail_newmessage_subject_nl != '')?$request->mail_newmessage_subject_nl:'';
        $mailsettings->mail_newmessage_subject_fr = ($request->mail_newmessage_subject_fr != '')?$request->mail_newmessage_subject_fr:'';
        $mailsettings->mail_newmessage_subject_en = ($request->mail_newmessage_subject_en != '')?$request->mail_newmessage_subject_en:'';
        $mailsettings->mail_newmessage_text_nl = ($request->mail_newmessage_text_nl != '')?$request->mail_newmessage_text_nl:'';
        $mailsettings->mail_newmessage_text_fr = ($request->mail_newmessage_text_fr != '')?$request->mail_newmessage_text_fr:'';
        $mailsettings->mail_newmessage_text_en = ($request->mail_newmessage_text_en != '')?$request->mail_newmessage_text_en:'';
        $mailsettings->mail_final_booking_subject_nl = ($request->mail_final_booking_subject_nl != '')?$request->mail_final_booking_subject_nl:'';
        $mailsettings->mail_final_booking_subject_fr = ($request->mail_final_booking_subject_fr != '')?$request->mail_final_booking_subject_fr:'';
        $mailsettings->mail_final_booking_subject_en = ($request->mail_final_booking_subject_en != '')?$request->mail_final_booking_subject_en:'';
        $mailsettings->mail_final_booking_text_nl = ($request->mail_final_booking_text_nl != '')?$request->mail_final_booking_text_nl:'';
        $mailsettings->mail_final_booking_text_fr = ($request->mail_final_booking_text_fr != '')?$request->mail_final_booking_text_fr:'';
        $mailsettings->mail_final_booking_text_en = ($request->mail_final_booking_text_en != '')?$request->mail_final_booking_text_en:'';
        $mailsettings->mail_extra_subject_nl = ($request->mail_extra_subject_nl != '')?$request->mail_extra_subject_nl:'';
        $mailsettings->mail_extra_subject_fr = ($request->mail_extra_subject_fr != '')?$request->mail_extra_subject_fr:'';
        $mailsettings->mail_extra_subject_en = ($request->mail_final_booking_subject_en != '')?$request->mail_final_booking_subject_en:'';
        $mailsettings->mail_extra_text_nl = ($request->mail_extra_text_nl != '')?$request->mail_extra_text_nl:'';
        $mailsettings->mail_extra_text_fr = ($request->mail_extra_text_fr != '')?$request->mail_extra_text_fr:'';
        $mailsettings->mail_extra_text_en = ($request->mail_extra_text_en != '')?$request->mail_extra_text_en:'';
        
        $mailsettings->mail_reminder_estimate_subject_nl = ($request->mail_reminder_estimate_subject_nl != '')?$request->mail_reminder_estimate_subject_nl:'';
        $mailsettings->mail_reminder_estimate_subject_fr = ($request->mail_reminder_estimate_subject_fr != '')?$request->mail_reminder_estimate_subject_fr:'';
        $mailsettings->mail_reminder_estimate_subject_en = ($request->mail_reminder_estimate_subject_en != '')?$request->mail_reminder_estimate_subject_en:'';
        $mailsettings->mail_reminder_estimate_text_nl = ($request->mail_reminder_estimate_text_nl != '')?$request->mail_reminder_estimate_text_nl:'';
        $mailsettings->mail_reminder_estimate_text_fr = ($request->mail_reminder_estimate_text_fr != '')?$request->mail_reminder_estimate_text_fr:'';
        $mailsettings->mail_reminder_estimate_text_en = ($request->mail_reminder_estimate_text_en != '')?$request->mail_reminder_estimate_text_en:'';
        
        $mailsettings->mail_reminder_booking_subject_nl = ($request->mail_reminder_booking_subject_nl != '')?$request->mail_reminder_booking_subject_nl:'';
        $mailsettings->mail_reminder_booking_subject_fr = ($request->mail_reminder_booking_subject_fr != '')?$request->mail_reminder_booking_subject_fr:'';
        $mailsettings->mail_reminder_booking_subject_en = ($request->mail_reminder_booking_subject_en != '')?$request->mail_reminder_booking_subject_en:'';
        $mailsettings->mail_reminder_booking_text_nl = ($request->mail_reminder_booking_text_nl != '')?$request->mail_reminder_booking_text_nl:'';
        $mailsettings->mail_reminder_booking_text_fr = ($request->mail_reminder_booking_text_fr != '')?$request->mail_reminder_booking_text_fr:'';
        $mailsettings->mail_reminder_booking_text_en = ($request->mail_reminder_booking_text_en != '')?$request->mail_reminder_booking_text_en:'';
        
        $mailsettings->mail_summary_subject_nl = ($request->mail_summary_subject_nl != '')?$request->mail_summary_subject_nl:'';
        $mailsettings->mail_summary_subject_fr = ($request->mail_summary_subject_fr != '')?$request->mail_summary_subject_fr:'';
        $mailsettings->mail_summary_subject_en = ($request->mail_summary_subject_en != '')?$request->mail_summary_subject_en:'';
        $mailsettings->mail_summary_text_nl = ($request->mail_summary_text_nl != '')?$request->mail_summary_text_nl:'';
        $mailsettings->mail_summary_text_fr = ($request->mail_summary_text_fr != '')?$request->mail_summary_text_fr:'';
        $mailsettings->mail_summary_text_en = ($request->mail_summary_text_en != '')?$request->mail_summary_text_en:'';
        
        $mailsettings->mail_booking_confirm_subject_nl = ($request->mail_booking_confirm_subject_nl != '')?$request->mail_booking_confirm_subject_nl:'';
        $mailsettings->mail_booking_confirm_subject_fr = ($request->mail_booking_confirm_subject_fr != '')?$request->mail_booking_confirm_subject_fr:'';
        $mailsettings->mail_booking_confirm_subject_en = ($request->mail_booking_confirm_subject_en != '')?$request->mail_booking_confirm_subject_en:'';
        $mailsettings->mail_booking_confirm_text_nl = ($request->mail_booking_confirm_text_nl != '')?$request->mail_booking_confirm_text_nl:'';
        $mailsettings->mail_booking_confirm_text_fr = ($request->mail_booking_confirm_text_fr != '')?$request->mail_booking_confirm_text_fr:'';
        $mailsettings->mail_booking_confirm_text_en = ($request->mail_booking_confirm_text_en != '')?$request->mail_booking_confirm_text_en:'';
        
        $mailsettings->mail_booking_confirm_subject_nl = ($request->mail_booking_confirm_subject_nl != '')?$request->mail_booking_confirm_subject_nl:'';
        $mailsettings->mail_booking_confirm_subject_fr = ($request->mail_booking_confirm_subject_fr != '')?$request->mail_booking_confirm_subject_fr:'';
        $mailsettings->mail_booking_confirm_subject_en = ($request->mail_booking_confirm_subject_en != '')?$request->mail_booking_confirm_subject_en:'';
        $mailsettings->mail_booking_confirm_text_nl = ($request->mail_booking_confirm_text_nl != '')?$request->mail_booking_confirm_text_nl:'';
        $mailsettings->mail_booking_confirm_text_fr = ($request->mail_booking_confirm_text_fr != '')?$request->mail_booking_confirm_text_fr:'';
        $mailsettings->mail_booking_confirm_text_en = ($request->mail_booking_confirm_text_en != '')?$request->mail_booking_confirm_text_en:'';
        
        $mailsettings->mail_final_booking_asset_subject_nl = ($request->mail_final_booking_asset_subject_nl != '')?$request->mail_final_booking_asset_subject_nl:'';
        $mailsettings->mail_final_booking_asset_subject_fr = ($request->mail_final_booking_asset_subject_fr != '')?$request->mail_final_booking_asset_subject_fr:'';
        $mailsettings->mail_final_booking_asset_subject_en = ($request->mail_final_booking_asset_subject_en != '')?$request->mail_final_booking_asset_subject_en:'';
        $mailsettings->mail_final_booking_asset_text_nl = ($request->mail_final_booking_asset_text_nl != '')?$request->mail_final_booking_asset_text_nl:'';
        $mailsettings->mail_final_booking_asset_text_fr = ($request->mail_final_booking_asset_text_fr != '')?$request->mail_final_booking_asset_text_fr:'';
        $mailsettings->mail_final_booking_asset_text_en = ($request->mail_final_booking_asset_text_en != '')?$request->mail_final_booking_asset_text_en:'';
        
        $mailsettings->save();

        return response()->json(['mailsettings' => $mailsettings], 200);
    }
}
