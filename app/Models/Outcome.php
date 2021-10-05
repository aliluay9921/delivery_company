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
        $outcoms = Outcome::whereIn('type', [2, 3])->where('paid_company', false);
        $incomes = Income::where('type', 0)->where('paid_company', false)->get();
        foreach ($outcoms->get() as $outcom) {
            $company_balance -= $outcom->value;
        }
        foreach ($incomes as $income) {
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