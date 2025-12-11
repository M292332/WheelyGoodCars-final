<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarTag extends Model
{
    use HasFactory;

    protected $table = 'car_tag';

    protected $fillable = ['car_id', 'tag_id'];
}
