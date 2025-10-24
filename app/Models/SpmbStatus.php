<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpmbStatus extends Model
{
    // Menonaktifkan timestamps karena kita tidak punya kolom created_at/updated_at
    public $timestamps = false; 

    // Menonaktifkan incrementing ID karena kita tidak punya kolom ID
    public $incrementing = false; 
    
    // Tentukan nama tabel
    protected $table = 'spmb_statuses';
    
    protected $fillable = [
        'status', 
        'closing_at', // 🔥 Tambahkan ini
        'starting_at',
    ];

    protected $casts = [
        'closing_at' => 'datetime', // 🔥 Atur sebagai datetime
        'starting_at' => 'datetime',
    ];
}
