<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Economiccat extends Model
{
    protected $fillable = [
        'title','type','economicsheet_id'
    ];

	public function economicsheet() {
		return $this->belongsTo('CtoVmm\Economicsheet');
	}

    public function economicsubcats() {
		return $this->hasMany('CtoVmm\Economicsubcat');
	}
}
