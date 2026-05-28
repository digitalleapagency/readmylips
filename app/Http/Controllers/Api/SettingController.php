<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use Auth;

use App\Models\User;
use App\Models\Setting;
use App\Models\SettingsAgenda;

class SettingController extends Controller
{
    public function index()
    {
		$user = User::find(Auth::user()->id);
		
        $settings_agenda = SettingsAgenda::where('user_id', $user->id)->first();
        
        if(!$settings_agenda) {
	        $settings_agenda = SettingsAgenda::create([
	            'user_id' => $user->id,
	            'monday' => 1,
	            'tuesday' => 1,
	            'wednesday' => 1,
	            'thursday' => 1,
	            'friday' => 1,
	            'saturday' => 0,
	            'sunday' => 0,
	            'timespace' => 60
	        ]);
        }
        
        $settings_agenda->monday = ($settings_agenda->monday)?true:false;
        $settings_agenda->tuesday = ($settings_agenda->tuesday)?true:false;
        $settings_agenda->wednesday = ($settings_agenda->wednesday)?true:false;
        $settings_agenda->thursday = ($settings_agenda->thursday)?true:false;
        $settings_agenda->friday = ($settings_agenda->friday)?true:false;
        $settings_agenda->saturday = ($settings_agenda->saturday)?true:false;
        $settings_agenda->sunday = ($settings_agenda->sunday)?true:false;
        
        if($settings_agenda->timespace < 60) {
	        $settings_agenda->timespace = array(
	        	'id' => $settings_agenda->timespace,
	        	'name' => $settings_agenda->timespace.' mins'
	        );
	    } else {
		    $hours = $settings_agenda->timespace/60;
	        $settings_agenda->timespace = array(
	        	'id' => $settings_agenda->timespace,
	        	'name' => $hours.' hour(s)'
	        );
	    }
        
        $settings = Setting::where('user_id', $user->id)->first();
        
        if(!isset($settings->token)) {
	        $settings = new Setting;
	        $settings->token = '';
	        $settings->user_id = $user->id;	        
        }

        return response()->json(['settings_agenda' => $settings_agenda, 'settings' => $settings], 200);
    }
    
    public function update(Request $request) {
		$user = User::find(Auth::user()->id);
		
        $settings_agenda = SettingsAgenda::where('user_id', $user->id)->first();
	    
	    $settings_agenda->monday = ($request->monday || $request->monday=='true')?1:0;
	    $settings_agenda->tuesday = ($request->tuesday || $request->tuesday=='true')?1:0;
	    $settings_agenda->wednesday = ($request->wednesday || $request->wednesday=='true')?1:0;
	    $settings_agenda->thursday = ($request->thursday || $request->thursday=='true')?1:0;
	    $settings_agenda->friday = ($request->friday || $request->friday=='true')?1:0;
	    $settings_agenda->saturday = ($request->saturday || $request->saturday=='true')?1:0;
	    $settings_agenda->sunday = ($request->sunday || $request->sunday=='true')?1:0;
	    
	    $settings_agenda->monday_hour_start = $request->monday_hour_start;
	    $settings_agenda->monday_hour_end = $request->monday_hour_end;
	    $settings_agenda->tuesday_hour_start = $request->tuesday_hour_start;
	    $settings_agenda->tuesday_hour_end = $request->tuesday_hour_end;
	    $settings_agenda->wednesday_hour_start = $request->wednesday_hour_start;
	    $settings_agenda->wednesday_hour_end = $request->wednesday_hour_end;
	    $settings_agenda->thursday_hour_start = $request->thursday_hour_start;
	    $settings_agenda->thursday_hour_end = $request->thursday_hour_end;
	    $settings_agenda->friday_hour_start = $request->friday_hour_start;
	    $settings_agenda->friday_hour_end = $request->friday_hour_end;
	    $settings_agenda->saturday_hour_start = $request->saturday_hour_start;
	    $settings_agenda->saturday_hour_end = $request->saturday_hour_end;
	    $settings_agenda->sunday_hour_start = $request->sunday_hour_start;
	    $settings_agenda->sunday_hour_end = $request->sunday_hour_end;
	    
	    $settings_agenda->timespace = $request->timespace['id'];
	    
	    $settings_agenda->save();
	    
	    return response()->json(['settings_agenda' => $settings_agenda], 200);
    }
}
