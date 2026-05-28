<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $language_code
 * @property string $created_at
 * @property string $updated_at
 * @property LanguageCustomer[] $languageCustomers
 */
class Language extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'language_code', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function languageCustomers()
    {
        return $this->hasMany('App\Models\LanguageCustomer');
    }
}
