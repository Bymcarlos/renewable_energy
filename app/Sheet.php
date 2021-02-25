<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Sheet extends Model
{
    protected $fillable = [
        'title','abbrev','assessment_id','required'
    ];

    public function components()
	{
		return $this->hasMany('CtoVmm\Component');
	}

	public function assessment()
	{
		return $this->belongsTo('CtoVmm\Assessment');
	}

	public function cats()
	{
		return $this->hasMany('CtoVmm\Cat');
	}
	public function benches(){
		return $this->belongsToMany('CtoVmm\Bench')
			->withPivot('id')
			->withPivot('status')
			->withTimestamps();
	}
}
