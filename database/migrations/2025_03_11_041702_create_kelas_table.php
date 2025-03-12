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
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('kelas')->unique();
            $table->foreignId('id_tingkat')->constrained('tingkats')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('id_jurusan')->constrained('jurusans')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('id_tahun_pelajaran')->constrained('tahun_pelajarans')->onUpdate('cascade')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
