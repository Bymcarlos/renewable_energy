<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $fillable = [
        'title',
    ];

    public function components()
	{
		return $this->belongsToMany('CtoVmm\Component')->withTimestamps();
	}

	public function inputsheets() {
		return $this->hasMany('CtoVmm\Inputsheet');
	}

	public function techsheets() {
		return $this->hasMany('CtoVmm\Techsheet');
	}

	public function ratings() {
		return $this->hasMany('CtoVmm\Rating');
	}
}
