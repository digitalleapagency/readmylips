<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $asset_id
 * @property integer $language_id
 * @property string $created_at
 * @property string $updated_at
 */
class AssetLanguage extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['asset_id', 'language_id', 'created_at', 'updated_at'];
}
