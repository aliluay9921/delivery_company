<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use HasFactory, Uuids, SoftDeletes;
    protected $guarded = [];
    protected $dates = ['deleted_at'];
    protected $appends = ['Balance'];


    public function checks()
    {
        return $this->hasMany(GoodsDriver::class, 'driver_id');
    }
    public function getBalanceAttribute()
    {
        $price = 0;

        $checks
            = GoodReceived::whereHas('goods_driver',  function ($q) {
                $q->where('driver_id', $this->id);
            })->where('order_status', 2)->where('paid_driver', "=", false);

        // GoodsDriver::where('driver_id', $this->id)->where('paid_driver', "=", false);

        foreach ($checks->get() as $check) {
            $price += $check->delevery_price->driver_cost;
        }
        $outcoms = Outcome::where('type', 0)->where('target_id', $this->id)->where('paid_driver', "=", false);
        foreach ($outcoms->get() as $outcom) {
            $price -= $outcom->value;
        }
        if ($price == 0) {
            $checks->update(['paid_driver' => true]);
            $outcoms->update(['paid_driver' => true]);
        }
        return  $price;
    }
}