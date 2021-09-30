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
    // public function getBalanceAttribute()
    // {
    //     $price = 0;
    //     foreach ($this->goodsRecevied as $good) {
    //         $price += $good->price;
    //     }
    //     return  $price;
    // }
}