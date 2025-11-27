<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QcRelease extends Model
{
    protected $table = 'qc_releases';

    protected $fillable = [
        'produksi_batch_id',

        'tgl_datang_granul',
        'tgl_analisa_granul',
        'tgl_rilis_granul',

        'tgl_datang_tablet',
        'tgl_analisa_tablet',
        'tgl_rilis_tablet',

        'tgl_datang_ruahan',
        'tgl_analisa_ruahan',
        'tgl_rilis_ruahan',

        'tgl_datang_ruahan_akhir',
        'tgl_analisa_ruahan_akhir',
        'tgl_rilis_ruahan_akhir',
    ];

    protected $casts = [
        'tgl_datang_granul'        => 'date',
        'tgl_analisa_granul'       => 'date',
        'tgl_rilis_granul'         => 'date',

        'tgl_datang_tablet'        => 'date',
        'tgl_analisa_tablet'       => 'date',
        'tgl_rilis_tablet'         => 'date',

        'tgl_datang_ruahan'        => 'date',
        'tgl_analisa_ruahan'       => 'date',
        'tgl_rilis_ruahan'         => 'date',

        'tgl_datang_ruahan_akhir'  => 'date',
        'tgl_analisa_ruahan_akhir' => 'date',
        'tgl_rilis_ruahan_akhir'   => 'date',
    ];

    public function batch()
    {
        return $this->belongsTo(ProduksiBatch::class, 'produksi_batch_id');
    }
}
