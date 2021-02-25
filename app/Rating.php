<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $fillable = [
        'title','description','area_id','techsheet_id','timesheet_id','economicsheet_id'
    ];

    public function area()
	{
		return $this->belongsTo('CtoVmm\Area');
	}

	public function techsheet()
	{
		return $this->belongsTo('CtoVmm\Techsheet');
	}

	public function timesheet()
	{
		return $this->belongsTo('CtoVmm\Timesheet');
	}

	public function economicsheet()
	{
		return $this->belongsTo('CtoVmm\Economicsheet');
	}

	public function ratinginputrequests() {
		return $this->hasMany('CtoVmm\Ratinginputrequest');
	}

	public function ratingtechcats() {
		return $this->hasMany('CtoVmm\Ratingtechcat');
	}

	public function ratingtechrequests() {
		return $this->hasMany('CtoVmm\Ratingtechrequest');
	}

	public function ratingbenches() {
		return $this->hasMany('CtoVmm\Ratingbench');
	}

	public function ratingfiles() {
		return $this->hasMany('CtoVmm\Ratingfile');
	}
}
