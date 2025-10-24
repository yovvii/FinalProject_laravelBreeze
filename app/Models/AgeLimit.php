<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AgeLimit extends Model
{
    use HasFactory;

    protected $fillable = [
        'min_age_years',
        'max_age_years',
        'reference_date',
    ];
    
    protected $casts = [
        'reference_date' => 'date',
    ];
}
