<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Category;
use App\Models\AssetCategory;
use App\Models\Asset;
use App\Models\Search;
use App\Models\CustomerSetting;

use DB;

class AiController extends Controller
{
    public function questions_for_category($id) {
	    $category = Category::where('id', $id)->first();
	    
	    if($category->question_1 && $category->question_2 && $category->question_3) {
		    $array = array(
			    $category->question_1,
			    $category->question_2,
			    $category->question_3
		    );
	    } else {
		    $jsonString = json_encode(array(
			  "providers" => "openai",
			  "text" => 'Generate three questions related to the category of '.$category->name.' and present them in JSON format. Here is an example of the expected output:[{"question": "What is the importance of SEO in online marketing?","question": "How can social media be used effectively for online marketing?","question": "What are the key metrics to track in an online marketing campaign?"}]',
			  "max_tokens" => 1000,
			  "temperature" => 0.3,
			  "fallback_providers" => ""
			));
	
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://api.edenai.run/v2/text/generation');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
			    "authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoiZjZhOWNiNTEtNjBlYy00ZjczLWE3NTktMDllMzFhZmQ4ZWI3IiwidHlwZSI6ImFwaV90b2tlbiJ9.ct5ZHDM2-iFDCCJbML0F4N5U5jFhPCvkDuqG6xXxzBc",
			    'content-type: application/json',
			]);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonString);
			
			$response = curl_exec($ch);
			$result = json_decode($response);
			
			curl_close($ch);
			
			$questions = json_decode(trim($result->openai->generated_text));
			
			foreach($questions as $key => $question) {
				$field = 'question_'.($key+1);
				
				$category->$field = $question->question;
			}
			
			$category->save();
			
			$array = array(
			    $category->question_1,
			    $category->question_2,
			    $category->question_3
		    );
		}
		
		return response()->json(['questions' => $array], 200);
    }
    
    public function search() {
	    $start_time = microtime(true);
	    $question = $_GET['question'];
	    $startPrice = $_GET['start'];
	    $endPrice = ($_GET['end'])?$_GET['end']:99999999;
	    
	    $customer = \Request::header('Customer');
	    
	    /* Check if we can find profiles that are linked to this search */
	    $asset_ids = Asset::where('title', 'like', '%' . $question . '%')->where('active', 1)->pluck('id');
	    
	    if(count($asset_ids)) {
		    $categories = array();
		    $category_string = '';
		    $jsonString = '';
		    $result = '';
		    $search = array();
		    $cat_ids = array();
	    } else {
			$categories = Category::where('active', 1)->where('customer_id', $customer)->get();
		    $category_string = '';
		    
		    foreach($categories as $category) {
			    $category_string .= $category->name.' (id: '.$category->id.', name_fr: '.$category->name_fr.', name_en: '.$category->name_en.'), ';
		    }
		    
		    $jsonString = json_encode(array(
			    'providers' => 'openai',
			    "temperature" => 0.1,
				"max_tokens" => 1000,
			    'text' => $question,
			    'chatbot_global_action' => 'As an assistant you should consider these categories (id of the category is behind it) and link the question or text I sent you to all categories with a percentage of how accurate they link to that category. Return the list in a json like format and rank them already from most matching to least. Besides the category and a percentage also add a reason for linking the category. Also return the name as well as the id in the json.
			    
	The format of the json array should be categories and inside each category, an id, name, percentage and reason. (Example of the format: { "categories": [ { "id": 106, "name": "Category 2", "name_fr": "Category 2", "name_en": "Category 2", "percentage": 80, "reason": "The text contains keywords related to strategy." }, { "id": 86, "name": "Category 1", "name_fr": "Category 1", "name_en": "Category 1", "percentage": 70, "reason": "The text mentions the execution of a strategy." } ] })
	
	In the JSON I want id, name, name_fr, name_en, percentage, and reason.
	
	List of categories:'.$category_string		    
		    ), JSON_PRETTY_PRINT);
		    
		    $ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://api.edenai.run/v2/text/chat');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
			    "authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoiZjZhOWNiNTEtNjBlYy00ZjczLWE3NTktMDllMzFhZmQ4ZWI3IiwidHlwZSI6ImFwaV90b2tlbiJ9.ct5ZHDM2-iFDCCJbML0F4N5U5jFhPCvkDuqG6xXxzBc",
			    'content-type: application/json',
			]);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonString);
			
			$response = curl_exec($ch);
			$result = json_decode($response);
			
			curl_close($ch);
			
			$categories = json_decode(trim($result->openai->generated_text));
			$cat_ids = array();
			
			if($categories && isset($categories->categories) && $categories->categories) {
				foreach($categories->categories as $key => $category) {
					if($category->percentage > 65) {
						$cat_ids[] = $category->id;
					} else {
						unset($categories->categories[$key]);
					}
				}
			} else {
				$cat_ids = array();
			}
			
			/* Get assets */
			$asset_ids = AssetCategory::whereIn('category_id', $cat_ids)->pluck('asset_id');
			
		    // Set attributes of the Search model
		    $end_time = microtime(true);
		    $execution_time = $end_time - $start_time;
		    $search = new Search;
		    $search->question = $question;
		    $search->response = $result->openai->generated_text;
		    $search->thumbs_up = 0;
		    $search->thumbs_down = 0;
		    $search->time_taken = $execution_time; // You can set the actual time taken
		
		    // Save the Search model to the database
		    $search->save();
		}
		
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
		$assets = $assets->whereIn('id', $asset_ids);
		$assets->orderBy('featured', 'desc');
	    
	    $customer_settings = CustomerSetting::where('customer_id', $customer)->first();
	    
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
	            
        $assets = $assets->get();
        
        if ($assets->isEmpty()) {
		    // Fallback to a different source, e.g., selecting all assets
		    $assets = Asset::where('active', 1)->get();
		}        
        
        if($categories && isset($categories->categories) && $categories->categories) {
	        $cats = $categories->categories;
	    } else {
		    $cats = array();
	    }
		
		return response()->json(['startPrice' => $startPrice, 'categories' => $cats, 'assets' => $assets, 'result' => $result, 'categories_feedback' => $categories, 'json_string' => json_decode($jsonString), 'search' => $search], 200);
    }
    
    public function thumbs($id, $thumbs) {
	    $search = Search::where('id', $id)->first();
	    
	    if($thumbs == 1) {
		    $search->thumbs_up = 1;
		    $search->thumbs_down = 0;
		    $search->save();
	    } else {
		    $search->thumbs_up = 0;
		    $search->thumbs_down = 1;
		    $search->save();
	    }
	    
		return response()->json(['id' => $id, 'thumbs' => $thumbs], 200);
    }
    
    public function search_old() {
	    $question = $_GET['question'];
	    
	    $examples = array();
	    $labels = array();
	    
	    $categories = Category::get();
	    
	    foreach($categories as $category) {
		    $labels[] = $category->name;
		    $examples[] = array(
			    $category->name,
			    $category->question_1
		    );
		    
		    $examples[] = array(
			    $category->name,
			    $category->question_2
		    );
		    
		    $examples[] = array(
			    $category->name,
			    $category->question_3
		    );
	    }
	    
	    $jsonString = json_encode(array(
		  "providers" => "openai",
		  "labels" => $labels,
		  "texts" => array(
			  $question
		  ),
		  "examples" => $examples
		), JSON_PRETTY_PRINT);
	    
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://api.edenai.run/v2/text/custom_classification');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
		    "authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoiZjZhOWNiNTEtNjBlYy00ZjczLWE3NTktMDllMzFhZmQ4ZWI3IiwidHlwZSI6ImFwaV90b2tlbiJ9.ct5ZHDM2-iFDCCJbML0F4N5U5jFhPCvkDuqG6xXxzBc",
		    'content-type: application/json',
		]);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonString);
		
		$response = curl_exec($ch);
		$result = json_decode($response);
		
		curl_close($ch);
		
		dd($result);
    }
}
