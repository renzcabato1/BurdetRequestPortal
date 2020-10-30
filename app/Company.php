<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    //
    protected $connection = 'hr_portal';

    public function address_info()
    {
        return $this->hasOne(Address::class,'id','address_id');
    }
    public function finance_heads()
    {
        return $this->hasOne(CompanyFinanceHead::class,'company_id','id');
    }
    public function company_info()
    {
        return $this->hasOne(CompanyCode::class,'company_id','id');
    }
    public function controlling_area()
    {
        return $this->hasOne(ControllingArea::class,'company_id','id');
    }
    public function order_types()
    {
        return $this->hasMany(OrderType::class,'company_id','id');
    }
    public function assign_letters()
    {
        return $this->hasMany(AssignLetter::class,'company_id','id');
    }
    public function finance_coors()
    {
        return $this->hasMany(CompanyFinanceCoor::class,'company_id','id');
    }
    public function plant_info()
    {
        return $this->hasMany(Plant::class,'company_id','id');
    }
    public function approver_plant()
    {
        return $this->hasMany(Plant::class,'company_id','id');
    }
    public function total()
    {
        return $this->hasMany(SbRequest::class,'company_id','id');
    }
    public function total_approved()
    {
        return $this->hasMany(SbRequest::class,'company_id','id')->where('last_status','=','Approved');
    }
    public function total_declined()
    {
        return $this->hasMany(SbRequest::class,'company_id','id')->where('last_status','=','Cancelled');
    }
   public function general_info()
    {
   
        return $this->hasOne(GeneralManager::class,'company_id','id');
    
    }
    public function cluster_head()
    {
        return $this->hasOne(ClusterHead::class,'company_id','id');
    }
}
