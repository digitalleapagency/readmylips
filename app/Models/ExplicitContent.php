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
class ExplicitContent extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'explicit_content';

    /**
     * @var array
     */
    protected $fillable = ['description', 'feedback', 'asset_id', 'created_at', 'updated_at'];
}
