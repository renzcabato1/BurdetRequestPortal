<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Endorsement extends Model
{
    //
    protected $connection = 'hr_portal';
    public function department_info()
    {
        return $this->hasOne(Department::class,'id','department_id');
    }
    public function user_info()
    {
   
        return $this->hasOne(User::class,'id','user_id');
    
    }
    public function employee_info()
    {
        return $this->hasOne(Employee::class,'user_id','user_id');
    }
    
}
