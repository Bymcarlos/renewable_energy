<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Scope extends Model
{
    protected $fillable = [
        'title','description'
    ];

    public function partners()
	{
		return $this->hasMany('CtoVmm\Partner');
	}
}
