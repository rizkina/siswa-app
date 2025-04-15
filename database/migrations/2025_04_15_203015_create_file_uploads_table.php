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
        Schema::create('file_uploads', function (Blueprint $table) {
            $table->id();
            $table->foreign('nisn')->references('nisn')->on('siswas')->cascadeOnDelete();
            $table->foreignId('file_kategori_id')->constrained('file_kategoris')->onDelete('cascade');
            $table->string('nama_file');
            $table->string('google_drive_file_id')->nullable();
            $table->text('google_drive_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_uploads');
    }
};
