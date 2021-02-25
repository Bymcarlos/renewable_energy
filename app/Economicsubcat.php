<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Economicsubcat extends Model
{
    protected $fillable = [
        'title','administrable','weighted','economiccat_id'
    ];

	public function economiccat() {
		return $this->belongsTo('CtoVmm\Economiccat');
	}

    public function economicrequests() {
		return $this->hasMany('CtoVmm\Economicrequest');
	}
}
