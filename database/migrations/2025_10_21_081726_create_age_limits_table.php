<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('age_limits', function (Blueprint $table) {
            $table->id();
            // Umur minimum dalam tahun (misal: 14)
            $table->integer('min_age_years')->default(14); 
            // Umur maksimum dalam tahun (misal: 17)
            $table->integer('max_age_years')->default(17); 
            // Tanggal acuan untuk perhitungan usia
            $table->date('reference_date')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('age_limits');
    }
};
