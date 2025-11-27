<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produksi_batches', function (Blueprint $table) {
            // qty batch hasil Primary/Secondary Pack (boleh desimal)
            if (!Schema::hasColumn('produksi_batches', 'qty_batch')) {
                $table->decimal('qty_batch', 15, 2)->nullable()->after('tgl_secondary_pack_1');
            }
        });
    }

    public function down(): void
    {
        Schema::table('produksi_batches', function (Blueprint $table) {
            if (Schema::hasColumn('produksi_batches', 'qty_batch')) {
                $table->dropColumn('qty_batch');
            }
        });
    }
};
