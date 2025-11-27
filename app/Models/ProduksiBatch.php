<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProduksiBatch extends Model
{
    protected $table = 'produksi_batches';

    protected $fillable = [

        /* ====================== IDENTITAS BATCH ====================== */
        'produksi_id',
        'nama_produk',
        'no_batch',
        'kode_batch',
        'batch_ke',
        'bulan',
        'tahun',
        'tipe_alur',

        'wo_date',
        'expected_date',

        /* ====================== PROSES PRODUKSI ====================== */
        'tgl_mulai_weighing',
        'tgl_weighing',

        'tgl_mulai_mixing',
        'tgl_mixing',

        'tgl_mulai_capsule_filling',
        'tgl_capsule_filling',

        'tgl_mulai_tableting',
        'tgl_tableting',

        'tgl_mulai_coating',
        'tgl_coating',

        'tgl_mulai_primary_pack',
        'tgl_primary_pack',

        'tgl_mulai_secondary_pack_1',
        'tgl_secondary_pack_1',

        'tgl_mulai_secondary_pack_2',
        'tgl_secondary_pack_2',

        'hari_kerja',
        'status_proses',

        /* ====================== QC DETAIL ====================== */
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

        /* ====================== AFTER SECONDARY PACK ====================== */
        'qty_batch',
        'status_qty_batch',        // pending | confirmed | rejected

        /* ==== Job Sheet QC ==== */
        'tgl_konfirmasi_produksi',
        'tgl_terima_jobsheet',
        'status_jobsheet',         // pending | done
        'catatan_jobsheet',

        /* ==== Sampling QC ==== */
        'tgl_sampling',
        'status_sampling',         // pending | accepted | rejected
        'catatan_sampling',

        /* ==== COA QC/QA ==== */
        // PAKAI NAMA YANG ADA DI DATABASE
        'tgl_qc_kirim_coa',        // tanggal QC kirim COA ke QA
        'tgl_qa_terima_coa',       // tanggal QA terima/approve COA
        'status_coa',              // pending | done | rejected (opsional)
        'catatan_coa',

        /* ==== REVIEW QA ==== */
        'status_review',           // pending | hold | released | rejected
        'tgl_review',
        'catatan_review',
    ];

    /* ================= CASTS ================= */
    protected $casts = [
        'wo_date'              => 'date',
        'expected_date'        => 'date',

        'tgl_mulai_weighing'   => 'date',
        'tgl_weighing'         => 'date',

        'tgl_mulai_mixing'     => 'date',
        'tgl_mixing'           => 'date',

        'tgl_mulai_capsule_filling' => 'date',
        'tgl_capsule_filling'       => 'date',

        'tgl_mulai_tableting'  => 'date',
        'tgl_tableting'        => 'date',

        'tgl_mulai_coating'    => 'date',
        'tgl_coating'          => 'date',

        'tgl_mulai_primary_pack'    => 'date',
        'tgl_primary_pack'          => 'date',

        'tgl_mulai_secondary_pack_1'=> 'date',
        'tgl_secondary_pack_1'      => 'date',

        'tgl_mulai_secondary_pack_2'=> 'date',
        'tgl_secondary_pack_2'      => 'date',

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

        'qty_batch'               => 'integer',

        'tgl_konfirmasi_produksi' => 'date',
        'tgl_terima_jobsheet'     => 'date',

        'tgl_sampling'            => 'date',

        // CAST sesuai nama kolom di DB
        'tgl_qc_kirim_coa'        => 'date',
        'tgl_qa_terima_coa'       => 'date',

        'tgl_review'              => 'date',
    ];

    /* ================= RELATION ================= */
    public function produksi()
    {
        return $this->belongsTo(Produksi::class, 'produksi_id');
    }

    // alias
    public function produk()
    {
        return $this->produksi();
    }

    public function qcRelease()
    {
        return $this->hasOne(QcRelease::class, 'produksi_batch_id');
    }

    /* ================= SCOPES ================= */

    // Weighing
    public function scopeNeedWeighing($q)
    {
        return $q->whereNull('tgl_weighing');
    }

    // Mixing
    public function scopeNeedMixing($q)
    {
        return $q->whereNotNull('tgl_weighing')
                 ->whereNull('tgl_mixing');
    }

    // Batch yang bisa masuk Qty Batch
    public function scopeHasQtyAfterSecondary($q)
    {
        return $q->whereNotNull('tgl_secondary_pack_1')
                 ->whereNotNull('qty_batch');
    }

    // Untuk modul Review (complete step)
    public function scopeReadyForReview($q)
    {
        return $q->where('status_qty_batch', 'confirmed')
                 ->where('status_jobsheet', 'done')
                 ->where('status_sampling', 'accepted')
                 ->where('status_coa', 'done'); // tadinya 'approved'
    }
}
