<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Weekstate extends Model
{
    protected $fillable = [
        'title'
    ];

    public function occupationweeks(){
    	return $this->hasMany('CtoVmm\Occupationweek');
    }
}
