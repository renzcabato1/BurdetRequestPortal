<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SbRequest extends Model
{
    //
    protected $connection = 'mysql';
    public function user_info()
    {
   
        return $this->hasOne(User::class,'id','request_by');
    
    }
    public function SbRequestData()
    {
   
        return $this->hasMany(SbRequest::class,'request_by','request_by');
    
    }
    public function company_info()
    {
        return $this->hasOne(Company::class,'id','company_id');
    }

    public function department_info()
    {
        return $this->hasOne(Department::class,'id','department_id');
    }

    public function approvers_info()
    {
        return $this->hasMany(RequestApprover::class,'sb_request_id','id');
    }
    public function original_approver()
    {
        return $this->hasMany(RequestApprover::class,'sb_request_id','id')->where('role_number','!=',0);
    }
    public function details()
    {
        return $this->hasMany(Detail::class,'sb_request_id','id');
    }
    public function attachments()
    {
        return $this->hasMany(Attachment::class,'sb_request_id','id');
    }
    public function cancel_info()
    {
        return $this->hasOne(User::class,'id','cancelled_by');
    }
  
}
