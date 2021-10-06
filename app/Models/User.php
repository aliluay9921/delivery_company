<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes, Uuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];
    protected $dates = ['deleted_at'];
    protected $appends = ['CompanyBalance'];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'pivot'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions', 'user_id', 'permission_id');
    }
    public function getCompanyBalanceAttribute()
    {

        $company_balance = 0;
        $goods = GoodReceived::where('order_status', 2)->where('paid_company', false);
        foreach ($goods->get() as $good) {
            $company_balance += $good->delevery_price->company_cost;
        }
        $outcoms = Outcome::whereIn('type', [2, 3])->where('paid_company', false);
        $incomes = Income::where('type', 0)->where('paid_company', false);
        foreach ($outcoms->get() as $outcom) {
            $company_balance -= $outcom->value;
        }
        foreach ($incomes->get() as $income) {
            $company_balance += $income->value;
        }

        if ($company_balance == 0) {
            $goods->update(['paid_company' => true]);
            $outcoms->update(['paid_company' => true]);
            $incomes->update(['paid_company' => true]);
        }
        return $company_balance;
    }
}