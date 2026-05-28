<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

use DB;
use File;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetLanguage;
use App\Models\Language;
use App\Models\Category;

class WebhookController extends Controller
{
    private $validToken = 'b3Z6mY8jR1pK9tH4vL2cS7wQ0nD5xF3aG0uT6V9lB2oJ8W1zM4X7nP5yR3'; // Replace with your actual token

    public function __construct(Request $request)
    {
	    // Check for the Bearer token in the Authorization header
        $token = $request->bearerToken();

        if ($token !== $this->validToken) {
            // If the token is invalid, abort with a 401 Unauthorized response
            abort(response()->json(['message' => 'Unauthorized'], 401));
        }
    }

    /**
     * Get available filter criteria.
     * Returns the list of criteria that can be used to filter assets.
     */
    public function criteria()
    {
        $criteria = [
            ['name' => 'non-profit', 'value' => false, 'description' => 'Filter for non-profit organizations'],
        ];

        return response()->json(['criteria' => $criteria]);
    }

    /**
     * Get all categories (topics) for customer_id = 1.
     * Categories represent the topics/themes that assets can be associated with.
     */
    public function categories()
    {
        $categories = Category::where('customer_id', 1)
            ->where('active', 1)
            ->select('id', 'name', 'name_fr', 'name_en', 'active')
            ->orderBy('name')
            ->get();

        return response()->json($categories);
    }

    /**
     * Get all topics (alias for categories, or keynote topics).
     * Returns categories as topics for filtering and display.
     */
    public function topics()
    {
        $topics = Category::where('customer_id', 1)
            ->where('active', 1)
            ->select('id', 'name', 'name_fr', 'name_en', 'active')
            ->orderBy('name')
            ->get();

        return response()->json(['topics' => $topics]);
    }

    /**
     * Get images for a specific asset.
     * Returns the images array (black-white, color, extra) for the given asset.
     */
    public function images($id)
    {
        $asset = Asset::where('customer_id', 1)
            ->where('active', 1)
            ->where('id', $id)
            ->first();

        if (!$asset) {
            return response()->json(['message' => 'Asset with ID ' . $id . ' not found'], 404);
        }

        $images = [
            ['type' => 'black-white', 'image' => $asset->image ?? ''],
            ['type' => 'color', 'image' => ''],
            ['type' => 'extra', 'image' => ''],
        ];

        return response()->json(['images' => $images]);
    }

    // Get all assets for customer_id = 1 and active = 1
    public function assets()
    {
        $assets = Asset::where('customer_id', 1)
                       ->where('active', 1);
                       
        if(isset($_GET['updated_at'])) {
	        $_GET['updated_at'] = $_GET['updated_at'] - 3600*2;
	        
	        $assets->where('updated_at', '>=', date('Y-m-d H:i:s', $_GET['updated_at']));
        }
                       
        $assets = $assets->get();

		foreach ($assets as $key => $asset) {
		    $imageUrl = $asset->image;
		
		    // Check if image URL is external
		    if (filter_var($imageUrl, FILTER_VALIDATE_URL) && Str::contains($imageUrl, 'readmylips.be')) {
		        try {
		            $imageContents = file_get_contents($imageUrl);
		            $fileExtension = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
		            $fileName = Str::random(10) . '.' . $fileExtension;
		            $destinationPath = public_path('profile_image');
		
		            // Ensure the uploads/speakers directory exists
		            if (!File::exists($destinationPath)) {
		                File::makeDirectory($destinationPath, 0755, true);
		            }
		
		            // Save the image in public/uploads/speakers
		            File::put($destinationPath . '/' . $fileName, $imageContents);
		
		            // Update the image URL to the local path (full URL)
		            $asset->image = asset('profile_image/' . $fileName);
		        } catch (\Exception $e) {
		            \Log::error('Image download failed for asset ID ' . $asset->id . ': ' . $e->getMessage());
		            continue; // Skip to the next asset if download fails
		        }
		    }
		
		    // Save the updated asset
		    $asset->save();
		    
		    $categories = AssetCategory::where('asset_id', $asset->id)->pluck('category_id');
		    $asset->categories = Category::whereIn('id', $categories)->where('active', 1)->select('id', 'name', 'name_fr', 'name_en', 'active')->get();
		    
		    $languages = AssetLanguage::where('asset_id', $asset->id)->pluck('language_id');
		    $asset->languages = Language::whereIn('id', $languages)->get();
		
		    // Create the images array with the specified structure
		    $asset->images = [
		        [
		            'type' => 'black-white',
		            'image' => $asset->image,
		        ],
		        [
		            'type' => 'color',
		            'image' => '',
		        ],
		        [
		            'type' => 'extra',
		            'image' => '',
		        ],
		    ];
		    
		    $asset->filters = [
			    [
				    'name' => 'non-profit',
				    'value' => false
			    ]
		    ];
		    
		    $asset->keynote_topics = array();
		}

        return response()->json($assets);
    }

