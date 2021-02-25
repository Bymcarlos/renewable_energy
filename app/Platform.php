<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    protected $fillable = [
        'title','customer_id'
    ];

	public function customer(){
    	return $this->belongsTo('CtoVmm\Customer');
    }

    public function products(){
    	return $this->hasMany('CtoVmm\Product');
    }
}
