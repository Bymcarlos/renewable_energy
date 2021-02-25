<?php

namespace CtoVmm;

use Illuminate\Database\Eloquent\Model;

class Timerequestsett extends Model
{
    protected $fillable = [
        'percent','value','label','timerequest_id'
    ];

	public function timerequest() {
		return $this->belongsTo('CtoVmm\Timerequest');
	}
}
