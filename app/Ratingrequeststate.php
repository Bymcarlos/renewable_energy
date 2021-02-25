<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Ratingrequeststate extends Model
{
    protected $fillable = [
        'title'
    ];

    public function ratingtimerequests()
	{
		return $this->hasMany('CtoVmm\Ratingtimerequest');
	}

	public function ratingeconomicrequests()
	{
		return $this->hasMany('CtoVmm\Ratingeconomicrequest');
	}
}
