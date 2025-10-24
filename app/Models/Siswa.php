<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Siswa extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nisn',
        'jenis_kelamin',
        'tanggal_lahir',
        'kabupaten',
        'kecamatan',
        'desa',
        'alamat', // error di sini
        'no_kk',
        'nik',
        'no_hp',
        'nama_ayah',
        'nama_ibu',
        'agama',
        'kebutuhan_k',
        'sekolah_asal_id', // ada foreign key tabel sekolah_asals
        'akta_file',
        'foto',

        'surat_pernyataan',
        'surat_keterangan_lulus',
        'ijazah_file',
        'sertifikat_file',
        'verifikasi_sertifikat',
        'document_afirmasi',
        'verifikasi_afirmasi',

        'data_sma_id',
        'jalur_pendaftaran_id',
        'nilai_akhir',
        'status_pendaftaran',

        'longitude_siswa',
        'latitude_siswa',

        'has_completed_steps',
        'result_viewed',

        'rapor_files_verified',
        'akta_file_verified',
        'surat_pernyataan_verified',
        'surat_keterangan_lulus_verified',
        'ijazah_file_verified'
      ];
      
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ortu(): HasOne
    {
        return $this->hasOne(Ortu::class);
    }

    public function sekolahAsal(): BelongsTo
    {
        return $this->belongsTo(SekolahAsal::class, 'sekolah_asal_id');
    }

    public function jalurPendaftaran(): BelongsTo
    {
        return $this->belongsTo(JalurPendaftaran::class, 'jalur_pendaftaran_id');
    }

    public function dataSma(): BelongsTo
    {
        return $this->belongsTo(DataSma::class, 'data_sma_id');
    }

    public function semesters(): HasMany
    {
        return $this->hasMany(Semester::class, 'user_id', 'user_id');
    }
    
    public function raporFiles(): HasMany
    {
        return $this->hasMany(RaporFile::class, 'user_id', 'user_id');
    }

    public function activities()
    {
        return $this->hasMany(StudentActivity::class);
    }

    public function timelineProgress()
    {
        return $this->hasOne(TimelineProgress::class, 'user_id', 'user_id');
    }
    
    public function isDataLengkap()
    {
        // dd("KODE isDataLengkap() DIEKSEKUSI!"); 
        $requiredFields = [
            // siswa tabel
            'user_id',
            'nisn',
            'jenis_kelamin',
            'tanggal_lahir',
            'kabupaten',
            'kecamatan',
            'desa',
            'alamat',
            'no_kk',
            'nik',
            'no_hp',
            'nama_ayah',
            'nama_ibu',
            'agama',
            'sekolah_asal_id',
            'akta_file',
            'foto',
            'surat_pernyataan',
            'surat_keterangan_lulus',
            'ijazah_file',
            'longitude_siswa',
            'latitude_siswa',

            // semester table
            'nilai_semester',
            'file_rapor',
        ];
        
        // dd("KODE isDataLengkap() DIEKSEKUSI!"); 

        // foreach ($requiredFields as $field) {
        //     if (is_null($this->{$field}) || $this->{$field} === '') {
        //         dd("Data Belum Lengkap. Kolom yang kosong: " . $field . " (Diperiksa di Model Siswa)");
        //     }
        // }

        $REQUIRED_SEMESTERS = 5;
        $rapor_file_count = $this->raporFiles()->count();
        if ($rapor_file_count < $REQUIRED_SEMESTERS) {
            // Hanya ada {rapor_file_count} file rapor yang diunggah, seharusnya 5.
            return false;
        }
        $nilai_semester_count = $this->semesters()->distinct('semester')->count();
        if ($nilai_semester_count < $REQUIRED_SEMESTERS) {
            // Hanya {nilai_semester_count} semester yang memiliki data, seharusnya 5.
            return false;
        }

        if (!$this->ortu) {
            return false;
        }

        return true;
    }
}