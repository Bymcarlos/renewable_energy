<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Economicsheet extends Model
{
    protected $fillable = [
        'title','description'
    ];

    public function economiccats() {
		return $this->hasMany('CtoVmm\Economiccat');
	}

	public function ratings() {
		return $this->hasMany('CtoVmm\Rating');
	}
}
