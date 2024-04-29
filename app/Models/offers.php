<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class offers extends Model
{
    use HasFactory;

    protected $fillable = [
        'the job',
        'company_id',
        'specialization_wanted',
        'salary',
        'the days',
        'hour begin',
        'period',
        'official holidays',
        'offer end at'
    ];

}
