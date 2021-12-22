<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Meal extends Model
{
    protected $connection = 'mongodb';
    use HasFactory;
}
