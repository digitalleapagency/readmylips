<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Hash;

use DB;
use Auth;
use Mail;
use File;
use Storage;

use App\Models\User;
use App\Models\Customer;
use App\Models\Asset;
use App\Models\Category;
use App\Models\AssetCategory;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\AssetField;
use App\Models\AssetFieldsInfo;
use App\Models\AssetTab;
use App\Models\Language;
use App\Models\AssetLanguage;
use App\Models\CustomerSetting;
use App\Models\ExplicitContent;

class AssetController extends Controller
{
	public function slidervalues() {
	    $customer = \Request::header('Customer');
	    $customer_settings = CustomerSetting::where('customer_id', $customer)->first();
	    
		$assets = Asset::where('customer_id', $customer)->orderBy('price', 'desc')->get();

	    // Calculate the min and max values in price
	    $minPrice = $assets->isEmpty() ? 0 : $assets->last()->price/100;
	    $maxPrice = $assets->isEmpty() ? 0 : $assets->first()->price/100;
	    
	    if($customer_settings->markup_type == 1) {
		    $minPrice += $customer_settings->markup;
		    $maxPrice += $customer_settings->markup;
	    } else {
		    $minPrice += $minPrice*(1+($customer_settings->markup/100));
		    $maxPrice += $maxPrice*(1+($customer_settings->markup/100));
	    }
	
	    // Calculate the step value
	    $step = ($maxPrice - $minPrice) / 100; // You can adjust the number of steps as needed
	
	    // Return the values in JSON format
	    return response()->json([
	        'min' => $minPrice,
	        'max' => $maxPrice,
	        'step' => $step,
	        'minValue' => $minPrice, // You can set these to initial values as needed
	        'maxValue' => $maxPrice,
	    ], 200);
	}
	
	public function languages() {
		$languages = Language::get();	
		
		return response()->json(['languages' => $languages], 200);
	}
	
	public function public()
    {
	    $assets = Asset::orderBy('title', 'asc');
        
        $assets = $assets->get();

        echo json_encode($assets);
    }
    
    public function duplicate($id) {
	    $customer = \Request::header('Customer');
	    
	    $asset = Asset::where('id', $id)->first();
	    
	    $allRoles = RoleUser::where('user_id', $asset->user_id)->where('customer_id', '!=', $customer)->get();
	    $newAsset = false;
	    
	    foreach($allRoles as $role) {
		    $asset_found = Asset::where('user_id', $asset->user_id)->where('customer_id', $role->customer_id)->first();
		    
		    if($asset_found) {
			    $newAsset = $asset_found;
		    } else {
			    $newAsset = $asset->replicate();
			    $newAsset->customer_id = $role->customer_id;
				$newAsset->save();
		    }
		}
	    
	    return response()->json(['asset' => $asset, 'new_asset' => $newAsset], 200);
    }
    
    public function toggle_featured($id) {
	    $customer = \Request::header('Customer');
	    $customer_settings = CustomerSetting::where('customer_id', $customer)->first();
	    
	    $asset = Asset::where('id', $id)->first();
	    
	    if($asset->featured) {
		    $asset->featured = 0;
	    } else {
		    $asset->featured = 1;
	    }
	    
	    $asset->save();
	    
	    /* Get assets */	    
	    $assets = Asset::where('active', '!=', 2)->where('customer_id', $customer);
		$assets->orderBy('featured', 'desc');
	    
	    switch($customer_settings->sorting) {
		    case 1:
		    	$assets->orderBy('title', 'asc');
		    	break;
		    case 2:
		    	$assets->orderBy('title', 'desc');
		    	break;
		    case 3:
		    	$assets->inRandomOrder();
		    	break;
	    }
		
		if(isset($_GET['category_id'])) {
		    $asset_ids = AssetCategory::where('category_id', $_GET['category_id'])->pluck('asset_id');
		    
		    if(count($asset_ids)) {
			    $asset_ids = $asset_ids->toArray();
			    
			    $assets = $assets->whereIn('id', $asset_ids);
		    }
	    }
        
        $assets = $assets->get();
	    
        return response()->json(['asset' => $asset, 'assets' => $assets], 200);
    }
    
    public function toggle_archive($id) {
	    $customer = \Request::header('Customer');
	    $customer_settings = CustomerSetting::where('customer_id', $customer)->first();
	    
	    $asset = Asset::where('id', $id)->first();
	    
	    $asset->active = 2;
	    
	    $asset->save();
	    
	    /* Get assets */	    
	    $assets = Asset::where('active', '!=', 2)->where('customer_id', $customer);
		$assets->orderBy('featured', 'desc');
	    
	    switch($customer_settings->sorting) {
		    case 1:
		    	$assets->orderBy('title', 'asc');
		    	break;
		    case 2:
		    	$assets->orderBy('title', 'desc');
		    	break;
		    case 3:
		    	$assets->inRandomOrder();
		    	break;
	    }
		
		if(isset($_GET['category_id'])) {
		    $asset_ids = AssetCategory::where('category_id', $_GET['category_id'])->pluck('asset_id');
		    
		    if(count($asset_ids)) {
			    $asset_ids = $asset_ids->toArray();
			    
			    $assets = $assets->whereIn('id', $asset_ids);
		    }
	    }
        
        $assets = $assets->get();
	    
        return response()->json(['asset' => $asset, 'assets' => $assets], 200);
    }
    
