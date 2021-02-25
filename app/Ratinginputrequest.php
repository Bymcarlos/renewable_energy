<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Ratinginputrequest extends Model
{
    protected $fillable = [
        'rating_id','inputrequest_id','value'
    ];

    public function rating()
	{
		return $this->belongsTo('CtoVmm\Rating');
	}

	public function inputrequest_id()
	{
		return $this->belongsTo('CtoVmm\Inputrequest');
	}
}
