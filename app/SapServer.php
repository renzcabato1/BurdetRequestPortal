<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SapServer extends Model
{
    //
    
    protected $connection = 'hr_portal';

    public function sapUser()
    {
        return $this->hasOne(SapUser::class,'sap_server','sap_server');
    }
}
