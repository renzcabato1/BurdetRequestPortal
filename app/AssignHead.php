<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssignHead extends Model
{
    //
    protected $connection = 'hr_portal';
    public function employee_info()
    {
        return $this->belongsTo(Employee::class,'employee_id','id');
    }
    public function employee_head_info()
    {
        return $this->belongsTo(Employee::class,'employee_head_id','id');
    }
}
