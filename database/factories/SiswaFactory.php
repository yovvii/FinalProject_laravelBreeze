<?php

namespace Database\Factories;

use App\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SiswaFactory extends Factory
{
    protected $model = Siswa::class;

    public function definition(): array
    {
        $faker = $this->faker;
        
        // Menggunakan provider khusus untuk data Indonesia
        $faker->addProvider(new \Faker\Provider\id_ID\Person($faker));
        $faker->addProvider(new \Faker\Provider\id_ID\Address($faker));
        $faker->addProvider(new \Faker\Provider\id_ID\PhoneNumber($faker));

        $jenisKelamin = $faker->randomElement(['Laki-Laki', 'Perempuan']);
        $namaAyah = $faker->name('male');
        $namaIbu = $faker->name('female');
        $fixedSekolahAsalId = 8; 
        $randomDistance = mt_rand(100, 2000) / 100;

        return [
            // Nilai statis/FK akan di-override oleh Seeder
            'user_id' => null, 
            'data_sma_id' => null, 
            'jalur_pendaftaran_id' => null,

            // Data Siswa (dari biodata.blade.php & Siswa.php)
            'nisn' => $faker->unique()->numerify('##########'), // 10 digit NISN
            'jenis_kelamin' => $jenisKelamin,
            'tanggal_lahir' => $faker->date('Y-m-d', '2008-01-01'),
            
            'kabupaten' => $faker->city,
            'kecamatan' => $faker->city,
            'desa' => $faker->state,
            'alamat' => $faker->address,
            
            'no_kk' => $faker->unique()->numerify('################'), // 16 digit KK
            'nik' => $faker->unique()->numerify('################'), // 16 digit NIK
            'no_hp' => $faker->phoneNumber(),

            // Kolom 'nama_ayah' dan 'nama_ibu' ada di tabel Siswa (berdasarkan Siswa.php fillable)
            'nama_ayah' => $namaAyah, 
            'nama_ibu' => $namaIbu,
            
            'agama' => $faker->randomElement(['Islam', 'Katolik', 'Kristen Protestan', 'Hindu', 'Budha']),
            'kebutuhan_k' => null, // Dibiarkan null (opsional)
            'sekolah_asal_id' => $fixedSekolahAsalId, 

            // Dokumen File (Dummy paths)
            'akta_file' => 'dummy/akta/' . Str::random(10) . '.pdf',
            'foto' => 'dummy/foto/' . Str::random(10) . '.jpg',
            
            // Dokumen Lulus (dari surat_pernyataan.blade.php & surat_keterangan_lulus.blade.php)
            'surat_pernyataan' => 'dummy/sp/' . Str::random(10) . '.pdf',
            'surat_keterangan_lulus' => 'dummy/skl/' . Str::random(10) . '.pdf',
            'ijazah_file' => 'dummy/ijazah/' . Str::random(10) . '.pdf',
            'nilai_akhir' => $faker->randomFloat(2, 75, 95),

            // Koordinat (Dari biodata.blade.php)
            'longitude_siswa' => $faker->longitude(),
            'latitude_siswa' => $faker->latitude(),

            // Set kolom yang tidak wajib diisi di isDataLengkap() ke NULL jika Anda tidak ingin mengisinya
            'sertifikat_file' => null,
            'status_pendaftaran' => 'completed', // Status agar tidak redirect ke timeline awal

            'document_afirmasi' => 'dummy/document/afirmasi_seeder.pdf',
            'rapor_files_verified' => 'terverifikasi',
            'akta_file_verified' => 'terverifikasi',
            'surat_pernyataan_verified' => 'terverifikasi',
            'surat_keterangan_lulus_verified' => 'terverifikasi',
            'ijazah_file_verified' => 'terverifikasi',
            'verifikasi_afirmasi' => 'terverifikasi',
            'jarak_ke_sma_km' => $randomDistance,
        ];
    }
}