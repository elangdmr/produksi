<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermintaanBahan extends Model
{
    protected $table = 'permintaan_bahan';

    // field yang memang akan kamu update via form
    protected $fillable = [
        'bahan_id','jumlah','satuan','kategori','tanggal_kebutuhan','alasan','status','user_id',
        // Halal
        'tgl_pengajuan','proses','hasil_halal','tgl_verifikasi','keterangan','ulang_ke',
        // COA (kalau dipakai)
        'tgl_coa_diterima','detail_uji','hasil_uji',
        // Purchasing (opsional)
        'pabrik_pembuat','negara_asal','distributor','distributor_list',
    ];

    protected $casts = [
        // JSON
        'proses'     => 'array',
        'detail_uji' => 'array',

        // DATE
        'tgl_pengajuan'     => 'date',
        'tgl_verifikasi'    => 'date',
        'tgl_coa_diterima'  => 'date',
        'tanggal_kebutuhan' => 'date',

        // angka
        'ulang_ke' => 'integer',
    ];

    public function bahan() { return $this->belongsTo(Bahan::class, 'bahan_id'); }
    public function user()  { return $this->belongsTo(User::class); }
}
