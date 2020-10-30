<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyCode extends Model
{
    //
    protected $connection = 'hr_portal';

    public function sapServer()
    {
        return $this->hasOne(SapServer::class,'sap_server','sap_server');
    }
    
}