    public function toggle_active($id) {
	    $customer = \Request::header('Customer');
	    $customer_settings = CustomerSetting::where('customer_id', $customer)->first();
	    
	    $asset = Asset::where('id', $id)->first();
	    
	    if($asset->active == 2) {
		    $asset->active = 0;
		}
	    
	    if($asset->active) {
		    $asset->active = 0;
	    } else {
		    $asset->active = 1;
	    }
	    
	    $asset->save();
	    
	    /* Get assets */	    
	    $assets = Asset::where('active', '!=', 2)->where('customer_id', $customer);
		$assets->orderBy('featured', 'desc');
	    
	    switch($customer_settings->sorting) {
		    case 1:
		    	$assets->orderBy('title', 'asc');
		    	break;
		    case 2:
		    	$assets->orderBy('title', 'desc');
		    	break;
		    case 3:
		    	$assets->inRandomOrder();
		    	break;
	    }
		
		if(isset($_GET['category_id'])) {
		    $asset_ids = AssetCategory::where('category_id', $_GET['category_id'])->pluck('asset_id');
		    
		    if(count($asset_ids)) {
			    $asset_ids = $asset_ids->toArray();
			    
			    $assets = $assets->whereIn('id', $asset_ids);
		    }
	    }
        
        $assets = $assets->get();
	    
        return response()->json(['asset' => $asset, 'assets' => $assets], 200);
    }
    
    public function admin_index()
    {
	    $user = User::find(Auth::user()->id);
	    
	    $customer = \Request::header('Customer');
	    $customer_settings = CustomerSetting::where('customer_id', $customer)->first();
	    
	    if(isset($_GET['user'])) {
		    $assets = Asset::orderBy('title', 'asc');
		} else {
			$assets = Asset::orderBy('featured', 'desc');
			
		    switch($customer_settings->sorting) {
			    case 1:
			    	$assets->orderBy('title', 'asc');
			    	break;
			    case 2:
			    	$assets->orderBy('title', 'desc');
			    	break;
			    case 3:
			    	$assets->inRandomOrder();
			    	break;
		    }
		}
		
		$assets->where('customer_id', $customer);
		
		if(isset($_GET['status'])) {
			$assets->where('active', $_GET['status']);
		} else {
			$assets->where('active', '!=', 2);
		}
		
		if(isset($_GET['name'])) {
			$assets->where('title', 'like', '%' . $_GET['name'] . '%');
		}
		
		if(isset($_GET['user'])) {
			$assets->where('user_id', '>', 0);
		}
		
		if(isset($_GET['category_id'])) {
		    $asset_ids = AssetCategory::where('category_id', $_GET['category_id'])->pluck('asset_id');
		    
		    if(count($asset_ids)) {
			    $asset_ids = $asset_ids->toArray();
			    
			    $assets = $assets->whereIn('id', $asset_ids);
		    }
	    }
        
        $assets = $assets->get();
        
/*
        foreach ($assets as $asset) {
		    $asset->rating = rand(1, 5);         // Random number between 1 and 5 (inclusive)
		    $asset->keynotes = rand(1, 1000);   // Random number between 1 and 1000 (inclusive)
		    
		    // Generate random views that are higher than keynotes
		    $minViews = $asset->keynotes + 1;   // Ensure views are higher than keynotes
		    $asset->views = rand($minViews, 10000); // Random number between $minViews and 10000
		    
		    $asset->save();
		}
*/

        return response()->json(['assets' => $assets], 200);
    }
    
    public function index()
    {	
	    $customer = \Request::header('Customer');
	    $customer = Customer::where('id', $customer)->first();

	    $customer_settings = CustomerSetting::where('customer_id', $customer->id)->first();
	    
	    /* Get all and also get the price with the update */
	    if($customer_settings->markup_type == 1) {
		    $customer_settings->markup = $customer_settings->markup*100;
		    $assets = Asset::select(DB::raw('*, price + '.$customer_settings->markup.' AS markup_price'));
	    } else {
		    $assets = Asset::select(DB::raw('*, price * 1.'.$customer_settings->markup.' AS markup_price'));
	    }
		
		if(isset($_GET['user'])) {
			$assets->where('user_id', '>', 0);
			
			$admin_user_ids = RoleUser::where('role_id', 1)->where('customer_id', $customer->id)->pluck('user_id')->toArray();
			
			if(!in_array(Auth::user()->id, $admin_user_ids)) {
				$assets->whereIn('user_id', $admin_user_ids);
			} else {
				$assets->whereNotIn('user_id', $admin_user_ids);
			}
		}
	    
	    $assets->where('active', 1)->where('customer_id', $customer->id);
	    
	    if(isset($_GET['user'])) {
		    $assets->orderBy('title', 'asc');
		} else {
			$assets->orderBy('featured', 'desc');
		    
		    switch($customer_settings->sorting) {
			    case 1:
			    	$assets->orderBy('title', 'asc');
			    	break;
			    case 2:
			    	$assets->orderBy('title', 'desc');
			    	break;
			    case 3:
			    	$assets->inRandomOrder();
			    	break;
		    }
		}
		
		if(isset($_GET['category_id'])) {
		    $asset_ids = AssetCategory::where('category_id', $_GET['category_id'])->pluck('asset_id');
		    
		    if(count($asset_ids)) {
			    $asset_ids = $asset_ids->toArray();
			    
			    $assets = $assets->whereIn('id', $asset_ids);
		    }
	    }
        
        $assets = $assets->get();

        return response()->json(['assets' => $assets, 'customer' => $customer], 200);
    }
    
