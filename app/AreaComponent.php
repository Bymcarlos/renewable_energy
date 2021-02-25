<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class AreaComponent extends Model
{
    protected $table="area_component";

    protected $fillable = [
		'area_id','component_id'
	];

	public function area(){
		return $this->belongsTo('CtoVmm\Area');
	}
	public function component(){
		return $this->belongsTo('CtoVmm\Component');
	}
	//An item area-component can be tested in many benches
	public function benches()
	{
		return $this->hasMany('CtoVmm\Bench');
	}
}
