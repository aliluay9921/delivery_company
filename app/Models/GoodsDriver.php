<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsDriver extends Model
{
    use HasFactory, Uuids;
    protected $guarded = [];
    protected $with = ['good'];


    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function good()
    {
        return $this->belongsTo(GoodReceived::class, 'goods_received_id');
    }
}
