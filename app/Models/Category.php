<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $category_id
 * @property string $name
 * @property string $image
 * @property string $created_at
 * @property string $updated_at
 * @property integer $customer_id
 * @property boolean $active
 * @property Category $category
 * @property AssetCategory[] $assetCategories
 * @property CategoryCustomer[] $categoryCustomers
 */
class Category extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['category_id', 'name', 'name_fr', 'name_en', 'image', 'created_at', 'updated_at', 'customer_id', 'active', 'question_1', 'question_2', 'question_3'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assetCategories()
    {
        return $this->hasMany('App\Models\AssetCategory');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categoryCustomers()
    {
        return $this->hasMany('App\Models\CategoryCustomer');
    }
}
