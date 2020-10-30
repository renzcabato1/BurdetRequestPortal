<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    //
    protected $connection = 'hr_portal';
    public function total()
    {
        return $this->hasMany(SbRequest::class,'department_id','id');
    }
    public function total_approved()
    {
        return $this->hasMany(SbRequest::class,'department_id','id')->where('last_status','=','Approved');
    }
    public function total_declined()
    {
        return $this->hasMany(SbRequest::class,'department_id','id')->where('last_status','=','Cancelled');
    }
}
