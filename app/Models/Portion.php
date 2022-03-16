<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portion extends Model
{
    use HasFactory;
    protected $table = 'portions';
    protected $fillable = ['portion_name'];
}
