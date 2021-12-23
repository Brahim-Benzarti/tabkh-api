<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Ingredient extends Model
{
    protected $connection = 'mongodb';
    use HasFactory;
}
