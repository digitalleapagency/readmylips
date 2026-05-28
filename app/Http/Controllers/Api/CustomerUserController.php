<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use App\Models\Customer;
use App\Models\User;

class CustomerUserController extends Controller
{
    public function store(Customer $customer, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        
        /* Get user by ID */
        $user = User::where('id', $request->user_id)->first();
        
        $user->customer_id = $customer->id;
	    $user->save();

        // Associate the user with the customer
        $customer->users()->save($user);

        return response()->json(['user' => $user], 201);
    }

    public function index(Customer $customer)
    {
        $users = $customer->users;

        return response()->json(['users' => $users], 200);
    }
}
