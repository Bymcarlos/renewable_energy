<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Techcat extends Model
{
    protected $fillable = [
        'title','order','techsheet_id','applicable_id'
    ];

    public function techsheet(){
		return $this->belongsTo('CtoVmm\Techsheet');
	}

	public function techrequests() {
		return $this->hasMany('CtoVmm\Techrequest');
	}

	public function applicable(){
		return $this->belongsTo('CtoVmm\Applicable');
	}

	public function ratingtechcats() {
		return $this->hasMany('CtoVmm\Ratingtechcat');
	}
}
