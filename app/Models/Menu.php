<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    protected $table = 'menus';
    protected $fillable = ['menu_name','menu_description','menu_category','menu_cuisine','menu_portion','menu_price','menu_image','menu_status','sub_category'];
}
