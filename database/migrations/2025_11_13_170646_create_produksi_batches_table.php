<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produksi_batches', function (Blueprint $table) {
            $table->id();

            // Relasi ke master produksi
            $table->foreignId('produksi_id')
                ->nullable()
                ->constrained('produksi')
                ->nullOnDelete();

            $table->string('nama_produk');
            $table->string('no_batch')->nullable();
            $table->string('kode_batch')->nullable();
            $table->unsignedInteger('batch_ke')->default(1);

            $table->unsignedTinyInteger('bulan')->nullable();
            $table->unsignedSmallInteger('tahun')->nullable();
            $table->string('tipe_alur', 50)->nullable();

            /* ===================== WORK ORDER ===================== */
            $table->date('wo_date')->nullable();
            $table->date('expected_date')->nullable();

            /* ===================== PROSES PRODUKSI ===================== */
            // Weighing
            $table->date('tgl_mulai_weighing')->nullable();
            $table->date('tgl_weighing')->nullable();

            // Mixing
            $table->date('tgl_mulai_mixing')->nullable();
            $table->date('tgl_mixing')->nullable();

            // Capsule Filling
            $table->date('tgl_mulai_capsule_filling')->nullable();
            $table->date('tgl_capsule_filling')->nullable();

            // Tableting
            $table->date('tgl_mulai_tableting')->nullable();
            $table->date('tgl_tableting')->nullable();

            // Coating
            $table->date('tgl_mulai_coating')->nullable();
            $table->date('tgl_coating')->nullable();

            // Primary Pack
            $table->date('tgl_mulai_primary_pack')->nullable();
            $table->date('tgl_primary_pack')->nullable();

            // Secondary Pack
            $table->date('tgl_mulai_secondary_pack_1')->nullable();
            $table->date('tgl_secondary_pack_1')->nullable();
            $table->date('tgl_mulai_secondary_pack_2')->nullable();
            $table->date('tgl_secondary_pack_2')->nullable();

            /* ===================== QC — PRODUK ANTARA GRANUL ===================== */
            $table->date('tgl_datang_granul')->nullable();
            $table->date('tgl_analisa_granul')->nullable();
            $table->date('tgl_rilis_granul')->nullable();

            /* ===================== QC — PRODUK ANTARA TABLET ===================== */
            $table->date('tgl_datang_tablet')->nullable();
            $table->date('tgl_analisa_tablet')->nullable();
            $table->date('tgl_rilis_tablet')->nullable();

            /* ===================== QC — PRODUK RUAHAN ===================== */
            $table->date('tgl_datang_ruahan')->nullable();
            $table->date('tgl_analisa_ruahan')->nullable();
            $table->date('tgl_rilis_ruahan')->nullable();

            /* ===================== QC — PRODUK RUAHAN AKHIR ===================== */
            $table->date('tgl_datang_ruahan_akhir')->nullable();
            $table->date('tgl_analisa_ruahan_akhir')->nullable();
            $table->date('tgl_rilis_ruahan_akhir')->nullable();

            /* ===================== LAINNYA ===================== */
            $table->unsignedInteger('hari_kerja')->nullable();
            $table->string('status_proses', 50)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produksi_batches');
    }
};

        