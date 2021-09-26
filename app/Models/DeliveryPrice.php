<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryPrice extends Model
{
    use HasFactory, SoftDeletes, Uuids;
    protected $guarded = [];
    protected $dates = ['deleted_at'];
}