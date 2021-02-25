<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Subcat extends Model
{
    protected $fillable = [
        'title','cat_id'
    ];

    public function cat()
	{
		return $this->belongsTo('CtoVmm\Cat');
	}
	public function questions()
	{
		return $this->hasMany('CtoVmm\Question');
	}
}
