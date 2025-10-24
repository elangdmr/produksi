<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('permintaan_bahan', function (Blueprint $table) {
            // info vendor
            $table->string('pabrik_pembuat')->nullable()->after('alasan');
            $table->string('negara_asal', 100)->nullable()->after('pabrik_pembuat');
            $table->string('distributor')->nullable()->after('negara_asal');

            // timeline COA (opsional tapi kita pakai di form)
            $table->date('tgl_permintaan_coa')->nullable()->after('distributor');
            $table->date('est_coa_diterima')->nullable()->after('tgl_permintaan_coa');
            $table->date('tgl_coa_diterima')->nullable()->after('est_coa_diterima');

            // index biar list kenceng
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('permintaan_bahan', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at']);

            $table->dropColumn([
                'pabrik_pembuat',
                'negara_asal',
                'distributor',
                'tgl_permintaan_coa',
                'est_coa_diterima',
                'tgl_coa_diterima',
            ]);
        });
    }
};
