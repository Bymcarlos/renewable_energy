<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Benchtype extends Model
{
    protected $fillable = [
		'title','description'
	];

	public function benches(){
		return $this->hasMany('CtoVmm\Bench');
	}
}
