<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Ratingbench extends Model
{
    protected $fillable = [
        'rating_id','bench_id'
    ];

    public function rating()
	{
		return $this->belongsTo('CtoVmm\Rating');
	}
	
	public function bench()
	{
		return $this->belongsTo('CtoVmm\Bench');
	}

	public function ratingtimerequests() {
		return $this->hasMany('CtoVmm\Ratingtimerequest');
	}

	public function ratingeconomicrequests() {
		return $this->hasMany('CtoVmm\Ratingeconomicrequest');
	}
}
