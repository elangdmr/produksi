<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bahan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bahans';

    // Boleh terus pakai nama lama; alias di bawah akan memetakan ke kolom DB sebenarnya
    protected $fillable = [
        'nama',
        'satuan_default',   // -> default_satuan
        'kategori_default', // -> kategori
        // kalau kamu pakai kolom lain silakan tambahkan juga:
        // 'kode','stok'
    ];

    // === ALIAS: satuan_default <-> default_satuan ===
    public function getSatuanDefaultAttribute()
    {
        return $this->attributes['default_satuan'] ?? null;
    }
    public function setSatuanDefaultAttribute($value): void
    {
        $this->attributes['default_satuan'] = $value;
    }

    // === ALIAS: kategori_default <-> kategori ===
    public function getKategoriDefaultAttribute()
    {
        return $this->attributes['kategori'] ?? null;
    }
    public function setKategoriDefaultAttribute($value): void
    {
        $this->attributes['kategori'] = $value;
    }

    // Opsi dropdown
    public const SATUAN   = ['gr','kg','mg','mcg','mL','L','pcs'];
    public const KATEGORI = ['Bahan Aktif','Bahan Penolong'];
}
