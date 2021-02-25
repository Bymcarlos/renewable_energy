<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Inputrequest extends Model
{
    protected $fillable = [
        'title','help','order','inputcat_id'
    ];

    public function inputcat()
	{
		return $this->belongsTo('CtoVmm\Inputcat');
	}

	public function techrequests() {
		return $this->hasMany('CtoVmm\Techrequest');
	}

	public function ratinginputrequests() {
		return $this->hasMany('CtoVmm\Ratinginputrequest');
	}
    
}
