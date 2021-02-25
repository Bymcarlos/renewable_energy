<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    protected $fillable = [
        'title','help','order','question_id','question_root','responsetype_id','unit_id','brand_name','brand_value','brand_value_unit','importable','parameter','rating_req','rating_crit','rating_func'
    ];
    //question_root: If it is the first feature of the question (on question groups always response type yes/no)
    //If responsetype is numeric, unit_id set the unit, else, unit_id is always 1 (none)
    //For responsetype 5 (brand name/value) ask for: brand_name, brand_value and brand_value_unit
    //importable: if thid field will be in the excel document for export/import benches
    //parameter: if this field will be in advanced search for reports
    //rating_req, rating_crit, rating_func: for rating tool excel report

    public function question()
	{
		return $this->belongsTo('CtoVmm\Question');
	}

	public function responsetype()
	{
		return $this->belongsTo('CtoVmm\Responsetype');
	}

	public function benches(){
		return $this->belongsToMany('CtoVmm\Bench')
			->withPivot('id','value', 'status', 'comments')
			->withTimestamps();
	}
	public function benchesfiles(){
		return $this->belongsToMany('CtoVmm\Bench','bench_sheet_feature_file')
			->withPivot('id','title', 'file')
			->withTimestamps();
	}

	public function benchesbrands(){
		return $this->belongsToMany('CtoVmm\Bench','bench_sheet_feature_brand')
			->withPivot('id','brand_name', 'brand_value')
			->withTimestamps();
	}

	public function unit(){
		return $this->belongsTo('CtoVmm\Unit');
	}

	public function brand_value_unit() {
		return $this->belongsTo('CtoVmm\Unit','brand_value_unit');
	}

	//RatingTool:
	public function techrequests() {
		return $this->hasMany('CtoVmm\Techrequest');
	}

}
