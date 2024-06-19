<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class offers extends Model
{
    use HasFactory;

    protected $fillable = [
        'the_job',
        'company_id',
        'specialization_wanted',
        'salary',
        'the_days',
        'hour_begin',
        'period',
        'official_holidays',
        'offer_end_at',
        'area_id'
    ];

}
