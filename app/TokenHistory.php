<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TokenHistory extends Model
{
	protected $fillable = ['id', 'user_id', 'token', 'description', 'name'];
	protected $table = 'token_history';
}	
