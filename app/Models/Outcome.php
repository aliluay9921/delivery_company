<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outcome extends Model
{
    use HasFactory, Uuids;
    protected $guarded = [];
    protected $appends = ['CompanyBalance'];

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'target_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'target_id');
    }
    public function getCompanyBalanceAttribute()
    {
        $company_balance = 0;
        $goods = GoodReceived::where('order_status', 2)->where('paid_company', false)->get();
        foreach ($goods as $good) {
            $company_balance += $good->delevery_price->company_cost;
        }
        $outcoms = Outcome::where('type', 0)->where('target_id', null)->where('paid_company', false);
        foreach ($outcoms->get() as $outcom) {
            $company_balance -= $outcom->value;
        }
        if ($company_balance == 0) {
            $goods->update(['paid_company' => true]);
            $outcoms->update(['paid_company' => true]);
        }
        return $company_balance;
    }
}