    // Show a specific asset
    public function show($id)
    {
        $asset = Asset::where('customer_id', 1)
                      ->where('active', 1)
                       ->where('id', $id)
                      ->first();

        if (!$asset) {
            return response()->json(['message' => 'Asset with ID '.$id.' not found, not ewe use ID, not drupal ID'], 404);
        }
        
        $categories = AssetCategory::where('asset_id', $asset->id)->pluck('category_id');
	    $asset->categories = Category::whereIn('id', $categories)->where('active', 1)->select('id', 'name', 'name_fr', 'name_en', 'active')->get();
	    
	    $languages = AssetLanguage::where('asset_id', $asset->id)->pluck('language_id');
	    $asset->languages = Language::whereIn('id', $languages)->get();
	
	    // Create the images array with the specified structure
	    $asset->images = [
	        [
	            'type' => 'black-white',
	            'image' => $asset->image,
	        ],
	        [
	            'type' => 'color',
	            'image' => '',
	        ],
	        [
	            'type' => 'extra',
	            'image' => '',
	        ],
	    ];
	    
	    $asset->filters = [
		    [
			    'name' => 'non-profit',
			    'value' => false
		    ]
	    ];
	    
	    $asset->keynote_topics = array();

        return response()->json($asset);
    }

    // Create a new asset, using request data if available, else fetching from external source
    public function create(Request $request, $id)
    {
        $data = $request->all(); // Use POST data if provided

        if (empty($data)) {
            $data = $this->fetchExternalData($id);

            if (!$data) {
                return response()->json(['message' => 'No data found for the given ID'], 404);
            }
        }
        
        $asset = Asset::where('customer_id', 1)
                      ->where('active', 1)
                       ->where('drupal_id', $id)
                      ->first();

        if ($asset) {
            return response()->json(['message' => 'Asset already exists'], 404);
        }
        
        $data['drupal_id'] = $id;
        $data['customer_id'] = 1;
        $data['asset_type_id'] = 1;

        $asset = Asset::create($data);

        return response()->json(['message' => 'Asset created successfully', 'asset' => $asset], 201);
    }

    // Update an existing asset, using request data if available, else fetching from external source
    public function update(Request $request, $id)
    {
        $asset = Asset::where('customer_id', 1)
                      ->where('active', 1)
                       ->where('id', $id)
                      ->first();

        if (!$asset) {
            return response()->json(['message' => 'Asset with ID '.$id.' (ID, NOT drupal ID) not found'], 404);
        }

        $data = $request->all(); // Use PUT data if provided

/*
        if (empty($data)) {
            $data = $this->fetchExternalData($id);

            if (!$data) {
                return response()->json(['message' => 'No data found for the given ID'], 404);
            }
        }
*/

        $asset->update($data);

        return response()->json(['message' => 'Asset updated successfully', 'asset' => $asset]);
    }

    // Trigger asset update or create, using request data if available, else fetching from external source
    public function trigger(Request $request, $id)
    {
        $data = $request->all(); // Use POST/PUT data if provided

        if (empty($data)) {
            $data = $this->fetchExternalData($id);

            if (!$data) {
                return response()->json(['message' => 'No data found for the given ID'], 404);
            }
        }

        $asset = Asset::updateOrCreate(
            ['drupal_id' => $id, 'customer_id' => 1, 'active' => 1], 
            $data
        );

        return response()->json(['message' => 'Asset processed successfully', 'asset' => $asset]);
    }

    // Helper function to fetch data from an external endpoint
    private function fetchExternalData($id)
    {
        $response = Http::get("https://external.api.endpoint/{$id}");

        if ($response->successful()) {
            return $response->json(); // Assuming the API returns the necessary fields
        }

        return null;
    }
}
