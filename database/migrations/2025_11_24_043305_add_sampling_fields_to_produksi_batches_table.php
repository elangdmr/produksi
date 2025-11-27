<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produksi_batches', function (Blueprint $table) {

            // Pastikan kolom tgl_sampling belum ada sebelum membuat
            if (!Schema::hasColumn('produksi_batches', 'tgl_sampling')) {
                $table->date('tgl_sampling')
                      ->nullable()
                      ->after('status_jobsheet');
            }

            // Pastikan kolom status_sampling belum ada, jika belum -> buat baru
            if (!Schema::hasColumn('produksi_batches', 'status_sampling')) {
                $table->enum('status_sampling', ['pending', 'accepted', 'rejected', 'confirmed'])
                      ->default('pending')
                      ->after('tgl_sampling');
            } else {
                // Jika sudah ada → ubah ENUM tanpa error
                $table->enum('status_sampling', ['pending', 'accepted', 'rejected', 'confirmed'])
                      ->default('pending')
                      ->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('produksi_batches', function (Blueprint $table) {

            // aman: cek dulu sebelum menghapus
            if (Schema::hasColumn('produksi_batches', 'status_sampling')) {
                $table->dropColumn('status_sampling');
            }

            if (Schema::hasColumn('produksi_batches', 'tgl_sampling')) {
                $table->dropColumn('tgl_sampling');
            }
        });
    }
};
