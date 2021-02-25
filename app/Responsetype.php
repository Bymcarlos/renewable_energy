<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Responsetype extends Model
{
    protected $fillable = [
		'title',
	];

	public function features()
	{
		return $this->hasMany('CtoVmm\Feature');
	}

	public function criteriafuncs(){
		return $this->hasMany('CtoVmm\Criteriafunc');
	}
}
