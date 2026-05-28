<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property boolean $google
 * @property boolean $vectera
 * @property integer $booking_flow
 * @property boolean $sorting
 * @property string $created_at
 * @property string $updated_at
 * @property string $asset_label_single
 * @property string $asset_label_double
 * @property string $asset_label_featured
 * @property string $asset_label_pricing
 * @property integer $length_appointment
 * @property boolean $show_pricing
 */
class CustomerSetting extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['google', 'vectera', 'booking_flow', 'sorting', 'created_at', 'updated_at', 'asset_label_single', 'asset_label_double', 'asset_label_featured', 'asset_label_pricing', 'length_appointment', 'show_pricing', 'asset_label_single_fr', 'asset_label_double_fr', 'asset_label_featured_fr', 'asset_label_pricing_fr', 'asset_label_single_en', 'asset_label_double_en', 'asset_label_featured_en', 'asset_label_pricing_en', 'change_price', 'markup', 'markup_type', 'show_slider', 'extra_info_invoice'];
}
