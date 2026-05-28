<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $user_id
 * @property string $token
 * @property string $refresh_token
 * @property string $agenda_provider
 * @property boolean $active
 * @property string $created_at
 * @property string $updated_at
 */
class Setting extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['user_id', 'token', 'refresh_token', 'agenda_provider', 'active', 'created_at', 'updated_at'];
}
