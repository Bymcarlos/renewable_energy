<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Ratingeconomicrequest extends Model
{
    protected $fillable = [
        'ratingbench_id','economicrequest_id','value','ratingrequeststate_id'
    ];

    public function ratingbench()
	{
		return $this->belongsTo('CtoVmm\Ratingbench');
	}

	public function economicrequest()
	{
		return $this->belongsTo('CtoVmm\Economicrequest');
	}

	public function ratingrequeststate()
	{
		return $this->belongsTo('CtoVmm\Ratingrequeststate');
	}
}
