<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $customer_id
 * @property string $field_name
 * @property boolean $editable
 * @property boolean $active
 * @property string $created_at
 * @property string $updated_at
 */
class AssetField extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['customer_id', 'field_name', 'editable', 'active', 'created_at', 'updated_at', 'assets_tab_id', 'field_type', 'customer_id'];
}
