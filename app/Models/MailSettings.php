<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $customer_id
 * @property string $mail_newbooking_subject_nl
 * @property string $mail_newbooking_subject_fr
 * @property string $mail_newbooking_subject_en
 * @property string $mail_newbooking_text_nl
 * @property string $mail_newbooking_text_fr
 * @property string $mail_newbooking_text_en
 * @property string $mail_gettoknow_subject_nl
 * @property string $mail_gettoknow_subject_fr
 * @property string $mail_gettoknow_subject_en
 * @property string $mail_gettoknow_text_nl
 * @property string $mail_gettoknow_text_fr
 * @property string $mail_gettoknow_text_en
 * @property string $mail_estimate_subject_nl
 * @property string $mail_estimate_subject_fr
 * @property string $mail_estimate_subject_en
 * @property string $mail_estimate_text_nl
 * @property string $mail_estimate_text_fr
 * @property string $mail_estimate_text_en
 * @property string $mail_newmessage_subject_nl
 * @property string $mail_newmessage_subject_fr
 * @property string $mail_newmessage_subject_en
 * @property string $mail_newmessage_text_nl
 * @property string $mail_newmessage_text_fr
 * @property string $mail_newmessage_text_en
 * @property string $created_at
 * @property string $updated_at
 * @property Customer $customer
 */
class MailSettings extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['customer_id', 'mail_newbooking_subject_nl', 'mail_newbooking_subject_fr', 'mail_newbooking_subject_en', 'mail_newbooking_text_nl', 'mail_newbooking_text_fr', 'mail_newbooking_text_en', 'mail_gettoknow_subject_nl', 'mail_gettoknow_subject_fr', 'mail_gettoknow_subject_en', 'mail_gettoknow_text_nl', 'mail_gettoknow_text_fr', 'mail_gettoknow_text_en', 'mail_estimate_subject_nl', 'mail_estimate_subject_fr', 'mail_estimate_subject_en', 'mail_estimate_text_nl', 'mail_estimate_text_fr', 'mail_estimate_text_en', 'mail_newmessage_subject_nl', 'mail_newmessage_subject_fr', 'mail_newmessage_subject_en', 'mail_newmessage_text_nl', 'mail_newmessage_text_fr', 'mail_newmessage_text_en', 'mail_final_booking_subject_nl', 'mail_final_booking_subject_fr', 'mail_final_booking_subject_en', 'mail_final_booking_text_nl', 'mail_final_booking_text_fr', 'mail_final_booking_text_en', 'mail_extra_subject_nl', 'mail_extra_subject_fr', 'mail_extra_subject_en', 'mail_extra_text_nl', 'mail_extra_text_fr', 'mail_extra_text_en', 'created_at', 'updated_at', 'mail_reminder_estimate_subject_nl', 'mail_reminder_estimate_subject_fr', 'mail_reminder_estimate_subject_en', 'mail_reminder_estimate_text_nl', 'mail_reminder_estimate_text_fr', 'mail_reminder_estimate_text_en', 'mail_reminder_booking_subject_nl', 'mail_reminder_booking_subject_fr', 'mail_reminder_booking_subject_en', 'mail_reminder_booking_text_nl', 'mail_reminder_booking_text_fr', 'mail_reminder_booking_text_en', 'mail_booking_confirm_subject_nl', 'mail_booking_confirm_subject_fr', 'mail_booking_confirm_subject_en', 'mail_booking_confirm_text_nl', 'mail_booking_confirm_text_fr', 'mail_booking_confirm_text_en', 'mail_final_booking_asset_subject_nl', 'mail_final_booking_asset_subject_fr', 'mail_final_booking_asset_subject_en', 'mail_final_booking_asset_text_nl', 'mail_final_booking_asset_text_fr', 'mail_final_booking_asset_text_en'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }
}
