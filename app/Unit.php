<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'title','description','unittype_id',
    ];

    public function unittype(){
    	return $this->belongsTo('CtoVmmTestDatabase\Unittype');
    }

    public function features(){
		return $this->hasMany('CtoVmm\Feature');
	}

	public function featurebrandvalues(){
		return $this->hasMany('CtoVmm\Feature');
	}

    public function economicrequests(){
        return $this->hasMany('CtoVmm\Economicrequest');
    }
}
