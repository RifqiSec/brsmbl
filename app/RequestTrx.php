<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestTrx extends Model
{
	protected $table = 'requests';
	protected $fillable = ['user_id', 'vehicle_id', 'city_id', 'color', 'qty', 'payment_method', 'dp', 'installment'];

	public function offers() {
		return $this->hasMany('App\Offer', 'request_id');
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