    public function show(Request $request, $id)
    {	    
	    $customer = \Request::header('Customer');
	    $customer = Customer::where('id', $customer)->first();

	    $customer_settings = CustomerSetting::where('customer_id', $customer->id)->first();
	    
	    /* Get all and also get the price with the update */
	    if($customer_settings->markup_type == 1) {
		    $customer_settings->markup = $customer_settings->markup*100;
		    $asset = Asset::select(DB::raw('*, price + '.$customer_settings->markup.' AS markup_price'));
	    } else {
		    $asset = Asset::select(DB::raw('*, price * 1.'.$customer_settings->markup.' AS markup_price'));
	    }
	    
	    if(isset($_GET['iframe'])) {
			$asset = Asset::where('drupal_id', $id)->first();
			$id = $asset->id;
	    }
	    
	    $asset = $asset->where('id', $id)->where('customer_id', $customer->id)->first();
	    
		/* Detect if url contains admin, else log a view */
	    $currentRoute = $request->path();
        
        if (strpos($currentRoute, '/admin') === false) {
	        $asset->views += 1;
	        $asset->save();
	    }
	    
	    $categories = AssetCategory::where('asset_id', $asset->id)->pluck('category_id');
	    $asset->categories = Category::whereIn('id', $categories)->where('active', 1)->get();
	    
	    $languages = AssetLanguage::where('asset_id', $asset->id)->pluck('language_id');
	    $asset->languages = Language::whereIn('id', $languages)->get();
	    
	    $asset->price = $asset->price/100;
	    
	    /* Get other versions of user in database */
	    $other_versions = Asset::where('user_id', $asset->user_id)->where('id', '!=', $asset->id)->get();
	    
	    $asset->keynote_topics = json_decode($asset->keynote_topics);
	    
	    if($asset->keynote_topics == null) {
		    $topics = array();
		    $topics[] = ['keynote' => ''];
		    
		    $asset->keynote_topics = $topics;
	    }
	    
        return response()->json(['asset' => $asset, 'customer' => $customer, 'other_versions' => $other_versions], 200);
    }
    
    public function fields() {
	    $asset_fields = AssetField::orderBy('assets_tab_id', 'ASC')->get()->toArray();
	    
	    foreach($asset_fields as $key => $field) {
		    if($field['assets_tab_id']) {
			   $asset_fields[$key]['asset_tab'] = AssetTab::where('id', $field['assets_tab_id'])->first();
		    } else {
			   $asset_fields[$key]['asset_tab'] = array();
		    }
	    }
	    
        return response()->json(['asset_fields' => $asset_fields], 200);
    }
    
    public function asset_fields($id)
    {
	    $customer = \Request::header('Customer');
	    
	    $asset = Asset::where('id', $id)->where('customer_id', $customer)->first();   
	    $fields = array();
	    $asset_tabs = array();
	    
	    if($asset) {
		    /* Fill fields public */
		    $asset_fields = AssetField::orderBy('assets_tab_id', 'ASC')->where('editable', 1)->where('customer_id', $customer)->where('active', '!=', 2)->get()->toArray();
		    
		    foreach($asset_fields as $key => $asset_field) {
			    $field = 'field_'.$asset_field['id'];
			    $field_val = '';
			    
			    /* Get field detail */
			    $field_info = AssetFieldsInfo::where('asset_id', $asset->id)->where('asset_field_id', $asset_field['id'])->first();
			    
			    if($field_info && $field_info->field_value) {
				    $field_val = $field_info->field_value;
			    }
			    
			    if($asset_field['assets_tab_id']) {
				   $asset_tab = AssetTab::where('id', $asset_field['assets_tab_id'])->where('customer_id', $customer)->first();
				   
				   if($field_info && $field_info->field_value) {
				   		$asset_tabs[$asset_field['assets_tab_id']] = $asset_tab;
				   }
			    } else {
				   $asset_tab = array();
			    }
			    
			    $fields[] = array(
				  'value' => $field_val,
				  'field' => $field,
				  'id' => $asset_field['id'],
				  'name' => $asset_field['field_name'],
				  'field_type' => $asset_field['field_type'],
				  'assets_tab_id' => $asset_field['assets_tab_id'],
				  'asset_tab' => $asset_tab,  
			    );
		    }
	    }
	    
        return response()->json(['fields' => $fields, 'tabs' => $asset_tabs], 200);
    }
    
