<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $fillable = ['order_no', 'table_no', 'ordered_on', 'ordered_by', 'food_ordered', 'total_amount', 'status'];
}
