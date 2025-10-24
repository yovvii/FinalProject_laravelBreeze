<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GlobalSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'important_info_content',
        'juknis_pdf_path',
        'alur_pendaftaran_path',
    ];
}
