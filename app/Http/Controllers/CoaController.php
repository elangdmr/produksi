<?php

namespace App\Http\Controllers;

use App\Models\ProduksiBatch;
use Illuminate\Http\Request;

class CoaController extends Controller
{
    /**
     * INDEX: COA aktif
     * Menampilkan batch yang Qty Batch sudah dikonfirmasi
     * dan COA-nya masih aktif (status_coa: null/pending/done, BUKAN confirmed).
     */
    public function index(Request $request)
    {
        $q      = $request->get('q', '');
        $bulan  = $request->get('bulan');
        $tahun  = $request->get('tahun');
        $status = $request->get('status'); // '', pending, done

        $rows = ProduksiBatch::query()
            ->where('status_qty_batch', 'confirmed')

            // exclude yang sudah pindah ke riwayat (confirmed)
            ->where(function ($qb) {
                $qb->whereNull('status_coa')
                   ->orWhere('status_coa', 'pending')
                   ->orWhere('status_coa', 'done');
            })

            // search
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function ($sub) use ($q) {
                    $sub->where('nama_produk', 'like', "%{$q}%")
                        ->orWhere('no_batch', 'like', "%{$q}%")
                        ->orWhere('kode_batch', 'like', "%{$q}%");
                });
            })

            // filter bulan
            ->when($bulan !== null && $bulan !== '', function ($qb) use ($bulan) {
                $qb->where('bulan', (int) $bulan);
            })

            // filter tahun
            ->when($tahun !== null && $tahun !== '', function ($qb) use ($tahun) {
                $qb->where('tahun', (int) $tahun);
            })

            // filter status COA (pending/done)
            ->when($status !== null && $status !== '', function ($qb) use ($status) {
                $qb->where('status_coa', $status);
            })

            ->orderBy('tahun')
            ->orderBy('bulan')
            ->orderBy('wo_date')
            ->orderBy('id')
            ->paginate(25);

        return view('coa.index', compact(
            'rows',
            'q',
            'bulan',
            'tahun',
            'status'
        ));
    }

    /**
     * RIWAYAT COA
     * Menampilkan batch yang COA-nya sudah dikonfirmasi QA
     * (status_coa = confirmed).
     */
    public function history(Request $request)
    {
        $q     = $request->get('q', '');
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');

        $rows = ProduksiBatch::query()
            ->where('status_qty_batch', 'confirmed')
            ->where('status_coa', 'confirmed')

            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function ($sub) use ($q) {
                    $sub->where('nama_produk', 'like', "%{$q}%")
                        ->orWhere('no_batch', 'like', "%{$q}%")
                        ->orWhere('kode_batch', 'like', "%{$q}%");
                });
            })

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

        return view('coa.history', compact(
            'rows',
            'q',
            'bulan',
            'tahun'
        ));
    }

    /**
     * FORM EDIT COA untuk 1 batch.
     */
    public function edit(ProduksiBatch $batch)
    {
        return view('coa.edit', compact('batch'));
    }

    /**
     * SIMPAN COA DARI FORM.
     *
     * - tgl_qc_kirim_coa  (required)
     * - tgl_qa_terima_coa (nullable)
     * - status_coa:
     *     - 'done'    jika tgl_qa_terima_coa terisi
     *     - 'pending' jika kosong
     *
     * Di sini BELUM pindah ke riwayat, pindahnya ketika QA klik "Konfirmasi".
     */
    public function update(Request $request, ProduksiBatch $batch)
    {
        $data = $request->validate([
            'tgl_qc_kirim_coa'  => ['required', 'date'],
            'tgl_qa_terima_coa' => ['nullable', 'date'],
        ]);

        $statusCoa = !empty($data['tgl_qa_terima_coa']) ? 'done' : 'pending';

        $batch->update([
            'tgl_qc_kirim_coa'  => $data['tgl_qc_kirim_coa'],
            'tgl_qa_terima_coa' => $data['tgl_qa_terima_coa'] ?? null,
            'status_coa'        => $statusCoa,
        ]);

        return redirect()
            ->route('coa.index')
            ->with('ok', 'Data COA berhasil disimpan.');
    }

    /**
     * KONFIRMASI COA (QA TERIMA & FINAL).
     *
     * - set tgl_qa_terima_coa kalau masih kosong
     * - status_coa = 'confirmed'  → pindah ke Riwayat COA
     * - status_review = 'pending' → masuk ke halaman Review & Release
     * - catatan_review ditambah log singkat.
     */
    public function confirm(ProduksiBatch $batch)
    {
        $update = [
            'tgl_qa_terima_coa' => $batch->tgl_qa_terima_coa ?: now()->toDateString(),
            'status_coa'        => 'confirmed',
        ];

        $batch->update($update);

        $catatanLama = trim($batch->catatan_review ?? '');
        $tambahan    = 'COA QC/QA dikonfirmasi oleh QA pada ' . now()->format('d-m-Y') . '.';

        $batch->update([
            'status_review'  => 'pending',
            'catatan_review' => trim($catatanLama . ' ' . $tambahan),
        ]);

        return redirect()
            ->route('coa.index')
            ->with('ok', 'COA telah dikonfirmasi, dipindah ke Riwayat, dan dikirim ke Review.');
    }
}
