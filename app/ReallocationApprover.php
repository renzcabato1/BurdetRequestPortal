<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReallocationApprover extends Model
{
    //
    public function employee_info()
    {
        return $this->hasOne(Employee::class,'user_id','approver_id');
    }
    public function user_info()
    {
        return $this->hasOne(User::class,'id','approver_id');
    }
    public function sb_request()
    {
        return $this->belongsTo(ReAllocation::class,'reallocation_id','id');
    }
    public function addtional_approver()
    {
        return $this->hasMany(RequestApprover::class,'sb_request_id','sb_request_id')->where('role_number',0)->where('status','Pending');
    }
}
