<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table='services';

    public function patinetName(){
        return $this->belongsTo(User::class,'patient_id');
    }
}