    public function profile_fields()
    {
	    $customer = \Request::header('Customer');
	    
	    $user = User::find(Auth::user()->id);
	    
	    if($user) {
			$asset = Asset::where('user_id', $user->id)->where('customer_id', $customer)->first();   
	    }
	    
	    $fields = array();    
	    
	    if($asset) {
		    /* Fill fields public */
		    $asset_fields = AssetField::orderBy('assets_tab_id', 'ASC')->where('editable', 1)->where('active', '!=', 2)->where('customer_id', $customer)->get()->toArray();
		    
		    foreach($asset_fields as $key => $asset_field) {
			    $field = 'field_'.$asset_field['id'];
			    $field_val = '';
			    
			    /* Get field detail */
			    $field_info = AssetFieldsInfo::where('asset_id', $asset->id)->where('asset_field_id', $asset_field['id'])->first();
			    
			    if($field_info && $field_info->field_value) {
				    $field_val = $field_info->field_value;
			    }
			    
			    if($asset_field['assets_tab_id']) {
				   $asset_tab = AssetTab::where('id', $asset_field['assets_tab_id'])->where('customer_id', $customer)->first();
			    } else {
				   $asset_tab = array();
			    }
			    
			    $fields[] = array(
				  'value' => $field_val,
				  'field' => $field,
				  'id' => $asset_field['id'],
				  'name' => $asset_field['field_name'],
				  'assets_tab_id' => $asset_field['assets_tab_id'],
				  'asset_tab' => $asset_tab,
				  'field_type' => $asset_field['field_type'],
			    );
		    }
	    }
	    
        return response()->json(['fields' => $fields], 200);
    }
    
    public function profile()
    {
	    $customer = \Request::header('Customer');
	    
	    $user = User::find(Auth::user()->id);
	    
	    if($user) {
			$asset = Asset::where('user_id', $user->id)->where('customer_id', $customer)->first();   
	    }
	    
	    if(!$asset) {
		    $asset = new Asset;
		    $asset->fill([
			    'title' => $user->name,
			    'subtitle' => '',
			    'description' => '',
			    'title_fr' => $user->name,
			    'subtitle_fr' => '',
			    'description_fr' => '',
			    'title_en' => $user->name,
			    'subtitle_en' => '',
			    'description_en' => '',
			    'image' => '',
			    'asset_type_id' => 1,
			    'customer_id' => $user->id,
			    'price' => 20000
		    ]);
	    }
	    
	    /* Fill fields public */
	    $asset_fields = AssetField::where('editable', 1)->where('active', '!=', 2)->where('customer_id', $customer)->get();
	    
	    foreach($asset_fields as $asset_field) {
		    $field = 'field_'.$asset_field->id;
		    $asset->$field = '';
		    
		    /* Get field detail */
		    $field_info = AssetFieldsInfo::where('asset_id', $asset->id)->where('asset_field_id', $asset_field->id)->first();
		    
		    if($field_info && $field_info->field_value) {
			    $asset->$field = $field_info->field_value;
		    }
	    }
	    
	    $categories = AssetCategory::where('asset_id', $asset->id)->pluck('category_id');
	    $asset->categories = Category::whereIn('id', $categories)->where('customer_id', $customer)->where('active', 1)->get();
	    
	    $languages = AssetLanguage::where('asset_id', $asset->id)->pluck('language_id');
	    $asset->languages = Language::whereIn('id', $languages)->get();
	    
	    if($asset->email == '') {
			$asset->email = $user->email;    
	    }
	    
	    $asset->price = $asset->price/100;
	    
	    $asset->keynote_topics = json_decode($asset->keynote_topics);
	    
	    if($asset->keynote_topics == null) {
		    $topics = array();
		    $topics[] = ['keynote' => ''];
		    
		    $asset->keynote_topics = $topics;
	    }
	    
        return response()->json(['asset' => $asset], 200);
    }
    
