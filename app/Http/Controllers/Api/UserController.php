<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Role;
use App\Models\Customer;
use App\Models\RoleUser;

use Auth;
use Mail;

class UserController extends Controller
{
	public function emailcheck() {
		if($_GET['email']) {
			$user = User::where('email', $_GET['email'])->first();
			
			return response()->json(['user' => $user], 200);
		}
	}
	
	public function roles() {
		$user = User::find(Auth::user()->id);
		
        $all_roles = RoleUser::where('user_id', $user->id)->groupBy('customer_id')->get()->toArray();
        
        foreach($all_roles as $key => $role) {
	        $customer_detail = Customer::where('id', $role['customer_id'])->first()->toArray();
	        $all_roles[$key]['customer'] = $customer_detail;
        }
		
		return response()->json([
            'user' => $user,
            'roles' => $all_roles
        ], 200);
	}
	
	public function login_as_user($id) {
		$user = User::where('id', $id)->first();
		
		$user_token = $user->createToken('appToken')->accessToken;
		$roles = $user->roles()->first();
        $all_roles = RoleUser::where('user_id', $user->id)->get()->toArray();
        
        foreach($all_roles as $key => $role) {
	        if($role['customer_id'] != null) {
		        $customer_detail = Customer::where('id', $role['customer_id'])->first()->toArray();
		        $all_roles[$key]['customer'] = $customer_detail;
	        }
        }
		
        return response()->json([
            'success' => true,
            'token' => $user_token,
            'user' => $user,
            'roles' => $roles,
            'all_roles' => $all_roles
        ], 200);
	}
	
	public function change_password(Request $request, $id = false) {
		if($id) {
			$user = User::where('id', $id)->first();
		} else {
	    	$user = User::find(Auth::user()->id);
		}
		
		if($user) {
			if($id) {
				$newPassword = 'xPrTopin5632!';
				
				$mail_text = 'We hebben een wachtwoord aanvraag ontvangen, je tijdelijke wachtwoord: <strong>'.$newPassword.'</strong>.';
			    $button_text = 'Inloggen kan hier';
			    $button_link = 'https://xpertopinion-6d27c50c2a0a.herokuapp.com/login';
			    
			    $htmlMessage = view('mail', compact('button_text', 'button_link', 'mail_text'))->render();
			    $to = $request->email;
			    $subject = 'Nieuw wachtwoord';
			    
			    Mail::send([], [], function ($message) use ($to, $subject, $htmlMessage) {
		            $message->to($user)
						->bcc('rml+debug@janluts.net')
		                ->subject($subject)
		                ->html($htmlMessage, 'text/html');
		        });
			} else {
				$newPassword = $request->input('password');
			}

	        // Update the asset's password attribute with the new password
	        $user->password = bcrypt($newPassword); // You may use bcrypt or any other hashing method
	        $user->one_time_token = NULL;
	        $user->save();
			
			return response()->json(['user' => $user], 200);
		}
	}
	
    public function index()
    {
	    $customer = \Request::header('Customer');
	    
        if(isset($_GET['type'])) {
	        if($_GET['type'] == 'admin') {
		        $user_ids = RoleUser::where('role_id', 1)->where('customer_id', $customer)->pluck('user_id');
		        $users = User::whereIn('id', $user_ids)->get();
	        } else {
		        $users = User::all();
	        }
        } else {
	        $users = User::all();
        }

        return response()->json(['users' => $users], 200);
    }
    
    public function show($id)
    {
        $user = User::where('id', $id)->first();
        
        $role = RoleUser::where('user_id', $id)->first();
        $role = Role::where('id', $role->role_id)->first();
        
        $user->usertype = $role;

        return response()->json(['user' => $user], 200);
    }
    
    public function usertypes() {
	    $roles = Role::where('id', '!=', 2)->get();

        return response()->json(['roles' => $roles], 200);
    }
    
    public function store(Request $request) {
	    $customer = \Request::header('Customer');
	    
	    $found_user = User::where('email', $request->email)->first();
	    
	    if($found_user) {
		    $user = $found_user;
	    
		    /* Remove previous roles for customer */
		    RoleUser::where('user_id', $user->id)->where('customer_id', $customer)->delete();
	    } else {
		    /* Create user */
		    $user = new User();
		    $user->name = $request->name;
		    $user->email = $request->email;
		    $user->password = Hash::make('12345678');
		    $user->save();
		}
	    
	    /* User create is always admin for now!!! */
	    $user->roles()->attach(Role::where('id', 1)->first());
	    
	    $role = RoleUser::where('user_id', $user->id)->latest()->first();
	    $role->customer_id = $customer;
	    $role->save();
	    
	    /* Send mail to finish registration */
	    if($request->email) {
		    $mail_text = 'Er werd een nieuw profiel voor jou geregistreerd op ReadMyLips. Je kan inloggen met dit mailadres en het wachtwoord <strong>12345678</strong>.';
		    $button_text = 'Vervolledig je profiel hier';
		    $button_link = 'https://xpertopinion-6d27c50c2a0a.herokuapp.com/login';
		    
		    $htmlMessage = view('mail', compact('button_text', 'button_link', 'mail_text'))->render();
		    $to = $request->email;
		    $subject = 'Nieuwe registratie voor ReadMyLips';
		    
		    Mail::send([], [], function ($message) use ($to, $subject, $htmlMessage) {
	            $message->to($to)
					->bcc('rml+debug@janluts.net')
	                ->subject($subject)
	                ->html($htmlMessage, 'text/html');
	        });
	    }
	    
	    return response()->json(['user' => $user], 200);
    }
    
    public function update(Request $request, $id) {
	    $user = User::where('id', $id)->first();
	    
	    $user->name = $request->name;
	    $user->email = $request->email;
	    $user->save();
	    
	    $roles = RoleUser::where('user_id', $user->id)->get();
	    
	    foreach($roles as $role) {
		    $role->delete();
	    }
	    
	    $user->roles()->attach(Role::where('id', $request->usertype['id'])->first());
	    
	    $user_type = $request->usertype;
	    
	    return response()->json(['user' => $user, 'user_type' => $user_type], 200);
    }
}
