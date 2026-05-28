<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $code
 * @property integer $value
 * @property boolean $active
 * @property string $created_at
 * @property string $updated_at
 */
class Voucher extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['code', 'value', 'active', 'created_at', 'updated_at'];
}
