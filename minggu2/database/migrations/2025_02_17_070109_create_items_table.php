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
        Schema::create('items', function (Blueprint $table) {
            $table->id(); // Membuat kolom 'id' sebagai primary key dengan auto-increment.
            $table->string('name'); // Menyimpan nama item dalam format teks pendek (maks. 255 karakter).
            $table->text('description'); // Menyimpan deskripsi item dalam format teks panjang.
            $table->timestamps(); // Menambahkan kolom 'created_at' dan 'updated_at' untuk mencatat waktu perubahan data.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items'); // Menghapus tabel 'items' jika ada.
    }
};
