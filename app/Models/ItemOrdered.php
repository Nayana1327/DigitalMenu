<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemOrdered extends Model
{
    use HasFactory;
    protected $table = 'item_ordereds';
    protected $fillable = ['table_no','order_no','menu_id','quantity'];
}
