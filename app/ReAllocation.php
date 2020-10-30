<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReAllocation extends Model
{
    //
    public function user_info()
    {
   
        return $this->hasOne(User::class,'id','request_by');
    
    }
    public function company_info()
    {
        return $this->hasOne(Company::class,'id','company_id');
    }
    public function department_info()
    {
        return $this->hasOne(Department::class,'id','department_id');
    }
    public function details()
    {
        return $this->hasMany(ReallocationDetail::class,'re_allocations_id','id');
    }
    public function approvers_info()
    {
        return $this->hasMany(ReallocationApprover::class,'reallocation_id','id');
    }
    public function original_approver()
    {
        return $this->hasMany(ReallocationApprover::class,'reallocation_id','id')->where('role_number','!=',0);
    }
    public function attachments()
    {
        return $this->hasMany(ReallocationAttachment::class,'re_allocation_id','id');
    }
    public function cancel_info()
    {
        return $this->hasOne(User::class,'id','cancelled_by');
    }
}
