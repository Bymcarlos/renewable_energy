<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $fillable = [
        'title','description','nda','scope_id','country_id'
    ];

    public function scope()
	{
		return $this->belongsTo('CtoVmm\Scope');
	}

	public function country()
	{
		return $this->belongsTo('CtoVmm\Country');
	}

	public function generalsheets() {
		return $this->belongsToMany('CtoVmm\Generalsheet')->withTimestamps();
	}

	public function generalrequests() {
		return $this->belongsToMany('CtoVmm\Generalrequest')
			->withPivot('value')
			->withTimestamps();
	}
}
