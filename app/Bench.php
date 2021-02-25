<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Bench extends Model
{
    protected $fillable = [
		'title','comments','entity_id','area_component_id','status','country_id','benchtype_id'
	];

	public function entity()
	{
		return $this->belongsTo('CtoVmm\Entity');
	}

	//A bench only check an area-component item:
	public function areaComponent()
	{
		return $this->belongsTo('CtoVmm\AreaComponent');
	}

	public function sheets(){
		return $this->belongsToMany('CtoVmm\Sheet')
			->withPivot('id')
			->withPivot('status')
			->withTimestamps();
	}

	public function features(){
		return $this->belongsToMany('CtoVmm\Feature')
			->withPivot('id','value', 'status', 'comments')
			->withTimestamps();
	}

	public function featuresfiles(){
		return $this->belongsToMany('CtoVmm\Feature','bench_feature_files')
			->withPivot('id','title', 'file')
			->withTimestamps();
	}

	public function featuresbrands(){
		return $this->belongsToMany('CtoVmm\Feature','bench_feature_brands')
			->withPivot('id','brand_name', 'brand_value')
			->withTimestamps();
	}

	public function occupations(){
		return $this->hasMany('CtoVmm\Occupation');
	}

	public function country(){
		return $this->belongsTo('CtoVmm\Country');
	}
	public function benchtype(){
		return $this->belongsTo('CtoVmm\Benchtype');
	}

	public function ratingbenches(){
		return $this->hasMany('CtoVmm\Ratingbench');
	}
}
