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
}
