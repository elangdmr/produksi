<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('produksi', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Boleh nullable + unique
            $table->string('kode_produk', 50)->nullable()->unique();

            $table->string('nama_produk', 150);

            // Tablet, kapsul, sirup kering, CLO, cream, dll
            $table->string('bentuk_sediaan', 50)->nullable();

            // CLO, CAIRAN_LUAR, DRY_SYRUP, TABLET_NON_SALUT, TABLET_SALUT, KAPSUL, dll
            $table->string('tipe_alur', 50);

            // Total hari target (opsional)
            $table->unsignedInteger('leadtime_target')->nullable();

            $table->boolean('is_aktif')->default(true);

            $table->timestamps(); // created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produksi');
    }
};
