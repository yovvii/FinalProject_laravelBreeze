<?php

namespace Database\Factories;

use App\Models\Ortu;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Semester;
use App\Models\RaporFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $this->faker->addProvider(new \Faker\Provider\id_ID\Person($this->faker));
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // Password tetap 'password'
            'remember_token' => Str::random(10),
            'role' => 'siswa',
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (User $user) {
            
            $fixedSmaId = 8;
            $fixedJalurId = 1;
            $REQUIRED_SEMESTERS = 5;
            
            // 1. BUAT DATA SISWA
            $siswa = Siswa::factory()->create([
                'user_id' => $user->id,
                'data_sma_id' => $fixedSmaId,
                'jalur_pendaftaran_id' => $fixedJalurId,
            ]);
            
            // 2. BUAT DATA ORTU (Karena isDataLengkap() mengecek $this->ortu)
            // Anda harus memastikan ada OrtuFactory.php atau buat manual di sini.
            Ortu::create([
                'siswa_id' => $siswa->id, 
                // Kolom wajib dari biodata.blade.php/Ortu
                'nama_wali' => $siswa->nama_ayah, // Menggunakan nama ayah sebagai wali
                'tempat_lahir_wali' => $this->faker->city,
                'tanggal_lahir_wali' => $this->faker->date('Y-m-d', '1980-01-01'),
                'pekerjaan_wali' => $this->faker->jobTitle,
                'alamat_wali' => $this->faker->address,
            ]);

            // 3. BUAT DATA RAPOR FILE (Untuk 5 semester)
            for ($s = 1; $s <= $REQUIRED_SEMESTERS; $s++) {
                RaporFile::create([
                    'user_id' => $user->id,
                    'semester' => $s,
                    'file_rapor' => 'dummy/rapor_file/' . $s . '_' . Str::random(5) . '.pdf',
                ]);
            }

            // 4. BUAT DATA NILAI SEMESTER (Minimal 5 entri unik semester)
            // Kita hanya perlu 5 entri unik semester agar $this->semesters()->distinct('semester')->count() > 5.
            for ($s = 1; $s <= $REQUIRED_SEMESTERS; $s++) {
                Semester::create([
                    'user_id' => $user->id,
                    'semester' => $s,
                    'mapel_id' => 1, // Asumsi Mapel ID 1 ada
                    'nilai_semester' => $this->faker->numberBetween(75, 95),
                ]);
            }
        });
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
