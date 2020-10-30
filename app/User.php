<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    protected $connection = 'hr_portal';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function employee_info()
    {
        return Employee::where('user_id', auth()->user()->id)->get()->first();
    }
    
    public function role_info()
    {
        return CompanyFinanceHead::with('company_info.controlling_area')->where('user_id',auth()->user()->id)->get();
    }
    public function plant_info()
    {
        return Plant::where('approver_id',auth()->user()->id)->get();
    }
    public function account_info()
    {
        return Account::where('user_id',auth()->user()->id)->first();
    }
    public function company_info()
    {
        return CompanyFinanceHead::where('user_id',auth()->user()->id)->get();
    }
}
