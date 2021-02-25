<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Unittype extends Model
{
    protected $fillable = [
        'title',
    ];

    public function units(){
    	return $this->hasMany('CtoVmm\Unit');
    }
}
