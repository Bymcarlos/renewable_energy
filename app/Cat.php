<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Cat extends Model
{
    protected $fillable = [
        'title','sheet_id'
    ];

    public function sheet()
	{
		return $this->belongsTo('CtoVmm\Sheet');
	}
	public function subcats()
	{
		return $this->hasMany('CtoVmm\Subcat');
	}
}
