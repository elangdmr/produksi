<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Pass 1
        Schema::table('permintaan_bahan', function (Blueprint $table) {
            $afterBase = Schema::hasColumn('permintaan_bahan', 'alasan')
                ? 'alasan'
                : (Schema::hasColumn('permintaan_bahan', 'bahan_id') ? 'bahan_id' : null);

            if (!Schema::hasColumn('permintaan_bahan', 'pabrik_pembuat')) {
                $col = $table->string('pabrik_pembuat')->nullable();
                if ($afterBase) $col->after($afterBase);
            }
        });

        // Pass 2
        Schema::table('permintaan_bahan', function (Blueprint $table) {
            if (!Schema::hasColumn('permintaan_bahan', 'negara_asal')) {
                $table->string('negara_asal', 100)->nullable()->after('pabrik_pembuat');
            }
            if (!Schema::hasColumn('permintaan_bahan', 'distributor')) {
                $table->string('distributor')->nullable()->after('negara_asal');
            }
        });

        // Pass 3
        Schema::table('permintaan_bahan', function (Blueprint $table) {
            if (!Schema::hasColumn('permintaan_bahan', 'distributor_list')) {
                $table->json('distributor_list')->nullable()->after('distributor');
            }
        });

        // Pass 4
        Schema::table('permintaan_bahan', function (Blueprint $table) {
            if (!Schema::hasColumn('permintaan_bahan', 'tgl_permintaan_coa')) {
                $table->date('tgl_permintaan_coa')->nullable()->after('distributor_list');
            }
            if (!Schema::hasColumn('permintaan_bahan', 'est_coa_diterima')) {
                $table->date('est_coa_diterima')->nullable()->after('tgl_permintaan_coa');
            }
            if (!Schema::hasColumn('permintaan_bahan', 'tgl_coa_diterima')) {
                $table->date('tgl_coa_diterima')->nullable()->after('est_coa_diterima');
            }
        });

        // Migrasi data "distributor" -> "distributor_list"
        if (Schema::hasColumn('permintaan_bahan', 'distributor')
            && Schema::hasColumn('permintaan_bahan', 'distributor_list')) {

            DB::table('permintaan_bahan')
              ->whereNotNull('distributor')
              ->orderBy('id')
              ->chunkById(200, function ($rows) {
                  foreach ($rows as $r) {
                      $arr = array_values(array_filter(array_map('trim', explode(',', (string)$r->distributor))));
                      DB::table('permintaan_bahan')
                        ->where('id', $r->id)
                        ->update(['distributor_list' => json_encode($arr, JSON_UNESCAPED_UNICODE)]);
                  }
              });
        }
    }

    public function down(): void
    {
        Schema::table('permintaan_bahan', function (Blueprint $table) {
            foreach ([
                'tgl_coa_diterima',
                'est_coa_diterima',
                'tgl_permintaan_coa',
                'distributor_list',
                'distributor',
                'negara_asal',
                'pabrik_pembuat',
            ] as $col) {
                if (Schema::hasColumn('permintaan_bahan', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
