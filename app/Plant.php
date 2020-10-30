<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plant extends Model
{
    //
    protected $connection = 'hr_portal';


    public function cluster_head_info()
    {
        return $this->hasOne(User::class,'id','cluster_head');
    }
}
