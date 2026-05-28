<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

use App\Models\User;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\Customer;

use Auth;

class AuthenticationController extends Controller
{
	public function forgot() {
		$user = User::where('email', request('email'))->first();
		
		// Generate a random token
		$token = Str::random(40);
		
		// Store the token in the user's record
		$user->one_time_token = $token;
		$user->save();
		
		$mail_text = '<div style="text-align: left;">Je hebt aangegeven dat je je wachtwoord vergeten bent. Gelieve hieronder een eenmalige link te vinden om in te loggen. Daarna kan je je wachtwoord wijzigen in de applicatie.</div>';
	        
        $mail_text .= '<p><a href="https://platform.xpertbooking.be/login?otl='.$token.'">Klik hier om in te loggen</a></p>';
        
        $htmlMessage = view('mail', compact('mail_text'))->render();
	    $subject = 'Wachtwoord vergeten';
		$to = $user->email;

        Mail::send([], [], function ($message) use ($to, $subject, $htmlMessage) {
            $message->to($to)
                    ->subject($subject)
                    ->html($htmlMessage, 'text/html'); // Set the content type to HTML
        });
        
        return response()->json([
            'success' => true
        ], 200);
	}
	
    /**
    * Handle an incoming authentication request.
    */
    public function store()
    {
	    if(request('otl') != '') {
		    $user = User::where('one_time_token', request('otl'))->first();
		
		    if ($user) {
		        $user_token = $user->createToken('appToken')->accessToken;
		        $roles = $user->roles()->first();
		        $all_roles = RoleUser::where('user_id', $user->id)->get()->toArray();
		        
		        foreach($all_roles as $key => $role) {
			        $customer_detail = Customer::where('id', $role['customer_id'])->first();
			        
			        if($customer_detail) {
				        $customer_detail = $customer_detail->toArray();
				        $all_roles[$key]['customer'] = $customer_detail;
			        }
		        }
		        
		        //$user->one_time_token = '';
				//$user->save();
		
		        return response()->json([
		            'success' => true,
		            'token' => $user_token,
		            'user' => $user,
		            'roles' => $roles,
		            'all_roles' => $all_roles
		        ], 200);
		    } else {
		        // Failure to authenticate
		        return response()->json([
		            'success' => false,
		            'message' => 'Failed to authenticate.',
		        ], 401);
		    }
		} else {
		    if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
		        // Successful authentication
		        $user = Auth::user();
		
		        $user_token = $user->createToken('appToken')->accessToken;
		        $roles = $user->roles()->first();
		        $all_roles = RoleUser::where('user_id', $user->id)->get()->toArray();
		        
		        foreach($all_roles as $key => $role) {			        
			        $customer_detail = Customer::where('id', $role['customer_id'])->first()->toArray();
			        $all_roles[$key]['customer'] = $customer_detail;
		        }
		
		        return response()->json([
		            'success' => true,
		            'token' => $user_token,
		            'user' => $user,
		            'roles' => $roles,
		            'all_roles' => $all_roles
		        ], 200);
		    } else {
		        // Failure to authenticate
		        return response()->json([
		            'success' => false,
		            'message' => 'Failed to authenticate.',
		        ], 401);
		    }
		}

    }
    
    /**
	* Destroy an authenticated session.
	*
	* @param  \Illuminate\Http\Request  $request
	* @return \Illuminate\Http\RedirectResponse
	*/
	public function destroy(Request $request)
	{
	    if (Auth::user()) {
	        $request->user()->token()->revoke();
	
	        return response()->json([
	            'success' => true,
	            'message' => 'Logged out successfully',
	        ], 200);
	    }
	}
	
	public function register(Request $request)
	{
	    $request->validate([
	        'name' => 'required|string',
	        'email' => 'required|string|email',
	        'password' => 'required|string|min:8',
	    ]);
	
	    // Check if the user already exists by their email
	    $user = User::where('email', $request->email)->first();
	
	    if ($user) {
	        // User already exists, you can choose to return an error response or update the existing user.
	        // For this example, we'll return an error response.
	        return response()->json(['error' => 'User already exists'], 400);
	    }
	
	    // If "admin" role is requested, revert to "user" role
	    $role = 'user';
	
	    if ($request->has('role') && $request->role != 'admin') {
	        $role = 'customer';
	    }
	
	    // Ensure the name is unique
	    $uniqueName = $this->makeNameUnique($request->name);
	
	    // Create a new user
	    $user = new User([
	        'name' => $uniqueName,
	        'email' => $request->email,
	        'password' => Hash::make($request->password),
	    ]);
	
	    // Save the user
	    $user->save();
	
	    // Attach the role to the user
	    $user->roles()->attach(Role::where('name', $role)->first());
		
	    // If the user role is "customer," create a new customer
		$customer = false;
	    if ($role == 'customer') {
	        $customer = new Customer([
	            'name' => $uniqueName,
	            'email' => $request->email,
	            'domain' => $slug = Str::slug($uniqueName) . '.xpertopinion.test',
	        ]);
	
	        $customer->save();
	        $user->customer_id = $customer->id;
	        $user->save();
	    }
	
	    // Create a Passport token for the user
	    $token = $user->createToken('appToken')->accessToken;
	
	    return response()->json(['token' => $token, 'role' => $role, 'customer' => $customer], 201);
	}

	private function makeNameUnique($name)
	{
	    $baseName = $name;
	    $count = 1;
	
	    while (Customer::where('name', $name)->exists()) {
	        $name = $baseName . $count;
	        $count++;
	    }
	
	    return $name;
	}
}
