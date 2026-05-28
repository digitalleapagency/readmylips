<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\Customer;
use App\Models\CustomerSetting;
use App\Models\MailSettings;
use App\Models\RoleUser;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::with('users')->get()->toArray();
        
        foreach($customers as $key => $customer) {
	        $customers[$key]['settings'] = CustomerSetting::where('customer_id', $customer['id'])->first();
        }
        
        return response()->json(['customers' => $customers], 200);
    }

    public function store(Request $request)
    {
        $customer = Customer::create([
            'name' => $request->name,
            'email' => time().'demo@mail.com',
            'domain' => $request->domain,
            'teamleader_access_token' => NULL,
            'teamleader_refresh_token' => NULL
        ]);
        
        if($customer) {
	        $customer_settings = CustomerSetting::where('booking_flow', $request->booking_flow)->first();

			if ($customer_settings) {
			    $new_customer_settings = $customer_settings->replicate();
				$new_customer_settings->customer_id = $customer->id;
				$new_customer_settings->application_name = $request->name;
			
			    $new_customer_settings->save();
			}
			
	        $mail_settings = MailSettings::where('customer_id', $customer_settings->customer_id)->first();

			if ($mail_settings) {
			    $new_mail_settings = $mail_settings->replicate();
				$new_mail_settings->customer_id = $customer->id;
			
			    $new_mail_settings->save();
			}
			
			/* Add demo user to this */
			$new_role = new RoleUser();
			$new_role->user_id = 1;
			$new_role->role_id = 1;
			$new_role->customer_id = $customer->id;
			$new_role->save();
        }
        
        return response()->json(['customer' => $customer], 201);
    }

    public function show(Customer $customer)
    {
        $customer->load('users');
        return response()->json(['customer' => $customer], 200);
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:customers,email,' . $customer->id
        ]);
        
	    $uniqueName = $this->makeNameUnique($request->name);

        // Update the customer's attributes
        $customer->name = $uniqueName;
        $customer->email = $request->email;
        $customer->domain = $slug = Str::slug($uniqueName) . '.xpertopinion.test';

        $customer->save();

        return response()->json(['customer' => $customer], 200);
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->json(['message' => 'Customer deleted'], 204);
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
