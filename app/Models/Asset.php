<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Asset extends Model
{
    use HasFactory;
	use LogsActivity;
    
    protected $fillable = ['title', 'subtitle', 'description', 'title_fr', 'subtitle_fr', 'description_fr', 'title_en', 'subtitle_en', 'description_en', 'image', 'asset_type_id', 'customer_id', 'active', 'price', 'price_hidden', 'email', 'rating', 'bookings', 'views', 'phone', 'drupal_id'];
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*']);
        // Chain fluent methods for configuration options
    }
}
