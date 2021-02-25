<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Applicable extends Model
{
    protected $fillable = [
        'title','template','rating'
    ];

	public function techcats() {
		return $this->hasMany('CtoVmm\Techcat');
	}

	public function ratingtechcats() {
		return $this->hasMany('CtoVmm\Ratingtechcat');
	}
}
