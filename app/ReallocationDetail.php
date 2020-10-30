<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReallocationDetail extends Model
{
    //
    public function sb_request()
    {
        return $this->belongsTo(ReAllocation::class,'re_allocations_id','id');
    }
    public function approvers()
    {
        return $this->hasOne(ReallocationApprover::class,'reallocation_id','re_allocations_id');
    }
}
