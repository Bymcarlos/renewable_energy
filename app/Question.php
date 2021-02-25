<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'title','help','questiontype_id','subcat_id'
    ];

    public function subcat()
	{
		return $this->belongsTo('CtoVmm\Subcat');
	}

	public function questiontype()
	{
		return $this->belongsTo('CtoVmm\Questiontype');
	}

	public function features()
	{
		return $this->hasMany('CtoVmm\Feature');
	}
}
