<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Inputsheet extends Model
{
    protected $fillable = [
        'title','description','area_id'
    ];

	public function area()
	{
		return $this->belongsTo('CtoVmm\Area');
	}
	public function techsheets()
	{
		return $this->hasMany('CtoVmm\Techsheet');
	}
    public function inputcats()
	{
		return $this->hasMany('CtoVmm\Inputcat');
	}
}
