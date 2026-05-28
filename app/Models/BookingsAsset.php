<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $booking_id
 * @property integer $asset_id
 * @property boolean $accepted
 * @property boolean $refused
 * @property string $refused_reason
 * @property string $estimate
 * @property boolean $booking_active
 * @property boolean $customer_active
 */
class BookingsAsset extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['booking_id', 'asset_id', 'accepted', 'refused', 'refused_reason', 'estimate', 'booking_active', 'customer_active', 'created_at', 'updated_at'];
}
