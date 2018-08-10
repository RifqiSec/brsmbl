<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestTrx extends Model
{
	protected $table = 'requests';
	// protected $fillable = ['from', 'to', 'message', 'type'];

	public function offers() {
		return $this->belongsTo('App\Offer');
	}

	public function vehicle()
	{
		return $this->belongsTo('App\Vehicle');
	}

	public function sales()
	{
		return $this->belongsToMany('App\User', 'selected_sales', 'request_id', 'sales_id');
	}

	public function user()
	{
		return $this->belongsTo('App\User');
	}

	public function city()
	{
		return $this->belongsTo('App\City');
	}
}
