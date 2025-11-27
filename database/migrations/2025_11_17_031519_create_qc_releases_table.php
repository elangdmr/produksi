<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qc_releases', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('produksi_batch_id');

            // Produk antara Granul
            $table->date('tgl_datang_granul')->nullable();
            $table->date('tgl_analisa_granul')->nullable();
            $table->date('tgl_rilis_granul')->nullable();

            // Produk antara Tablet
            $table->date('tgl_datang_tablet')->nullable();
            $table->date('tgl_analisa_tablet')->nullable();
            $table->date('tgl_rilis_tablet')->nullable();

            // Produk Ruahan
            $table->date('tgl_datang_ruahan')->nullable();
            $table->date('tgl_analisa_ruahan')->nullable();
            $table->date('tgl_rilis_ruahan')->nullable();

            // Produk Ruahan Akhir
            $table->date('tgl_datang_ruahan_akhir')->nullable();
            $table->date('tgl_analisa_ruahan_akhir')->nullable();
            $table->date('tgl_rilis_ruahan_akhir')->nullable();

            $table->timestamps();

            $table->foreign('produksi_batch_id')
                ->references('id')
                ->on('produksi_batches')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qc_releases');
    }
};
