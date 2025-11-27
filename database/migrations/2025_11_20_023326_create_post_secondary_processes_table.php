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
        Schema::create('post_secondary_processes', function (Blueprint $table) {
            $table->id();

            // Relasi ke produksi_batches (batch produksi utama)
            $table->unsignedBigInteger('produksi_batch_id');
            $table->foreign('produksi_batch_id')
                  ->references('id')->on('produksi_batches')
                  ->onDelete('cascade');

            // Data utama
            $table->unsignedBigInteger('qty_batch')->nullable()
                  ->comment('Qty hasil akhir setelah secondary pack, boleh null kalau belum diisi');

            $table->string('proses_selanjutnya', 100)->nullable()
                  ->comment('Nama proses lanjutan, mis: Pengiriman, Release, dsb');

            // Range tanggal proses lanjutan
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();

            // Status proses setelah secondary
            // contoh nilai: pending, on_progress, done, rejected, confirmed
            $table->string('status', 50)->default('pending');

            // Catatan / alasan tolak / remark lain
            $table->text('keterangan')->nullable();

            // Optional: siapa yang update (kalau mau dipakai nanti)
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_secondary_processes');
    }
};
