<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $tab_name
 * @property string $tab_icon_code
 * @property boolean $active
 * @property string $created_at
 * @property string $updated_at
 */
class AssetTab extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['tab_name', 'tab_icon_code', 'active', 'created_at', 'updated_at', 'customer_id'];
}
