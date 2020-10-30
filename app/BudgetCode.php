<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BudgetCode extends Model
{
    //
    public function company_info()
    {
        return $this->hasOne(Company::class,'id','company_id');
    }
}
