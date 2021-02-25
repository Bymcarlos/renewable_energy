<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Economicrequest extends Model
{
    protected $fillable = [
        'title','help','ordering','economicsubcat_id','weight'
    ];

	public function economicsubcat() {
		return $this->belongsTo('CtoVmm\Economicsubcat');
	}

	public function ratingeconomicrequests() {
		return $this->hasMany('CtoVmm\Ratingeconomicrequest');
	}

	public function unit() {
		return $this->belongsTo('CtoVmm\Unit');
	}
}
