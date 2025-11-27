<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produksi_batches', function (Blueprint $table) {

            // Tanggal otomatis saat Qty Batch dikonfirmasi
            $table->date('tgl_konfirmasi_produksi')
                  ->nullable()
                  ->after('status_qty_batch');

            // Tanggal terima jobsheet (manual input)
            $table->date('tgl_terima_jobsheet')
                  ->nullable()
                  ->after('tgl_konfirmasi_produksi');

            // Status proses Job Sheet
            $table->enum('status_jobsheet', ['pending', 'done'])
                  ->default('pending')
                  ->after('tgl_terima_jobsheet');

            // Catatan dari QC Produksi untuk Job Sheet
            $table->text('catatan_jobsheet')
                  ->nullable()
                  ->after('status_jobsheet');
        });
    }

    public function down(): void
    {
        Schema::table('produksi_batches', function (Blueprint $table) {
            $table->dropColumn([
                'tgl_konfirmasi_produksi',
                'tgl_terima_jobsheet',
                'status_jobsheet',
                'catatan_jobsheet'
            ]);
        });
    }
};
