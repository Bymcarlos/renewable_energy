<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Component extends Model
{
    protected $fillable = [
        'title','sheet_id'
    ];

    public function areas()
	{
		return $this->belongsToMany('CtoVmm\Area')->withTimestamps();
	}

	public function sheet()
	{
		return $this->belongsTo('CtoVmm\Sheet');
	}
}
