<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyFinanceHead extends Model
{
    //
    protected $connection = 'hr_portal';

    public function user_info()
    {
        return $this->hasOne(User::class,'id','user_id');
    }
    public function company_info()
    {
        return $this->hasOne(Company::class,'id','company_id');
    }
}
