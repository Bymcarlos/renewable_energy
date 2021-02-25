<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Techsheet extends Model
{
    protected $fillable = [
        'title','description','area_id','inputsheet_id'
    ];

    public function area(){
		return $this->belongsTo('CtoVmm\Area');
	}

	public function inputsheet() {
		return $this->belongsTo('CtoVmm\Inputsheet');
	}

	public function techcats() {
		return $this->hasMany('CtoVmm\Techcat');
	}

	public function ratings() {
		return $this->hasMany('CtoVmm\Rating');
	}

	public function criticalities() {
		return $this->belongsToMany('CtoVmm\Criticality')
			->withPivot('score_weight')
			->withTimestamps();
	}
}
