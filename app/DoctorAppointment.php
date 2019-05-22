<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DoctorAppointment extends Model
{
    protected $table='doctor_appointments';

    public function doctorDetails(){
        return $this->belongsTo(User::class,'doctor_id');
    }

    public function patientDetails(){
        return $this->belongsTo(User::class,'patient_id');
    }

    public function timeDetails(){
        return $this->belongsTo(TimeTable::class,'appointment_time_id');


    }
}



