<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $domain
 * @property string $created_at
 * @property string $updated_at
 * @property LanguageCustomer[] $languageCustomers
 * @property Asset[] $assets
 * @property CategoryCustomer[] $categoryCustomers
 * @property Booking[] $bookings
 * @property User[] $users
 */
class Customer extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'email', 'domain', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function languageCustomers()
    {
        return $this->hasMany('App\Models\LanguageCustomer');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assets()
    {
        return $this->hasMany('App\Models\Asset');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categoryCustomers()
    {
        return $this->hasMany('App\Models\CategoryCustomer');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bookings()
    {
        return $this->hasMany('App\Models\Booking');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany('App\Models\User');
    }
}
