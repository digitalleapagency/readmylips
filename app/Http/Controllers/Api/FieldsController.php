<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Hash;

use Auth;

use App\Models\User;
use App\Models\AssetField;
use App\Models\AssetTab;

class FieldsController extends Controller
{
    public function tabs_index()
    {	
	    $customer = \Request::header('Customer');
	    
	    $asset_tabs = AssetTab::where('customer_id', $customer)->get();
	    
        return response()->json(['asset_tabs' => $asset_tabs], 200);
    }
    
    public function tabs_show($id)
    {	    
	    $customer = \Request::header('Customer');
	    
	    $asset_tab = AssetTab::where('customer_id', $customer)->where('id', $id)->first();
	    
	    $asset_tab->active = ($asset_tab->active)?'true':'false';
	    
	    if($asset_tab->tab_icon_code) {
		    switch($asset_tab->tab_icon_code) {
			    case 'heart':
			    	$icon_name = 'Social media';
			    	break;
			    case 'circle-play':
			    	$icon_name = 'Video';
			    	break;
			    case 'file-lines':
			    	$icon_name = 'Presentatie';
			    	break;
			    case 'circle-user':
			    	$icon_name = 'Extra info';
			    default:
			    	$icon_name = 'Extra info';
			}
	    
/*
		    $asset_tab->tab_icon_code = array(
			  'name' => $icon_name,
			  'icon_code' => $asset_tab->tab_icon_code
		    );
*/
		}
		    
        return response()->json(['asset_tab' => $asset_tab], 200);
    }
    
    public function tabs_store(Request $request) {	 
	    $customer = \Request::header('Customer');
	       	    
	    $asset_tab = new AssetTab;
	    $asset_tab->fill([
		    'tab_name' => '',
		    'tab_icon_code' => '',
		    'active' => 1
	    ]);
	    
	    $asset_tab->customer_id = $customer;
	    $asset_tab->tab_name = $request->tab_name;
	    $asset_tab->tab_icon_code = $request->tab_icon_code;
	    $asset_tab->active = 1;
	    
	    $asset_tab->save();
	    
	    return response()->json(['asset_tab' => $asset_tab], 200);
    }
    
    public function tabs_update(Request $request, $id = false) {	    
	    $asset_tab = AssetTab::where('id', $id)->first(); 
	    
	    $asset_tab->tab_name = $request->tab_name;
	    $asset_tab->tab_icon_code = $request->tab_icon_code;
	    
	    $asset_tab->save();
	    
	    return response()->json(['asset_tab' => $asset_tab], 200);
    }
    
	public function tabs_toggle_active($id) { 
	    $customer = \Request::header('Customer');
	    
	    $asset_tab = AssetTab::where('id', $id)->first();
	    
	    if($asset_tab->active) {
		    $asset_tab->active = 0;
	    } else {
		    $asset_tab->active = 1;
	    }
	    
	    $asset_tab->save();
	    
	    /* Get categories */
	    $asset_tabs = AssetTab::where('customer_id', $customer)->get();
	    
	    return response()->json(['asset_tabs' => $asset_tabs], 200);
    }
    
	public function toggle_active($id) {
	    $customer = \Request::header('Customer');
	    
	    $asset_field = AssetField::where('id', $id)->first();
	    
	    if($asset_field->active) {
		    $asset_field->active = 0;
	    } else {
		    $asset_field->active = 1;
	    }
	    
	    $asset_field->save();
	    
	    /* Get categories */
	    $asset_fields = AssetField::where('customer_id', $customer)->get();
	    
	    return response()->json(['asset_fields' => $asset_fields], 200);
    }
    
    public function index()
    {	    
	    $customer = \Request::header('Customer');
	    
	    $asset_fields = AssetField::orderBy('assets_tab_id', 'ASC')->where('customer_id', $customer)->get()->toArray();
	    
	    foreach($asset_fields as $key => $field) {
		    if($field['assets_tab_id']) {
			   $asset_fields[$key]['asset_tab'] = AssetTab::where('id', $field['assets_tab_id'])->first();
		    } else {
			   $asset_fields[$key]['asset_tab'] = array();
		    }
	    }
	    
        return response()->json(['asset_fields' => $asset_fields], 200);
    }
    
    public function show($id)
    {	    
	    $customer = \Request::header('Customer');
	    
	    $asset_field = AssetField::where('id', $id)->where('customer_id', $customer)->first();
	    
	    $asset_field->editable = ($asset_field->editable)?'true':'false';
	    $asset_field->active = ($asset_field->active)?'true':'false';
	    $asset_field->assets_tab = array();
	    
	    if($asset_field->assets_tab_id) {
		    $asset_tab = AssetTab::where('id', $asset_field->assets_tab_id)->first();
		    
	    	$asset_field->assets_tab = $asset_tab;
	    }
	    
	    switch($asset_field->field_type) {
		    case 1:
		    	$asset_field->field_type = array('id' => $asset_field->field_type, 'name' => 'Text');
		    	break;
		    case 3:
		    	$asset_field->field_type = array('id' => $asset_field->field_type, 'name' => 'Video (youtube/vimeo');
		    	break;
		    case 4:
		    	$asset_field->field_type = array('id' => $asset_field->field_type, 'name' => 'Social media');
		    	break;
		    case 5:
		    	$asset_field->field_type = array('id' => $asset_field->field_type, 'name' => 'Link');
		    	break;
		    case 6:
		    	$asset_field->field_type = array('id' => $asset_field->field_type, 'name' => 'Audio (soundcloud)');
		    	break;
	    }
	    
        return response()->json(['asset_field' => $asset_field], 200);
    }
    
    public function store(Request $request) {	
	    $customer = \Request::header('Customer');
	        	    
	    $asset_field = new AssetField;
	    $asset_field->fill([
		    'field_name' => '',
		    'editable' => 1,
		    'active' => 1,
		    'customer_id' => '',
		    'field_type' => 1
	    ]);
	    
	    $user = User::find(Auth::user()->id);
	    
	    $asset_field->customer_id = $customer;
	    $asset_field->field_name = $request->field_name;
	    $asset_field->editable = ($request->editable=='true')?1:0;
	    $asset_field->active = ($request->active=='true')?1:0;
	    $asset_field->field_type = ($request->field_type)?$request->field_type['id']:1;
	    
	    if($request->assets_tab) {
	    	$asset_field->assets_tab_id = $request->assets_tab['id'];
	    } else {
		    $asset_field->assets_tab_id = NULL;
	    }
	    
	    $asset_field->save();
	    
	    return response()->json(['asset_field' => $asset_field], 200);
    }
    
    public function update(Request $request, $id = false) {	    
	    $asset_field = AssetField::where('id', $id)->first(); 
	    
	    $asset_field->field_name = $request->field_name;
	    $asset_field->editable = ($request->editable=='true')?1:0;
	    $asset_field->active = ($request->active=='true')?1:0;
	    $asset_field->field_type = ($request->field_type)?$request->field_type['id']:1;
	    
	    if($request->assets_tab) {
	    	$asset_field->assets_tab_id = $request->assets_tab['id'];
	    } else {
		    $asset_field->assets_tab_id = NULL;
	    }
	    
	    $asset_field->save();
	    
	    return response()->json(['asset_field' => $asset_field], 200);
    }
}
