<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LastApprover extends Model
{
    //
    public function user_info()
    {
        return $this->hasOne(User::class,'id','user_id');
    }
}
