<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Ratingfile extends Model
{
    protected $fillable = [
        'title','description','file','status','rating_id'
    ];

    public function rating()
	{
		return $this->belongsTo('CtoVmm\Rating');
	}
}
