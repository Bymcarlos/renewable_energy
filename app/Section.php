<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = [
        'title','generalsheet_id'
    ];

    public function generalsheet()
	{
		return $this->belongsTo('CtoVmm\Generalsheet');
	}

	public function generalrequests()
	{
		return $this->hasMany('CtoVmm\Generalrequest');
	}
}
