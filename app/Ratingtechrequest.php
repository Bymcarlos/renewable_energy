<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Ratingtechrequest extends Model
{
    protected $fillable = [
        'rating_id','techrequest_id','criticality_id'
    ];

    public function rating()
	{
		return $this->belongsTo('CtoVmm\Rating');
	}

	public function techrequest()
	{
		return $this->belongsTo('CtoVmm\Techrequest');
	}

	public function criticality()
	{
		return $this->belongsTo('CtoVmm\Criticality');
	}
}
