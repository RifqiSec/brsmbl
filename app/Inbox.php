<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inbox extends Model
{
	protected $fillable = ['from', 'to', 'message', 'type', 'title'];

	public function from() {
		return $this->belongsTo('App\User', 'from');
	}

	public function to() {
		return $this->belongsTo('App\User', 'to');
	}
}
