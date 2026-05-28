<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $question
 * @property mixed $response
 * @property integer $thumbs_up
 * @property integer $thumbs_down
 * @property float $time_taken
 * @property string $created_at
 * @property string $updated_at
 */
class Search extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['question', 'response', 'thumbs_up', 'thumbs_down', 'time_taken', 'created_at', 'updated_at'];
}
