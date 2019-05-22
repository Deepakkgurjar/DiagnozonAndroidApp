<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Laboratories extends Model
{
    public function labName(){
        return $this->belongsTo(User::class,'user_id');
    }
}
