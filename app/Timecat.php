<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Timecat extends Model
{
    protected $fillable = [
        'title','type','score_weight','timesheet_id'
    ];

	public function timesheet() {
		return $this->belongsTo('CtoVmm\Timesheet');
	}

    public function timesubcats() {
		return $this->hasMany('CtoVmm\Timesubcat');
	}
}
