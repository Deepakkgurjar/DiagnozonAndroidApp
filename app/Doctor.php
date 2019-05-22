<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $table='doctors';

    public function doctorName(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function clicnic_type(){
    	return $this->belongsTo(ClinicTypes::class,'clinic_type_id');
    }

    public function sepcility(){
    	return $this->belongsTo(DocSpecilization::class,'specialty_id');
    }

    public function qualification()
    {
    	return $this->belongsTo(DocHigestQualification::class,'highest_quali_id');
    }
}
