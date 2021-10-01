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
        $checks = GoodsDriver::where('driver_id', $this->id)->get();
        foreach ($checks as $check) {
            $price += $check->good->delevery_price->driver_cost;
        }
        return  $price;
    }
}