<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Auth;

use App\Models\Voucher;

class VoucherController extends Controller
{
	public function toggle_active($id) {
	    $asset_field = AssetField::where('id', $id)->first();
	    
	    if($asset_field->active) {
		    $asset_field->active = 0;
	    } else {
		    $asset_field->active = 1;
	    }
	    
	    $asset_field->save();
	    
	    /* Get categories */
	    $asset_fields = AssetField::get();
	    
	    return response()->json(['asset_fields' => $asset_fields], 200);
    }
    
    public function index()
    {	    
	    $vouchers = Voucher::get();
	    
        return response()->json(['vouchers' => $vouchers], 200);
    }
    
    public function store(Request $request)
    {	    	    	    
	    $voucher = new Voucher;
	    $voucher->code = $request->code;
	    $voucher->value = $request->value*100;
	    $voucher->active = 1;
	    
	    $voucher->save();
	    
	    return response()->json(['voucher' => $voucher], 200);
    }
}
