<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Ratingtechcat extends Model
{
    protected $fillable = [
        'rating_id','techcat_id','applicable_id'
    ];

    public function rating()
	{
		return $this->belongsTo('CtoVmm\Rating');
	}

	public function techcat()
	{
		return $this->belongsTo('CtoVmm\Techcat');
	}

	public function applicable()
	{
		return $this->belongsTo('CtoVmm\Applicable');
	}
}
