<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property boolean $monday
 * @property boolean $tuesday
 * @property boolean $wednesday
 * @property boolean $thursday
 * @property boolean $friday
 * @property boolean $saturday
 * @property boolean $sunday
 * @property string $created_at
 * @property string $updated_at
 */
class SettingsAgenda extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'settings_agenda';

    /**
     * @var array
     */
    protected $fillable = ['user_id', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday', 'created_at', 'updated_at', 'monday_hour_start', 'tuesday_hour_start', 'wednesday_hour_start', 'thursday_hour_start', 'friday_hour_start', 'saturday_hour_start', 'sunday_hour_start', 'monday_hour_end', 'tuesday_hour_end', 'wednesday_hour_end', 'thursday_hour_end', 'friday_hour_end', 'saturday_hour_end', 'sunday_hour_end'];
}
