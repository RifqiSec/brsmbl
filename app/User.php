<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fullname', 'email', 'password', 'nik', 'noaccount', 'phone', 'photo', 'warning', 'token', 'role', 'challenge_code', 'is_confirm'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function sales()
    {
        return $this->belongsToMany('App\Dealer', 'user_sales')->withPivot('is_active');
    }

    public function city()
    {
        return $this->belongsTo('App\City');
    }


    public function dealer()
    {
        return $this->hasMany('App\Dealer');
    }

    public function tokenHistory()
    {
        return $this->hasMany('App\TokenHistory');
    }

    public function vehicle()
    {
        return $this->belongsToMany('App\Vehicle', 'sales_vehicles', 'sales_id');
    }
}
