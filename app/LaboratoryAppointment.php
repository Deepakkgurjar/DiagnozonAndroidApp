<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LaboratoryAppointment extends Model
{
    protected $table='laboratory_appointments';

    public function labDetails(){
        return $this->belongsTo(User::class,'lab_id');
    }

    public function patientDetails(){
        return $this->belongsTo(User::class,'patient_id');
    }

    public function timeDetails(){
        return $this->belongsTo(TimeTable::class,'appointment_time_id');


    }
}
