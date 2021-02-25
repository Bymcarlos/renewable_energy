<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $fillable = [
        'title','order','assessmenttype_id'
    ];

    public function sheets()
	{
		return $this->hasMany('CtoVmm\Sheet');
	}

	public function assessmenttype()
	{
		return $this->belongsTo('CtoVmm\Assessmenttype');
	}
}
