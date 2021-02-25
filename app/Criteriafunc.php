<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Criteriafunc extends Model
{
    protected $fillable = [
        'title','description','criteria_id','responsetype_id','askinput','askvalue','askrange'
    ];

    public function criteria(){
		return $this->belongsTo('CtoVmm\Criteria');
	}

	public function responsetype(){
		return $this->belongsTo('CtoVmm\Responsetype');
	}
	
	public function techrequests() {
		return $this->hasMany('CtoVmm\Techrequest');
	}
}
