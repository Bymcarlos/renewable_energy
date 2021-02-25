<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Timesubcat extends Model
{
    protected $fillable = [
        'title','administrable','timecat_id'
    ];

	public function timecat() {
		return $this->belongsTo('CtoVmm\Timecat');
	}

    public function timerequests() {
		return $this->hasMany('CtoVmm\Timerequest');
	}
}
