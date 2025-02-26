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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('nisn', 10);
            $table->enum('kategori', ['Ijazah', 'Kartu Keluarga', 'Akta Kelahiran']);
            $table->string('file');
            $table->text('path')->nullable();

            $table->foreign('nisn')->references('nisn')->on('siswas')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
