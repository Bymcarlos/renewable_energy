<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Timesheet extends Model
{
    protected $fillable = [
        'title','description'
    ];

    public function timecats() {
		return $this->hasMany('CtoVmm\Timecat');
	}

	public function ratings() {
		return $this->hasMany('CtoVmm\Rating');
	}
}
