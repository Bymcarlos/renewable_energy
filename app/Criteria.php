<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Criteria extends Model
{
    protected $fillable = [
        'title'
    ];

	public function criteriafuncs(){
		return $this->hasMany('CtoVmm\Criteriafunc');
	}
}
