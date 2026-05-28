<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Hash;

use Auth;
use Log;

use App\Models\User;
use App\Models\Asset;
use App\Models\ActivityLog;

class LoggingController extends Controller
{
	public function emails() {
		$loggedEmails = Log::get(); // Assuming you use the 'emails' channel
		
		return response()->json(['emails' => $loggedEmails], 200);
	}
	
	public function index() {
	    $customer = \Request::header('Customer');
	    
		$logs = ActivityLog::where('properties', 'like', '%"customer_id":'.$customer.',%')->orderBy('id', 'desc')->get()->toArray();
		$changed = array();
		
		foreach($logs as $key1 => $log) {
			$array = json_decode($log['properties']);
			
			$array1 = (array)$array->attributes;
			$array2 = (array)$array->old;
			
			// Get changes in values
		    $changes = [];
		    foreach ($array1 as $key => $value) {
			    if($key != 'updated_at' && $key != 'views') {
			        if (isset($array2[$key]) && $array2[$key] !== $value) {
			            $changes[$key] = [
			                'old_value' => $value,
			                'new_value' => $array2[$key]
			            ];
			        }
		        }
		    }
		    
		    if(count($changes)) {
			    $logs[$key1]['changes'] = $changes;
			    $changed[] = $changes;
			    $logs[$key1]['asset'] = Asset::where('id', $log['subject_id'])->first();
			    $logs[$key1]['user'] = User::where('id', $log['causer_id'])->first();
			} else {
				unset($logs[$key1]);
			}
		}
		
		return response()->json(['logging' => $logs, 'changed' => $changed], 200);
	}
}
