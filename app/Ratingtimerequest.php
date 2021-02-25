<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Ratingtimerequest extends Model
{
    protected $fillable = [
        'ratingbench_id','timerequest_id','value','ratingrequeststate_id'
    ];

    public function ratingbench()
	{
		return $this->belongsTo('CtoVmm\Ratingbench');
	}

	public function timerequest()
	{
		return $this->belongsTo('CtoVmm\Timerequest');
	}

	public function ratingrequeststate()
	{
		return $this->belongsTo('CtoVmm\Ratingrequeststate');
	}
}
