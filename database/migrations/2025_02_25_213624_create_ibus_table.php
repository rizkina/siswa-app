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
        Schema::create('ibus', function (Blueprint $table) {
            $table->id();
            $table->string('nisn', 10);
            $table->string('nik', 16)->nullable();
            $table->string('nama')->nullable();
            $table->year('tahun_lahir')->nullable();
            $table->string('pendidikan_id')->nullable();
            $table->string('pekerjaan_id')->nullable();
            $table->string('penghasilan_id')->nullable();
            
            // Foreign keys
            $table->foreign('nisn')->references('nisn')->on('siswas')->cascadeOnDelete();
            $table->foreign('pendidikan_id')->references('id_pendidikan')->on('pendidikans')->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('pekerjaan_id')->references('id_pekerjaan')->on('pekerjaans')->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('penghasilan_id')->references('id_penghasilan')->on('penghasilans')->nullOnDelete()->cascadeOnUpdate();
            
            $table->softDeletes();
            $table->timestamps();

            $table->index('nisn');
            $table->index(['pendidikan_id', 'pekerjaan_id', 'penghasilan_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ibus');
    }
};
