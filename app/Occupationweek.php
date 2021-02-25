<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Occupationweek extends Model
{
    protected $fillable = [
        'week','occupation_id','weekstate_id'
    ];

    public function occupation()
	{
		return $this->belongsTo('CtoVmm\Occupation');
	}
	public function weekstate()
	{
		return $this->belongsTo('CtoVmm\Weekstate');
	}
}
