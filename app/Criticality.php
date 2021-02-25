<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Criticality extends Model
{
    protected $fillable = [
        'title','type','tbcps','tbcst'
    ];

    public function techrequests(){
		return $this->hasMany('CtoVmm\Techrequest');
	}

	public function techsheets() {
		return $this->belongsToMany('CtoVmm\Techsheet')
			->withPivot('score_weight')
			->withTimestamps();
	}
}
