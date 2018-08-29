<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
	public function request()
	{
		return $this->belongsTo('App\RequestTrx');
	}

	public function sales()
	{
		return $this->belongsTo('App\User', 'sales_id');
	}

	public function dealer()
	{
		return $this->belongsTo('App\Dealer');
	}

	public function transaction()
	{
		return $this->hasMany('App\Transaction');
	}
}
