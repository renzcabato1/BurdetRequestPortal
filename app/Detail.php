<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{
    //
    public function sb_request()
    {
        return $this->belongsTo(SbRequest::class);
    }
    public function approvers()
    {
        return $this->hasOne(RequestApprover::class,'sb_request_id','sb_request_id');
    }
}
