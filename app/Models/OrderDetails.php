<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'order_details';

    protected $fillable = ['order_id','menu_id','quantity','menu_total_amount'];
    
    protected $dates = ['deleted_at'];
}
