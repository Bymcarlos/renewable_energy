<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Assessmenttype extends Model
{
    protected $fillable = [
        'title','key'
    ];

    public function assessments()
	{
		return $this->hasMany('CtoVmm\Assessment');
	}
}
