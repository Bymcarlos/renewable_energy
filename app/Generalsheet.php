<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Generalsheet extends Model
{
    protected $fillable = [
        'title'
    ];

    public function sections()
	{
		return $this->hasMany('CtoVmm\Section');
	}

	public function partners() {
		return $this->belongsToMany('CtoVmm\Partner')->withTimestamps();
	}
}
