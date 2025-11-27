<?php

namespace App\Http\Controllers;

use App\Models\ProduksiBatch;
use Illuminate\Http\Request;

class TabletingController extends Controller
{
    /**
     * List batch TABLET yang butuh proses Tableting.
     * Syarat:
     * - tipe_alur = TABLET_NON_SALUT / TABLET_SALUT
     * - tgl_mixing != null      (sudah selesai Mixing)
     * - tgl_tableting is null   (belum di-Tableting)
     */
    public function index(Request $request)
    {
        $search  = trim($request->get('q', ''));
        $bulan   = $request->get('bulan');   // boleh null / "all"
        $tahun   = $request->get('tahun');   // boleh null
        $perPage = (int) $request->get('per_page', 20);
        if ($perPage <= 0) {
            $perPage = 20;
        }

        // Hanya untuk alur tablet
        $alurTablet = ['TABLET_NON_SALUT', 'TABLET_SALUT'];

        $query = ProduksiBatch::with('produksi')
            ->whereIn('tipe_alur', $alurTablet)
            ->whereNotNull('tgl_mixing')     // sudah mixing
            ->whereNull('tgl_tableting');    // belum tableting

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
            ->orderBy('id')
            ->paginate($perPage)
            ->withQueryString();

        return view('produksi.tableting.index', [
            'batches' => $batches,
            'search'  => $search,
            'bulan'   => $bulan,
            'tahun'   => $tahun,
        ]);
    }

    /**
     * Riwayat batch TABLET yang sudah selesai Tableting.
     * Syarat:
     * - tipe_alur = TABLET_NON_SALUT / TABLET_SALUT
     * - tgl_tableting != null
     */
    public function history(Request $request)
    {
        $search  = trim($request->get('q', ''));
        $bulan   = $request->get('bulan');
        $tahun   = $request->get('tahun');
        $perPage = (int) $request->get('per_page', 20);
        if ($perPage <= 0) {
            $perPage = 20;
        }

        $alurTablet = ['TABLET_NON_SALUT', 'TABLET_SALUT'];

        $query = ProduksiBatch::with('produksi')
            ->whereIn('tipe_alur', $alurTablet)
            ->whereNotNull('tgl_tableting');   // sudah tableting

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
            ->orderBy('id')
            ->paginate($perPage)
            ->withQueryString();

        return view('produksi.tableting.history', [
            'batches' => $batches,
            'search'  => $search,
            'bulan'   => $bulan,
            'tahun'   => $tahun,
        ]);
    }

    /**
     * Konfirmasi tanggal Tableting untuk 1 batch.
     * - tgl_mulai_tableting = tanggal mulai
     * - tgl_tableting       = tanggal selesai (wajib)
     *
     * Setelah konfirmasi:
     * - batch hilang dari index()
     * - muncul di history()
     */
    public function confirm(Request $request, ProduksiBatch $batch)
    {
        $data = $request->validate([
            'tgl_mulai_tableting' => ['nullable', 'date'],
            'tgl_tableting'       => ['required', 'date'],
        ]);

        // Kalau mulai kosong → samakan dengan selesai
        $start = $data['tgl_mulai_tableting'] ?? $data['tgl_tableting'];

        $batch->tgl_mulai_tableting = $start;
        $batch->tgl_tableting       = $data['tgl_tableting'];

        // optional: update status_proses
        $batch->status_proses = 'TABLETING_SELESAI';

        $batch->save();

        return redirect()
            ->route('tableting.index')
            ->with('success', 'Tanggal Tableting berhasil dikonfirmasi.');
    }
}
