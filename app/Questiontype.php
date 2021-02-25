<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Questiontype extends Model
{
    protected $fillable = [
        'title'
    ];

    public function questions()
	{
		return $this->hasMany('CtoVmm\Question');
	}
}