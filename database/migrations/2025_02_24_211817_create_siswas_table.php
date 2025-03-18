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
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            $table->string('nisn', 10)->unique();
            $table->string('nipd')->unique();
            $table->string('nik', 16)->unique();
            $table->string('nama');
            $table->enum('jns_kelamin', ['L', 'P'])->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('agama_id')->nullable();
            $table->foreign('agama_id')->references('id_agama')->on('agamas')->cascadeOnUpdate()->nullOnDelete();
            $table->string('alamat')->nullable();
            $table->text('foto')->nullable();

            // Menambhakan id_kelas untuk relasi One- to-Many
            $table->foreignId('id_kelas')->constrained('kelas')->cascadeOnUpdate()->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
