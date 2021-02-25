<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Inputcat extends Model
{
    protected $fillable = [
        'title','order','inputsheet_id'
    ];

	public function inputsheet()
	{
		return $this->belongsTo('CtoVmm\Inputsheet');
	}
    public function inputrequests()
	{
		return $this->hasMany('CtoVmm\Inputrequest');
	}
}
