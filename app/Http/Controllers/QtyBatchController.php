<?php

namespace App\Http\Controllers;

use App\Models\ProduksiBatch;
use Illuminate\Http\Request;

class QtyBatchController extends Controller
{
    /**
     * INDEX: Qty Batch (aktif)
     *
     * Menampilkan batch yang:
     * - Secondary Pack sudah selesai (tgl_secondary_pack_1 NOT NULL)
     * - qty_batch sudah diisi (qty_batch NOT NULL)
     * - status_qty_batch BUKAN 'confirmed'
     *
     * Jadi begitu dikonfirmasi, batch akan hilang dari halaman ini
     * dan pindah ke halaman Riwayat Qty Batch.
     */
    public function index(Request $request)
    {
        $q      = $request->get('q', '');
        $bulan  = $request->get('bulan');
        $tahun  = $request->get('tahun');
        $status = $request->get('status'); // '', pending, rejected (confirmed tidak relevan di index)

        $rows = ProduksiBatch::with('produksi')
            ->whereNotNull('tgl_secondary_pack_1')
            ->whereNotNull('qty_batch')

            // EXCLUDE yang sudah dikonfirmasi (pindah ke riwayat)
            ->where(function ($qb) {
                $qb->whereNull('status_qty_batch')
                   ->orWhere('status_qty_batch', '!=', 'confirmed');
            })

            // Search
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function ($sub) use ($q) {
                    $sub->where('nama_produk', 'like', "%{$q}%")
                        ->orWhere('no_batch', 'like', "%{$q}%")
                        ->orWhere('kode_batch', 'like', "%{$q}%");
                });
            })

            // Filter bulan
            ->when($bulan !== null && $bulan !== '', function ($qb) use ($bulan) {
                $qb->where('bulan', (int) $bulan);
            })

            // Filter tahun
            ->when($tahun !== null && $tahun !== '', function ($qb) use ($tahun) {
                $qb->where('tahun', (int) $tahun);
            })

            // Filter status (di index cuma relevan pending / rejected)
            ->when($status !== null && $status !== '', function ($qb) use ($status) {
                if ($status === 'pending') {
                    $qb->where(function ($sub) {
                        $sub->whereNull('status_qty_batch')
                            ->orWhere('status_qty_batch', 'pending');
                    });
                } elseif ($status === 'rejected') {
                    $qb->where('status_qty_batch', 'rejected');
                }
                // kalau user pilih 'confirmed' di filter, hasil akan kosong
                // karena sudah di-exclude di query utama
            })

            ->orderBy('tahun')
            ->orderBy('bulan')
            ->orderBy('wo_date')
            ->orderBy('id')
            ->paginate(25);

        return view('qty_batch.index', compact(
            'rows',
            'q',
            'bulan',
            'tahun',
            'status'
        ));
    }

    /**
     * RIWAYAT: Qty Batch yang sudah dikonfirmasi.
     *
     * Menampilkan batch dengan:
     * - status_qty_batch = 'confirmed'
     */
    public function history(Request $request)
    {
        $q     = $request->get('q', '');
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');

        $rows = ProduksiBatch::with('produksi')
            ->whereNotNull('tgl_secondary_pack_1')
            ->whereNotNull('qty_batch')
            ->where('status_qty_batch', 'confirmed')

            // Search
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function ($sub) use ($q) {
                    $sub->where('nama_produk', 'like', "%{$q}%")
                        ->orWhere('no_batch', 'like', "%{$q}%")
                        ->orWhere('kode_batch', 'like', "%{$q}%");
                });
            })

            // Filter bulan & tahun
            ->when($bulan !== null && $bulan !== '', function ($qb) use ($bulan) {
                $qb->where('bulan', (int) $bulan);
            })
            ->when($tahun !== null && $tahun !== '', function ($qb) use ($tahun) {
                $qb->where('tahun', (int) $tahun);
            })

            ->orderBy('tahun')
            ->orderBy('bulan')
            ->orderBy('wo_date')
            ->orderBy('id')
            ->paginate(25);

        return view('qty_batch.history', compact(
            'rows',
            'q',
            'bulan',
            'tahun'
        ));
    }

    /**
     * Konfirmasi Qty batch.
     *
     * Efek:
     * - status_qty_batch        = 'confirmed'
     * - tgl_konfirmasi_produksi = diisi (dipakai modul Job Sheet QC)
     * - status_jobsheet         = 'pending' (antrian Job Sheet QC)
     * - status_coa              = 'pending' (antrian COA)
     *
     * Setelah ini batch:
     * - tidak muncul lagi di index Qty Batch
     * - muncul di Riwayat Qty Batch
     * - akan muncul di halaman Sampling, Job Sheet QC, dan COA.
     */
    public function confirm(ProduksiBatch $batch)
    {
        $batch->update([
            'status_qty_batch'        => 'confirmed',
            'tgl_konfirmasi_produksi' => $batch->tgl_konfirmasi_produksi ?: now()->toDateString(),
            'status_jobsheet'         => $batch->status_jobsheet ?: 'pending',
            'status_coa'              => $batch->status_coa ?: 'pending',
            // status_sampling boleh tetap null → di modul Sampling ditampilkan sebagai pending
        ]);

        return redirect()
            ->route('qc-jobsheet.edit', $batch->id)
            ->with('ok', 'Qty batch dikonfirmasi. Silakan isi Job Sheet QC.');
    }

    /**
     * Tolak Qty batch.
     */
    public function reject(ProduksiBatch $batch)
    {
        $batch->update([
            'status_qty_batch' => 'rejected',
        ]);

        return back()->with('ok', 'Qty batch ditolak.');
    }
}
