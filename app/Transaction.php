<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{

    protected $fillable = ['offer_id', 'user_id'];
	public function offer()
    {
        return $this->belongsTo('App\Offer');
    }
}
