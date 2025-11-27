<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produksi extends Model
{
    // Nama tabel di DB
    protected $table = 'produksi';

    // Kolom yang boleh diisi mass assignment
    protected $fillable = [
        'kode_produk',
        'nama_produk',
        'bentuk_sediaan',
        'tipe_alur',
        'leadtime_target',
        'is_aktif',
    ];

    protected $casts = [
        'is_aktif'       => 'boolean',
        'leadtime_target'=> 'integer',
    ];

    /* ========= Scope kecil opsional ========= */

    // Hanya produk aktif
    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }
}
