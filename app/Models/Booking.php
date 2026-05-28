<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Musonza\Chat\Traits\Messageable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * @property integer $id
 * @property integer $asset_id
 * @property integer $customer_id
 * @property string $title
 * @property boolean $calendar_type
 * @property string $calendar_id
 * @property string $mollie_feedback
 * @property integer $category_id
 * @property integer $booking_type
 * @property integer $price
 * @property boolean $paid
 * @property string $created_at
 * @property string $updated_at
 * @property string $email
 * @property string $invoice_name
 * @property string $invoice_address
 * @property string $invoice_postal
 * @property string $invoice_city
 * @property string $invoice_email
 * @property string $date
 * @property string $date_hour_start
 * @property string $date_hour_end
 * @property string $date_2
 * @property string $date_2_hour_start
 * @property string $date_2_hour_end
 * @property string $json
 * @property Customer $customer
 * @property Asset $asset
 */
class Booking extends Model
{
    use Messageable;
	use LogsActivity;
    
    /**
     * @var array
     */
    protected $fillable = ['asset_id', 'customer_id', 'title', 'calendar_type', 'calendar_id', 'mollie_feedback', 'category_id', 'booking_type', 'price', 'paid', 'created_at', 'updated_at', 'mobile', 'email', 'invoice_name', 'invoice_address', 'invoice_postal', 'invoice_city', 'invoice_email', 'date', 'date_hour_start', 'date_hour_end', 'date_2', 'date_2_hour_start', 'date_2_hour_end', 'json', 'invoice_vat', 'booking_flow', 'accepted', 'finished', 'status', 'location', 'remark', 'estimate', 'extra_doc', 'refused_reason', 'customer_estimate_lines', 'customer_viewed', 'customer_accepted', 'customer_refused', 'customer_refused_reason', 'video_link', 'meeting_link', 'meeting_id', 'summary', 'audio_link', 'text_to_audio_id', 'amount_of_visitors', 'date_unknown', 'booking_seen', 'managing_user_initials', 'managing_user_id', 'keynote_subject', 'last_mail_send', 'last_action_admin', 'last_action_customer', 'tags'];
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*']);
        // Chain fluent methods for configuration options
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function asset()
    {
        return $this->belongsTo('App\Models\Asset');
    }
}
