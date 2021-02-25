<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Techrequest extends Model
{
    protected $fillable = [
        'title','help','ordering','techcat_id','inputrequest_id','feature_id','criticality_id','criteriafunc_id','inputfactor','value','range_x','range_y'
    ];

    public function techcat(){
		return $this->belongsTo('CtoVmm\Techcat');
	}

	public function inputrequest() {
		return $this->belongsTo('CtoVmm\Inputrequest');
	}

	public function feature() {
		return $this->belongsTo('CtoVmm\Feature');
	}
	
	public function criticality() {
		return $this->belongsTo('CtoVmm\Criticality');
	}

	public function criteriafunc() {
		return $this->belongsTo('CtoVmm\Criteriafunc');
	}

	public function ratingtechrequests() {
		return $this->hasMany('CtoVmm\Ratingtechrequest');
	}
}
