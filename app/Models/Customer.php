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
    // protected $appends = ['Balance'];

    public function goods_recevied()
    {
        return $this->hasMany(GoodReceived::class, 'customer_id');
    }

    public function getBalanceAttribute()
    {
        $price = 0;
        $goods = $this->goods_recevied()->where('order_status', 1)->get();
        foreach ($goods as $good) {
            $price += $good->price;
        }
        $min = $this->goods_recevied()->where('order_status', 1)->where('type_deliver', 0)->get();
        foreach ($min as $num) {
            $price -= $num->delevery_price()->company_cost + $min->delevery_price()->driver_cost;
        }
        // delevery_price
        return  $price;
    }
}