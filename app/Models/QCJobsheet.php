<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QcJobSheet extends Model
{
    protected $table = 'qc_jobsheets';

    protected $fillable = [
        'produksi_batch_id',
        'tgl_konfirmasi_produksi',
        'tgl_terima_jobsheet',
    ];

    public function batch()
    {
        return $this->belongsTo(ProduksiBatch::class, 'produksi_batch_id');
    }
}
