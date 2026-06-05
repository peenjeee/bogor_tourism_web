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
        Schema::create('places', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kategori')->nullable();
            $table->string('label')->nullable();
            $table->text('deskripsi')->nullable();
            $table->text('alamat')->nullable();
            $table->text('fasilitas')->nullable();
            $table->string('harga_tiket')->nullable();
            $table->string('jam_operasional')->nullable();
            $table->string('telepon')->nullable();
            $table->string('url')->nullable();
            $table->string('url_gambar')->nullable();
            $table->text('tags')->nullable();
            $table->integer('likes')->default(0);
            $table->string('author')->nullable();
            $table->string('sumber')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('kategori');
            $table->index('likes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('places');
    }
};
