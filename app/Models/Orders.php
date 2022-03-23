<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'orders';
    
    protected $fillable = ['table_id','waiter_id','waiter_approval','order_status', 'order_total_amount'];
    
    protected $dates = ['deleted_at'];
}
