<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $asset_field_id
 * @property integer $asset_id
 * @property integer $user_id
 * @property string $field_value
 * @property string $created_at
 * @property string $updated_at
 */
class AssetFieldsInfo extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'asset_fields_info';

    /**
     * @var array
     */
    protected $fillable = ['asset_field_id', 'asset_id', 'user_id', 'field_value', 'created_at', 'updated_at'];
}
