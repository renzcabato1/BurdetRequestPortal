<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyFinanceCoor extends Model
{
    //
    protected $connection = 'hr_portal';
    public function user_info()
    {
        return $this->hasOne(User::class,'id','user_id');
    }
}
