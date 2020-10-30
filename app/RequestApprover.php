<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestApprover extends Model
{
    //
    public function user_info()
    {
        return $this->hasOne(User::class,'id','approver_id');
    
    }
    public function employe_info()
    {
        return $this->hasOne(Employee::class,'user_id','approver_id');
    
    }
    public function sb_request()
    {
        return $this->belongsTo(SbRequest::class);
    }
    public function review_again_info()
    {
        return $this->hasMany(RequestApproverRemark::class);
    }
    public function addtional_approver()
    {
        return $this->hasMany(RequestApprover::class,'sb_request_id','sb_request_id')->where('role_number',0)->where('status','Pending');
    }
}