    public function store(Request $request) {	 
	    $customer = \Request::header('Customer');
	       	    
	    $asset = new Asset;
	    $asset->fill([
		    'title' => '',
		    'subtitle' => '',
		    'description' => '',
		    'title_fr' => '',
		    'subtitle_fr' => '',
		    'description_fr' => '',
		    'title_en' => '',
		    'subtitle_en' => '',
		    'description_en' => '',
		    'image' => '',
		    'asset_type_id' => 1,
		    'customer_id' => 1,
			'price' => 20000
	    ]);
	    
	    $asset->title = $request->title;
	    $asset->subtitle = $request->subtitle;
	    $asset->description = $request->description;
	    $asset->title_fr = $request->title_fr;
	    $asset->subtitle_fr = $request->subtitle_fr;
	    $asset->description_fr = $request->description_fr;
	    $asset->title_en = $request->title_en;
	    $asset->subtitle_en = $request->subtitle_en;
	    $asset->description_en = $request->description_en;
	    $asset->email = $request->email;
	    $asset->email_assistant = $request->email_assistant;
	    $asset->phone = $request->phone;
	    $asset->notes = $request->notes;
	    $asset->drupal_id = $request->drupal_id;
	    $asset->customer_id = $customer;
		    
		$asset->keynote_topics = json_encode($request->keynote_topics);
	    
	    if(isset($request->price)) {
	    	$asset->price = $request->price*100;
	    }
	    
	    $asset->video = $request->video;
	    $asset->rating = (isset($request->rating))?$request->rating:0;
	    $asset->keynotes = (isset($request->keynotes))?$request->keynotes:0;
	    
	    $asset->save();
	    
	    if($request->image) {
		    $dataUri = $request->image;
		
		    list(, $base64Data) = explode(';', $dataUri);
		    list(, $base64Data) = explode(',', $base64Data);
		
		    $imageData = base64_decode($base64Data);
			$filename = uniqid('profile_image_') . '-'.$asset->id.'.png';
			$publicDirectory = public_path('profile_image');

		    if (!file_exists($publicDirectory)) {
		        mkdir($publicDirectory, 0777, true);
		    }
		
		    file_put_contents($publicDirectory . '/' . $filename, $imageData);
		
		    $imageUrl = asset('profile_image/' . $filename);
		
		    $asset->image = $imageUrl;
	    
			$asset->save();
	    }
	    
	    /* Create user */
	    if($asset->email) {
	    /* Search for user */
	    $user = User::where('email', $request->email)->first();
	    
	    if($user) {
		    $asset->user_id = $user->id;
			$asset->save();
			
			$user->roles()->attach(Role::where('name', 'user')->first());
		    
		    $role = RoleUser::where('user_id', $user->id)->latest()->first();
		    $role->customer_id = $customer;
		    $role->save();
	    } else {
		    $user = new User();
		    $user->name = $request->title;
		    $user->email = $request->email;
		    $user->password = Hash::make('12345678');
		    $user->save();
		    
		    $asset->user_id = $user->id;
		    $asset->save();
		    
		    $user->roles()->attach(Role::where('name', 'user')->first());
		    
		    $role = RoleUser::where('user_id', $user->id)->latest()->first();
		    $role->customer_id = $customer;
		    $role->save();
		    
		    /* Send mail to finish registration */
		    if($request->email) {
			    $mail_text = 'Er werd een nieuw profiel voor jou geregistreerd op Xpertbooking. Je kan inloggen met dit mailadres en het wachtwoord <strong>12345678</strong>.';
			    $button_text = 'Vervolledig je profiel hier';
			    $button_link = 'https://platform.xpertbooking.be/login';
			    
			    $htmlMessage = view('mail', compact('button_text', 'button_link', 'mail_text'))->render();
			    $to = $request->email;
			    $subject = 'Nieuwe registratie voor Xpertbooking';
			    
			    $customer_settings = CustomerSetting::where('customer_id', $asset->customer_id)->first();
			    $fromName = $customer_settings->application_name;
				$name = strtolower($fromName);
				$name = preg_replace('/[^a-z0-9]/', '.', $name);
				$fromEmail = $name . '@mlbx.email';
			    
	/*
			    Mail::send([], [], function ($message) use ($to, $subject, $htmlMessage, $fromEmail, $fromName) {
		            $message->to($to)
						->bcc('rml+debug@janluts.net')
		                ->subject($subject)
		                ->from($fromEmail, $fromName)
		                ->html($htmlMessage, 'text/html');
		        });
	*/
			    }
		    }
		}
	    
	    if(isset($request->categories)) {
		    foreach($request->categories as $category) {		    
			    $asset_cat = new AssetCategory;
			    
			    $asset_cat->fill([
				    'category_id' => $category['id'],
				    'asset_id' => $asset->id
				]);
				
				$asset_cat->save();
		    }
	    }
	    
	    if(isset($request->languages)) {
		    foreach($request->languages as $language) {		    
			    $asset_lang = new AssetLanguage;
			    
			    $asset_lang->fill([
				    'language_id' => $language['id'],
				    'asset_id' => $asset->id
				]);
				
				$asset_lang->save();
		    }
	    }
	    
	    return response()->json(['asset' => $asset], 200);
    }
    
    public function detectAdultContent($text) {
	    // List of keywords indicating adult content
	    $adultKeywords = array(
	        'explicit', 'porn', 'xxx', 'nude', 'erotic', 'nsfw', 'naked'
	    );
	
	    // List of porn sites to check against
	    $pornSites = array(
		    'pornhub.com', 'xvideos.com', 'xhamster.com', 'youporn.com', 'redtube.com',
		    'bravotube.com', 'tube8.com', 'spankbang.com', 'pornhd.com', 'empflix.com',
		    'spankwire.com', 'hdporn.com', 'beeg.com', 'sex.com', 'lustery.com', 'xossip.com',
		    'nudevista.com', 'perfectgirls.net', 'eporner.com', 'porntube.com', 'sextube.com',
		    'fapdu.com', 'pornmd.com', 'yespornplease.com', 'drtuber.com', 'gotporn.com',
		    'tubegalore.com', 'vporn.com', 'porn.com', 'pornpics.com'
		);
	
	    // Check for adult keywords
	    foreach ($adultKeywords as $keyword) {
	        if (stripos($text, $keyword) !== false) {
	            return true;
	        }
	    }
	
	    // Check for links to porn sites
	    foreach ($pornSites as $site) {
	        if (stripos($text, $site) !== false) {
	            return true;
	        }
	    }
	
	    // No adult content detected
	    return false;
	}
    
