<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dealer extends Model
{
	protected $fillable = ['name', 'company_type', 'trademark', 'address', 'city_id', 'user_id'];

	public function user()
	{
		return $this->belongsTo('App\User');
	}

	public function city()
	{
		return $this->belongsTo('App\City');
	}

}
