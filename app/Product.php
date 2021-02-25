<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title','platform_id'
    ];

    public function platform(){
    	return $this->belongsTo('CtoVmm\Platform');
    }
}
