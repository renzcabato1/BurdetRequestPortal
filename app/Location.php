<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    //
    protected $connection = 'hr_portal';
    
    public function address_info()
    {
        return $this->hasOne(Address::class,'id','address_id');
    }
}

