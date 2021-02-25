<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'title','dial_code','code'
    ];

    public function benches(){
		return $this->hasMany('CtoVmm\Bench');
	}

	public function partners(){
		return $this->hasMany('CtoVmm\Partner');
	}
}
