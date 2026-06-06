<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Repair databases where the old places migrations are marked as ran,
     * but the table itself is missing.
     */
    public function up(): void
    {
        if (!Schema::hasTable('places')) {
            Schema::create('places', function (Blueprint $table) {
                $table->id();
                $table->string('nama');
                $table->string('kategori')->nullable();
                $table->string('label')->nullable();
                $table->text('deskripsi')->nullable();
                $table->text('alamat')->nullable();
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();
                $table->text('fasilitas')->nullable();
                $table->string('harga_tiket')->nullable();
                $table->string('jam_operasional')->nullable();
                $table->string('telepon')->nullable();
                $table->text('url')->nullable();
                $table->text('url_gambar')->nullable();
                $table->text('tags')->nullable();
                $table->integer('likes')->default(0);
                $table->string('author')->nullable();
                $table->string('sumber')->nullable();
                $table->timestamps();

                $table->index('kategori');
                $table->index('likes');
                $table->index(['latitude', 'longitude']);
            });

            return;
        }

        Schema::table('places', function (Blueprint $table) {
            if (!Schema::hasColumn('places', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable()->after('alamat');
            }

            if (!Schema::hasColumn('places', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            }
        });
    }

    /**
     * Keep rollback non-destructive because this migration may only repair a
     * table that previous migrations already claim to own.
     */
    public function down(): void
    {
        //
    }
};
