<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class ExcelColumn extends Model
{
	public $timestamps = false;
    protected $fillable = [
        'title',
    ];
}
