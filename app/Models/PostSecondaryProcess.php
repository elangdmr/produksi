<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostSecondaryProcess extends Model
{
    use HasFactory;

    protected $table = 'post_secondary_processes';

    protected $fillable = [
        'produksi_batch_id',
        'qty_batch',
        'proses_selanjutnya',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    public function batch()
    {
        return $this->belongsTo(ProduksiBatch::class, 'produksi_batch_id');
    }
}
