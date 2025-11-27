<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produksi_batches', function (Blueprint $table) {
            // status_review: pending | hold | released | rejected
            $table->string('status_review')->default('pending')->after('status_coa');
            $table->date('tgl_review')->nullable()->after('status_review');
            $table->text('catatan_review')->nullable()->after('tgl_review');
        });
    }

    public function down(): void
    {
        Schema::table('produksi_batches', function (Blueprint $table) {
            $table->dropColumn(['status_review', 'tgl_review', 'catatan_review']);
        });
    }
};
