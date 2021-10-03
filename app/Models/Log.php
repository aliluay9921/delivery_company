<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory, Uuids;
    protected $guarded = [];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'target_id');
    }
    public function driver()
    {
        return $this->belongsTo(Driver::class, 'target_id');
    }
    public function Goods_driver()
    {
        return $this->belongsTo(GoodsDriver::class, 'target_id');
    }
}