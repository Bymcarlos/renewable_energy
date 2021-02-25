<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    protected $fillable = [
        'title','section_id'
    ];

    public function section()
	{
		return $this->belongsTo('CtoVmm\Section');
	}

	public function partners() {
		return $this->belongsToMany('CtoVmm\Partner')
			->withPivot('value')
			->withTimestamps();
	}
}
