<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, Uuids, SoftDeletes;
    protected $guarded = [];
    protected $dates = ['deleted_at'];
    protected $appends = ['Balance'];

    public function goods_recevied()
    {
        return $this->hasMany(GoodReceived::class, 'customer_id');
    }

    public function getBalanceAttribute()
    {
        $price = 0;
        // $goods = $this->goods_recevied()->where('order_status', 1)->get();
        $goods = GoodReceived::where('customer_id', $this->id)->where('order_status', 2)->where('paid_customer', false);
        $outcoms = Outcome::where('type', 1)->where('target_id', $this->id)->where('paid_customer', false);
        foreach ($outcoms->get() as $outcom) {
            $price -= $outcom->value;
        }
        foreach ($goods->get() as $good) {
            $price += $good->price;
            if ($good->type_deliver == 0) {
                $price -= $good->delevery_price->company_cost + $good->delevery_price->driver_cost;
            }
        }
        if ($price == 0) {
            $goods->update(['paid_customer' => true]);
            $outcoms->update(['paid_customer' => true]);
        }
        return  $price;
    }
}