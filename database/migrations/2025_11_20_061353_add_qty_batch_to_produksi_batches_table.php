<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah qty_batch kalau BELUM ada
        if (!Schema::hasColumn('produksi_batches', 'qty_batch')) {
            Schema::table('produksi_batches', function (Blueprint $table) {
                $table->integer('qty_batch')->nullable()->after('tgl_secondary_pack_1');
            });
        }

        // Tambah status_qty_batch kalau BELUM ada
        if (!Schema::hasColumn('produksi_batches', 'status_qty_batch')) {
            Schema::table('produksi_batches', function (Blueprint $table) {
                $table->enum('status_qty_batch', ['pending', 'confirmed', 'rejected'])
                      ->default('pending')
                      ->after('qty_batch');
            });
        }
    }

    public function down(): void
    {
        // Hapus kolom hanya kalau memang ada
        if (Schema::hasColumn('produksi_batches', 'status_qty_batch')) {
            Schema::table('produksi_batches', function (Blueprint $table) {
                $table->dropColumn('status_qty_batch');
            });
        }

        if (Schema::hasColumn('produksi_batches', 'qty_batch')) {
            Schema::table('produksi_batches', function (Blueprint $table) {
                $table->dropColumn('qty_batch');
            });
        }
    }
};