    public function update(Request $request, $id = false) {	 
	    $customer = \Request::header('Customer');
	       
	    if($id) {
		    $user = User::find(Auth::user()->id);
		    
		    $asset = Asset::where('id', $id)->first(); 
	    } else {
		    $user = User::find(Auth::user()->id);
		    
		    if($user) {
				$asset = Asset::where('user_id', $user->id)->where('customer_id', $customer)->first();   
		    }
	    }
	    
	    if(!$asset) {
		    $asset = new Asset;
		    $asset->fill([
			    'title' => $user->name,
			    'subtitle' => '',
			    'description' => '',
			    'title_fr' => $user->name,
			    'subtitle_fr' => '',
			    'description_fr' => '',
			    'title_en' => $user->name,
			    'subtitle_en' => '',
			    'description_en' => '',
			    'image' => '',
			    'asset_type_id' => 1,
			    'customer_id' => $user->id
		    ]);
	    }
	    
	    if($id) {
		} else {
	    	$user->name = $request->title;
	    }
	    
	    $user->save();
	    
	    /* Check description */
	    $customer_settings = CustomerSetting::where('customer_id', $customer)->first();
	    $adult_content = false;
	    
	    if(self::detectAdultContent($request->description)) {
		    $request->description = $asset->description;
			$adult_content = true;
			
			$explicit_content = new ExplicitContent;
			$explicit_content->asset_id = $asset->id;
			$explicit_content->description = $request->description;
			$explicit_content->feedback = $adult_content;
			$explicit_content->save();
	    }
	    
	    if(self::detectAdultContent($request->description_fr)) {
		    $request->description_fr = $asset->description_fr;
			$adult_content = true;
			
			$explicit_content = new ExplicitContent;
			$explicit_content->asset_id = $asset->id;
			$explicit_content->description = $request->description;
			$explicit_content->feedback = $adult_content;
			$explicit_content->save();
	    }
	    
	    if(self::detectAdultContent($request->description_en)) {
		    $request->description_en = $asset->description_en;
			$adult_content = true;
			
			$explicit_content = new ExplicitContent;
			$explicit_content->asset_id = $asset->id;
			$explicit_content->description = $request->description;
			$explicit_content->feedback = $adult_content;
			$explicit_content->save();
	    }

		if($customer_settings->check_explicit_content && $adult_content) {
			return response()->json(['asset' => $asset], 202);
		} else {
		    $asset->title = $request->title;
		    $asset->subtitle = $request->subtitle;
		    $asset->description = $request->description;
		    $asset->title_fr = $request->title_fr;
		    $asset->subtitle_fr = $request->subtitle_fr;
		    $asset->description_fr = $request->description_fr;
		    $asset->title_en = $request->title_en;
		    $asset->subtitle_en = $request->subtitle_en;
		    $asset->description_en = $request->description_en;
		    $asset->email = $request->email;
		    $asset->email_assistant = $request->email_assistant;
		    $asset->phone = $request->phone;
		    $asset->drupal_id = $request->drupal_id;
		    $asset->video = $request->video;
		    
		    if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $asset->video, $matches)) {
		        // Construct the full YouTube link using the extracted video ID
		        $asset->video = "https://www.youtube.com/watch?v=" . $matches[1];
		    }
		    
		    $asset->rating = $request->rating;
		    $asset->keynotes = $request->keynotes;
		    $asset->notes = $request->notes;
		    
		    $asset->keynote_topics = json_encode($request->keynote_topics);
		    
