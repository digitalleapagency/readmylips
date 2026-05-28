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
class Meeting extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['booking_id', 'customer_id', 'meetingId', 'title', 'start_date', 'end_date', 'user_id', 'roomName', 'roomUrl', 'hostRoomUrl', 'viewerRoomUrl', 'audio_link', 'text', 'summary', 'invitees', 'text_invite'];
}
