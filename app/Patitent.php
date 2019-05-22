<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Patitent extends Model
{
    protected $table='patients';

    public function patientName(){
        return $this->belongsTo(User::class,'user_id');
    }
}