		    if ($request->image && filter_var($request->image, FILTER_VALIDATE_URL) !== false) {
			} else {
				if($request->image) {
				    $dataUri = $request->image;
				
				    list(, $base64Data) = explode(';', $dataUri);
				    list(, $base64Data) = explode(',', $base64Data);
				
				    $imageData = base64_decode($base64Data);
					$filename = uniqid('profile_image_') . '-'.$asset->id.'.png';
					$publicDirectory = public_path('profile_image');
		
				    if (!file_exists($publicDirectory)) {
				        mkdir($publicDirectory, 0777, true);
				    }
				
				    file_put_contents($publicDirectory . '/' . $filename, $imageData);
				
				    $imageUrl = asset('profile_image/' . $filename);
				    
				    /* Check image for explicit content */
				    if($customer_settings->check_explicit_content) {
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, 'https://api.edenai.run/v2/image/explicit_content');
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
						curl_setopt($ch, CURLOPT_HTTPHEADER, [
						    'authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoiZjZhOWNiNTEtNjBlYy00ZjczLWE3NTktMDllMzFhZmQ4ZWI3IiwidHlwZSI6ImFwaV90b2tlbiJ9.ct5ZHDM2-iFDCCJbML0F4N5U5jFhPCvkDuqG6xXxzBc',
						    'content-type: application/json',
						]);
						curl_setopt($ch, CURLOPT_POSTFIELDS, "{\n  \"providers\": \"google\",\n  \"file_url\": \"https://phplaravel-1146384-3986140.cloudwaysapps.com/profile_image/".$filename."\"\n}");
						
						$response = curl_exec($ch);
						$result = json_decode($response);
						
						curl_close($ch);
						
						if(isset($result->google) && $result->google->nsfw_likelihood > 3) {
							if (File::exists($publicDirectory . '/' . $filename)) {
							    File::delete($publicDirectory . '/' . $filename);
							}

							return response()->json(['asset' => $asset], 202);
						}
					}					
				
				    $asset->image = $imageUrl;
			    
					$asset->save();
				}
		    }
		    
		    $asset->price = $request->price*100;
		    
		    $asset->save();
		    
		    /* Fill fields public */
		    $asset_fields = AssetField::get();
		    
		    foreach($asset_fields as $asset_field) {
			    $field = 'field_'.$asset_field->id;
			    $asset->$field = '';
			    
			    $field_info = AssetFieldsInfo::where('asset_id', $asset->id)->where('asset_field_id', $asset_field->id)->first();
			    
			    if($customer_settings->check_explicit_content) {
				    if(self::detectAdultContent($request->$field)) {
					    return response()->json(['asset' => $asset], 202);
					}
				}
			    
			    if($field_info) {
				    $field_info->field_value = $request->$field;
			    } else {
				    $field_info = new AssetFieldsInfo;
				    $field_info->asset_id = $asset->id;
				    $field_info->asset_field_id = $asset_field->id;
				    $field_info->field_value = $request->$field;
			    }
			    
			    $field_info->save();
		    }
		    
		    /* Check if user is linked, if not check if user is found */
		    if($request->email) {
			    $user = User::where('email', $request->email)->first();
			    
			    if($user) {
				    $role = RoleUser::where('user_id', $user->id)->where('customer_id', $asset->customer_id)->first();
				    
				    if($role) {
					} else {
					    $asset->user_id = $user->id;
						$asset->save();
						
						$user->roles()->attach(Role::where('name', 'user')->first());
					    
					    $role = RoleUser::where('user_id', $user->id)->latest()->first();
					    $role->customer_id = $asset->customer_id;
					    $role->save();
					}
			    }
		    }
		    
		    /* Remove old cats */
		    if(isset($request->categories)) {
			    AssetCategory::where('asset_id', $asset->id)->delete();
			    
			    foreach($request->categories as $category) {		    
				    $asset_cat = new AssetCategory;
				    
				    $asset_cat->fill([
					    'category_id' => $category['id'],
					    'asset_id' => $asset->id
					]);
					
					$asset_cat->save();
			    }
			}
		    
		    /* Remove old Languages */
		    if(isset($request->languages)) {
			    AssetLanguage::where('asset_id', $asset->id)->delete();
			    
			    foreach($request->languages as $language) {		    
				    $asset_lang = new AssetLanguage;
				    
				    $asset_lang->fill([
					    'language_id' => $language['id'],
					    'asset_id' => $asset->id
					]);
					
					$asset_lang->save();
			    }
			}
		    
		    return response()->json(['asset' => $asset], 200);
		}
    }
    
    public function head_categories() {
	    /* Search customers */
	    $customer = \Request::header('Customer');
	    
	    $categories = Category::where('category_id', NULL)->where('active', 1)->where('customer_id', $customer)->orderBy('order')->get();
	    
	    return response()->json(['categories' => $categories], 200);
    }
    
    public function categories() {
	    /* Search customers */
	    $customer = \Request::header('Customer');
		$has_subcats = false;
	    
	    if(isset($_GET['structured']) && $_GET['structured']) {
		    $categories = array();
		    
		    $head_categories = Category::orderBy('order')->where('active', 1)->where('category_id', null)->where('customer_id', $customer)->get();
		    
		    foreach($head_categories as $head_category) {
			    $subcats = Category::orderBy('order')->where('active', 1)->where('category_id', $head_category->id)->where('customer_id', $customer)->get();
			    
			    if(count($subcats)) {
				    $has_subcats = true;
				    
				    $categories[] = array(
					    'id' => $head_category->id,
					    'name' => $head_category->name,
					    'headcat' => $head_category->name,
					    'headcat_details' => $head_category,
					    'subcats' => $subcats
				    );
				} else {
					$categories[] = array(
					    'id' => $head_category->id,
					    'name' => $head_category->name,
					    'headcat' => $head_category->name,
					    'headcat_details' => $head_category,
					    'subcats' => array($head_category)
				    );
				}
		    }
		    
		    if(!$has_subcats) {
			    $categories_list = Category::orderBy('order')->where('active', 1)->where('customer_id', $customer)->get();
			    
/*
			    $categories[] = array(
				    'id' => 'all',
				    'name' => 'All',
				    'headcat' => 'All',
				    'subcats' => $categories_list
			    );
*/
		    }
	    } else {
		    $categories = Category::orderBy('order')->where('active', 1)->where('customer_id', $customer)->get();
	    }
	    
	    return response()->json(['categories' => $categories, 'hassubcats' => $has_subcats], 200);
    }
    
    public function admin_categories() {	    
	    /* Search customers */
	    $customer = \Request::header('Customer');
	    
	    $categories = Category::where('category_id', NULL)->where('active', '!=', 2)->where('customer_id', $customer)->orderBy('order')->get();
	    $categories = $categories->toArray();
	    
	    foreach($categories as $key => $category) {
		    $sub_categories = Category::where('category_id', $category['id'])->where('active', '!=', 2)->where('customer_id', $customer)->orderBy('order')->get()->toArray();
		    $categories[$key]['subcategories'] = $sub_categories;
	    }
	    
	    return response()->json(['categories' => $categories], 200);
    }
    
	public function search() {
		$category = (isset($_GET['category']))?$_GET['category']:'';
		$language = (isset($_GET['language']))?$_GET['language']:'';
	    $startPrice = ($_GET['start'])?$_GET['start']:0;
	    $endPrice = ($_GET['end'])?$_GET['end']:99999999;
		
		$asset_category_ids = $asset_language_ids = array();
		
		$sub_categories = array();
		
		if($category && $category != null) {
        	$sub_categories = Category::where('category_id', $category)->where('active', 1)->get();
        	
        	$category_find = Category::where('id', $category)->first();
        	
        	if($category_find->category_id) {
	        	$sub_categories = Category::where('category_id', $category_find->category_id)->get();
        	}
        }
		
		if($category && $category != null) {
			$category = Category::where('id', $category)->first();
			
			$asset_category_ids = AssetCategory::where('category_id', $category->id)->pluck('asset_id');
			$asset_category_ids = $asset_category_ids->toArray();
		}
		
		if($language && $language != null) {
			$language = Language::where('id', $language)->first();
			
			$asset_language_ids = AssetLanguage::where('language_id', $language->id)->pluck('asset_id');
			$asset_language_ids = $asset_language_ids->toArray();
		}
		
		if(count($asset_category_ids) && count($asset_language_ids) || isset($_GET['category']) && isset($_GET['language']) && $_GET['category'] != '' && $_GET['language'] != '') {
			$asset_ids = array_intersect($asset_category_ids, $asset_language_ids);
		} else {
			if(count($asset_category_ids)) {
				$asset_ids = $asset_category_ids;
			} else if(count($asset_language_ids)) {
				$asset_ids = $asset_language_ids;
			} else {
				if(isset($_GET['category']) && isset($_GET['language']) && $_GET['category'] == '' && $_GET['language'] == '') {
					$asset_ids = Asset::where('active', 1)->pluck('id');
					$asset_ids = $asset_ids;
				} else {
					$asset_ids = array();
				}
			}
		}
		
		if(count($asset_ids)) {
		    /* Search customers */
		    $customer = \Request::header('Customer');
		    
			$customer_settings = CustomerSetting::where('customer_id', $customer)->first();
			
		    /* Get all and also get the price with the update */
		    if($customer_settings->markup_type == 1) {
			    $customer_settings->markup = $customer_settings->markup*100;
			    $assets = Asset::select(DB::raw('*, price + '.$customer_settings->markup.' AS markup_price'));
		    } else {
			    $assets = Asset::select(DB::raw('*, price * 1.'.$customer_settings->markup.' AS markup_price'));
		    }
			$assets->where('customer_id', $customer);
		    
			$assets->where('active', 1);
			
			$assets->where('price', '>=', $startPrice*100);
			$assets->where('price', '<=', $endPrice*100);
			
			$assets->orderBy('featured', 'desc');
		    
		    switch($customer_settings->sorting) {
			    case 1:
			    	$assets->orderBy('title', 'asc');
			    	break;
			    case 2:
			    	$assets->orderBy('title', 'desc');
			    	break;
			    case 3:
			    	$assets->inRandomOrder();
			    	break;
		    }
		    
			$assets = $assets->whereIn('id', $asset_ids);
	        
	        $assets = $assets->get();
	    } else {
		    $assets = array();
	    }

        return response()->json(['asset_category_ids' => $asset_category_ids, 'asset_language_ids' => $asset_language_ids, 'asset_ids' => $asset_ids, 'assets' => $assets, 'categories' => $sub_categories], 200);
	}
    
    public function search_old() {
	    $term = urldecode($_GET['term']);
	    
	    $category = Category::where('name', $term)->first();
	    $result = false;
/*
	    $sub_categories = false;

		if(!$category) {
			$curl = curl_init();
			
			curl_setopt_array($curl, array(
			  CURLOPT_URL => 'http://ec2-16-171-150-252.eu-north-1.compute.amazonaws.com:8080/label',
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS =>'{"text": "'.$term.'"}',
			  CURLOPT_HTTPHEADER => array(
			    'Content-Type: application/json'
			  ),
			));
			
			$response = curl_exec($curl);
			
			curl_close($curl);
			
			$result = json_decode($response);
			
			$result->label = ucwords($result->label);
			
			if($result->label) {			
				$category = Category::where('name', $result->label)->first();
			} else {
				$category = false;
			}
		}
		
		$assets = Asset::orderBy('title', 'asc');
		
		if($category) {		    
		    if($category->category_id != null) {
			    $sub_categories = Category::where('category_id', $category->category_id)->get();
				$asset_ids = AssetCategory::where('category_id', $category->category_id)->pluck('asset_id');
			} else {
			    $sub_categories = Category::where('category_id', $category->id)->get();
			    $sub_cat_ids = Category::where('category_id', $category->id)->pluck('id');
				$asset_ids = AssetCategory::whereIn('category_id', $sub_cat_ids)->pluck('asset_id');
			}
		    
		    if(count($asset_ids)) {
			    $asset_ids = $asset_ids->toArray();
			    
			    $assets = $assets->whereIn('id', $asset_ids);
		    } else {
		   		$categories = Category::where('category_id', $category->category_id)->pluck('id');
			    $asset_ids = AssetCategory::whereIn('category_id', $categories)->pluck('asset_id');
			    
			    if(count($asset_ids)) {
				    $asset_ids = $asset_ids->toArray();
				    
				    $assets = $assets->whereIn('id', $asset_ids);
			    }
		    }
	    }
*/

		$asset_ids = AssetCategory::where('category_id', $category->id)->pluck('asset_id');
			    
	    $asset_ids = $asset_ids->toArray();
	    
	    $assets = Asset::orderBy('title', 'asc');
		$assets = $assets->whereIn('id', $asset_ids);
        
        $assets = $assets->get();
        
        $sub_categories = array();

        return response()->json(['assets' => $assets, 'feedback' => $result, 'categories' => $sub_categories], 200);
    }
}
