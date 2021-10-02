<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outcome extends Model
{
    use HasFactory, Uuids;
    protected $guarded = [];

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'target_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'target_id');
    }
}