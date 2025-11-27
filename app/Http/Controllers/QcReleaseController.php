<?php

namespace App\Http\Controllers;

use App\Models\ProduksiBatch;
use Illuminate\Http\Request;

class QcReleaseController extends Controller
{
    /**
     * List batch untuk input / edit tanggal QC.
     * Hanya batch yang sudah mulai proses (minimal Mixing)
     * dan BELUM berstatus QC RELEASED.
     */
    public function index(Request $request)
    {
        $search = trim($request->get('q', ''));
        $bulan  = $request->get('bulan');
        $tahun  = $request->get('tahun');

        $query = ProduksiBatch::with('produksi')
            ->whereNotNull('tgl_mixing') // fokus yang sudah jalan prosesnya
            ->where(function ($q) {
                $q->whereNull('status_proses')
                  ->orWhere('status_proses', '!=', 'QC RELEASED');
            });

        // Filter search (produk / no batch / kode batch)
        if ($search !== '') {
            $query->where(function ($q2) use ($search) {
                $q2->where('nama_produk', 'like', "%{$search}%")
                    ->orWhere('no_batch', 'like', "%{$search}%")
                    ->orWhere('kode_batch', 'like', "%{$search}%");
            });
        }

        // Filter bulan
        if ($bulan !== null && $bulan !== '' && $bulan !== 'all') {
            $query->where('bulan', (int) $bulan);
        }

        // Filter tahun
        if ($tahun !== null && $tahun !== '') {
            $query->where('tahun', (int) $tahun);
        }

        $batches = $query
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->orderBy('wo_date')
            ->paginate(20)
            ->withQueryString();

        return view('produksi.qc_release.index', [
            'batches' => $batches,
            'search'  => $search,
            'bulan'   => $bulan,
            'tahun'   => $tahun,
        ]);
    }

    /**
     * HISTORY: batch yang sudah dikonfirmasi QC RELEASED.
     */
    public function history(Request $request)
    {
        $search = trim($request->get('q', ''));
        $bulan  = $request->get('bulan');
        $tahun  = $request->get('tahun');

        $query = ProduksiBatch::with('produksi')
            ->where('status_proses', 'QC RELEASED');

        if ($search !== '') {
            $query->where(function ($q2) use ($search) {
                $q2->where('nama_produk', 'like', "%{$search}%")
                    ->orWhere('no_batch', 'like', "%{$search}%")
                    ->orWhere('kode_batch', 'like', "%{$search}%");
            });
        }

        if ($bulan !== null && $bulan !== '' && $bulan !== 'all') {
            $query->where('bulan', (int) $bulan);
        }

        if ($tahun !== null && $tahun !== '') {
            $query->where('tahun', (int) $tahun);
        }

        $batches = $query
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->orderBy('wo_date')
            ->paginate(20)
            ->withQueryString();

        return view('produksi.qc_release.history', [
            'batches' => $batches,
            'search'  => $search,
            'bulan'   => $bulan,
            'tahun'   => $tahun,
        ]);
    }

    /**
     * Simpan tanggal QC per batch.
     * - action = save     -> hanya simpan tanggal
     * - action = confirm  -> simpan tanggal + set status_proses = QC RELEASED
     */
    public function update(Request $request, ProduksiBatch $batch)
    {
        $data = $request->validate([
            // Produk antara Granul
            'tgl_datang_granul'        => ['nullable', 'date'],
            'tgl_analisa_granul'       => ['nullable', 'date'],
            'tgl_rilis_granul'         => ['nullable', 'date'],

            // Produk antara Tablet
            'tgl_datang_tablet'        => ['nullable', 'date'],
            'tgl_analisa_tablet'       => ['nullable', 'date'],
            'tgl_rilis_tablet'         => ['nullable', 'date'],

            // Produk Ruahan
            'tgl_datang_ruahan'        => ['nullable', 'date'],
            'tgl_analisa_ruahan'       => ['nullable', 'date'],
            'tgl_rilis_ruahan'         => ['nullable', 'date'],

            // Produk Ruahan Akhir
            'tgl_datang_ruahan_akhir'  => ['nullable', 'date'],
            'tgl_analisa_ruahan_akhir' => ['nullable', 'date'],
            'tgl_rilis_ruahan_akhir'   => ['nullable', 'date'],

            'action'                   => ['nullable', 'string'], // save / confirm
        ]);

        // Langsung simpan ke tabel produksi_batches (kolom QC detail)
        $batch->tgl_datang_granul        = $data['tgl_datang_granul']        ?? null;
        $batch->tgl_analisa_granul       = $data['tgl_analisa_granul']       ?? null;
        $batch->tgl_rilis_granul         = $data['tgl_rilis_granul']         ?? null;

        $batch->tgl_datang_tablet        = $data['tgl_datang_tablet']        ?? null;
        $batch->tgl_analisa_tablet       = $data['tgl_analisa_tablet']       ?? null;
        $batch->tgl_rilis_tablet         = $data['tgl_rilis_tablet']         ?? null;

        $batch->tgl_datang_ruahan        = $data['tgl_datang_ruahan']        ?? null;
        $batch->tgl_analisa_ruahan       = $data['tgl_analisa_ruahan']       ?? null;
        $batch->tgl_rilis_ruahan         = $data['tgl_rilis_ruahan']         ?? null;

        $batch->tgl_datang_ruahan_akhir  = $data['tgl_datang_ruahan_akhir']  ?? null;
        $batch->tgl_analisa_ruahan_akhir = $data['tgl_analisa_ruahan_akhir'] ?? null;
        $batch->tgl_rilis_ruahan_akhir   = $data['tgl_rilis_ruahan_akhir']   ?? null;

        // Kalau tombol "Konfirmasi" yang dipencet
        if (($data['action'] ?? '') === 'confirm') {
            $batch->status_proses = 'QC RELEASED';
        }

        $batch->save();

        return back()->with('success', 'Data QC berhasil disimpan.');
    }
}
