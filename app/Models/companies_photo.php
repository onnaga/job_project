<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class companies_photo extends Model
{
    use HasFactory;
    protected $fillable = [
        'path',
        'company name'
    ];
}
