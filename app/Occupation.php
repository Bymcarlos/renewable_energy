<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Occupation extends Model
{
    protected $fillable = [
        'year','product_id','bench_id'
    ];

    public function product()
	{
		return $this->belongsTo('CtoVmm\Product');
	}
	public function bench()
	{
		return $this->belongsTo('CtoVmm\Bench');
	}

	public function occupationweeks()
	{
		return $this->hasMany('CtoVmm\Occupationweek');
	}
}
