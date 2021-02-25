<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'title',
    ];

    public function platforms(){
    	return $this->hasMany('CtoVmm\Platform');
    }
}
