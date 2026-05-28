<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Hash;

use Auth;

use App\Models\Category;

class CategoryController extends Controller
{
	public function toggle_archive($id) {
	    $customer = \Request::header('Customer');
	    
	    $category = Category::where('id', $id)->first();
	    
		$category->active = 2;
	    
	    $category->save();
	    
	    /* Get categories */
	    $categories = Category::where('category_id', NULL)->where('active', '!=', 2)->where('customer_id', $customer)->orderBy('order')->get();
	    $categories = $categories->toArray();
	    
	    foreach($categories as $key => $category) {
		    $sub_categories = Category::where('category_id', $category['id'])->where('active', '!=', 2)->where('customer_id', $customer)->orderBy('order')->get()->toArray();
		    $categories[$key]['subcategories'] = $sub_categories;
	    }
	    
	    return response()->json(['categories' => $categories], 200);
    }
    
	public function toggle_active($id) {
	    $customer = \Request::header('Customer');
	    
	    $category = Category::where('id', $id)->first();
	    
	    if($category->active) {
		    $category->active = 0;
	    } else {
		    $category->active = 1;
	    }
	    
	    $category->save();
	    
	    /* Get categories */
	    $categories = Category::where('category_id', NULL)->where('active', '!=', 2)->where('customer_id', $customer)->orderBy('order')->get();
	    $categories = $categories->toArray();
	    
	    foreach($categories as $key => $category) {
		    $sub_categories = Category::where('category_id', $category['id'])->where('active', '!=', 2)->where('customer_id', $customer)->orderBy('order')->get()->toArray();
		    $categories[$key]['subcategories'] = $sub_categories;
	    }
	    
	    return response()->json(['categories' => $categories], 200);
    }
    
    public function change_order($id1, $type) {
	    $customer = \Request::header('Customer');
	    
	    $category = Category::where('id', $id1)->first();
	    
	    \DB::enableQueryLog();
	    
		$order = $category->order;
		$parent = ($category->category_id) ? false : true;
			    
		if ($type == 'up') {
		    $category2 = Category::where('order', '<', $order)->orderBy('order', 'desc');
		} else {
		    $category2 = Category::where('order', '>', $order)->orderBy('order', 'asc');
		}
			    
		if ($parent) {
		    $category2->where('category_id', NULL);
		} else {
		    $category2->where('category_id', '>', 0);
		}
		
		$category2->where('customer_id', $customer)->where('active', '!=', 2);
			    
		$category2 = $category2->first();
		
		// Get the SQL statement
		$sqlStatement = \DB::getQueryLog();
	    
	    $id2 = $category2->id;
	    
	    // Get all categories and add them to an array
	    $categories = Category::where('customer_id', $customer)->where('active', '!=', 2)->orderBy('order')->get()->toArray();
	
	    // Find the indices of the two categories to swap
	    $index1 = null;
	    $index2 = null;
	    
	    foreach ($categories as $index => $category) {
	        if ($category['id'] == $id1) {
	            $index1 = $index;
	        }
	        if ($category['id'] == $id2) {
	            $index2 = $index;
	        }
	    }
	
	    // Check if both categories exist
	    if ($index1 === null || $index2 === null) {
	        return response()->json(['message' => 'One or both categories not found'], 404);
	    }
	
	    // Swap the positions of the two categories in the array
	    $tempCategory = $categories[$index1];
	    $categories[$index1] = $categories[$index2];
	    $categories[$index2] = $tempCategory;
	
	    // Update the order values of the categories in the database
	    foreach ($categories as $index => $category) {
	        Category::where('id', $category['id'])->update(['order' => $index]);
	    }
	    
	    /* Get categories */
	    $categories = Category::where('customer_id', $customer)->where('category_id', NULL)->where('active', '!=', 2)->orderBy('order')->get();
	    $categories = $categories->toArray();
	    
	    foreach($categories as $key => $category) {
		    $sub_categories = Category::where('customer_id', $customer)->where('category_id', $category['id'])->where('active', '!=', 2)->orderBy('order')->get()->toArray();
		    $categories[$key]['subcategories'] = $sub_categories;
	    }
	    
	    return response()->json(['category' => $category, 'categories' => $categories], 200);
	}
    
    public function show($id)
    {	    
	    $customer = \Request::header('Customer');
	    
	    $category = Category::where('customer_id', $customer)->where('id', $id)->first()->toArray();
	    
	    $sub_categories = Category::where('customer_id', $customer)->where('category_id', $category['id'])->get()->toArray();
		$category['subcategories'] = $sub_categories;
		
		if($category['category_id']) {
			$category['category'] = Category::where('customer_id', $customer)->where('id', $category['category_id'])->get()->toArray();
			$category['category'] = $category['category'][0];
		}
	    
        return response()->json(['category' => $category], 200);
    }
    
    public function store(Request $request) {	
	    $customer = \Request::header('Customer');
	        	    
	    $category = new Category;
	    $category->fill([
		    'name' => '',
		    'name_fr' => '',
		    'name_en' => '',
		    'image' => '',
		    'category_id' => NULL
	    ]);
	    
	    $category->name = $request->name;
	    $category->name_fr = $request->name_fr;
	    $category->name_en = $request->name_en;
	    $category->customer_id = $customer;
	    
	    if($request->category) {
		    $category->category_id = $request->category['id'];
	    }
	    
	    if($request->image) {
		    $category->image = $request->image;
	    }
	    
	    $category->save();
	    
	    return response()->json(['category' => $category], 200);
    }
    
    public function update(Request $request, $id = false) {	    
	    $category = Category::where('id', $id)->first(); 
	    
	    $category->name = $request->name;
	    $category->name_fr = $request->name_fr;
	    $category->name_en = $request->name_en;
	    
	    if($request->category) {
		    $category->category_id = $request->category['id'];
	    }
	    
	    if($request->image) {
		    $category->image = $request->image;
	    }
	    
	    $category->save();
	    
	    return response()->json(['category' => $category], 200);
    }
}
