<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Timerequest extends Model
{
    protected $fillable = [
        'title','label','ordering','settable','state','timesubcat_id'
    ];

	public function timesubcat() {
		return $this->belongsTo('CtoVmm\Timesubcat');
	}

	public function timerequestsetts() {
		return $this->hasMany('CtoVmm\Timerequestsett');
	}

	public function ratingtimerequests() {
		return $this->hasMany('CtoVmm\Ratingtimerequest');
	}

}
