<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    protected $fillable = [
        'title',
    ];

    public function benches(){
    	return $this->hasMany('CtoVmm\Bench');
    }
}
