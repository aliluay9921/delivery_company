<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodReceived extends Model
{
    use HasFactory, SoftDeletes, Uuids;
    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    public function delevery_price()
    {
        return $this->belongsTo(DeliveryPrice::class, 'delevery_price_id');
    }
}