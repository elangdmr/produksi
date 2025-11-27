<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produksi_batches', function (Blueprint $table) {
            // tanggal QC kirim COA ke QA
            $table->date('tgl_qc_kirim_coa')->nullable()->after('tgl_terima_jobsheet');

            // tanggal QA terima COA
            $table->date('tgl_qa_terima_coa')->nullable()->after('tgl_qc_kirim_coa');

            // status COA: pending / done
            $table->string('status_coa', 20)->default('pending')->after('tgl_qa_terima_coa');
        });
    }

    public function down(): void
    {
        Schema::table('produksi_batches', function (Blueprint $table) {
            $table->dropColumn([
                'tgl_qc_kirim_coa',
                'tgl_qa_terima_coa',
                'status_coa',
            ]);
        });
    }
};
