<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class rating extends Model
{
    use HasFactory;
    protected $fillable = [
    'offer_id',
    'comments',
    'star_rating',
    'user_id'
    ];
}
