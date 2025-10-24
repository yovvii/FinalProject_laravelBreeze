<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RaporFile extends Model
{
    protected $fillable = [
        'user_id',
        'semester',
        'file_rapor'
    ];

    public function semester()
    {
        // Asumsi relasi ke model Semester
        return $this->belongsTo(Semester::class); 
    }
}
