<?php

namespace App\Http\Controllers;

use App\Models\ProduksiBatch;
use Illuminate\Http\Request;

class MixingController extends Controller
{
    /**
     * List batch yang BUTUH mixing & belum selesai mixing.
     */
    public function index(Request $request)
    {
        $search = trim($request->get('q', ''));
        $bulan  = $request->get('bulan');  // boleh null / "all"
        $tahun  = $request->get('tahun');  // boleh null
        $perPage = (int) $request->get('per_page', 25);
        if ($perPage <= 0) {
            $perPage = 25;
        }

        // TIPE ALUR YANG LEWAT MIXING
        $alurMixing = [
            'CAIRAN_LUAR',
            'DRY_SYRUP',
            'TABLET_NON_SALUT',
            'TABLET_SALUT',
            'KAPSUL',
        ];

        $query = ProduksiBatch::with('produksi')
            ->whereIn('tipe_alur', $alurMixing)
            // sudah ada WO / weighing
            ->whereNotNull('tgl_weighing')
            // yang BELUM selesai mixing
            ->whereNull('tgl_mixing');

        // Filter search (produk / no batch / kode batch)
        if ($search !== '') {
            $query->where(function ($q2) use ($search) {
                $q2->where('nama_produk', 'like', "%{$search}%")
                    ->orWhere('no_batch', 'like', "%{$search}%")
                    ->orWhere('kode_batch', 'like', "%{$search}%");
            });
        }

        // Filter bulan kalau dipilih selain "all"
        if ($bulan !== null && $bulan !== '' && $bulan !== 'all') {
            $query->where('bulan', (int) $bulan);
        }

        // Filter tahun kalau diisi
        if ($tahun !== null && $tahun !== '') {
            $query->where('tahun', (int) $tahun);
        }

        $batches = $query
            // urutkan: yang paling awal WO-nya di atas
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->orderBy('wo_date')
            ->orderBy('id')
            ->paginate($perPage)
            ->withQueryString();

        return view('produksi.mixing.index', [
            'batches' => $batches,
            'search'  => $search,
            'bulan'   => $bulan,
            'tahun'   => $tahun,
        ]);
    }

    /**
     * Riwayat batch yang sudah selesai mixing (tgl_mixing terisi).
     */
    public function history(Request $request)
    {
        $search = trim($request->get('q', ''));
        $bulan  = $request->get('bulan');  // boleh null / "all"
        $tahun  = $request->get('tahun');  // boleh null
        $perPage = (int) $request->get('per_page', 25);
        if ($perPage <= 0) {
            $perPage = 25;
        }

        $alurMixing = [
            'CAIRAN_LUAR',
            'DRY_SYRUP',
            'TABLET_NON_SALUT',
            'TABLET_SALUT',
            'KAPSUL',
        ];

        $query = ProduksiBatch::with('produksi')
            ->whereIn('tipe_alur', $alurMixing)
            ->whereNotNull('tgl_mixing'); // SUDAH mixing

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

        return view('produksi.mixing.history', [
            'batches' => $batches,
            'search'  => $search,
            'bulan'   => $bulan,
            'tahun'   => $tahun,
        ]);
    }

    /**
     * Konfirmasi mixing untuk 1 batch.
     * - tgl_mulai_mixing = tanggal mulai mixing (boleh kosong, default = tgl_mixing)
     * - tgl_mixing       = tanggal selesai mixing (wajib)
     * Setelah disimpan → batch hilang dari index & muncul di history.
     */
    public function confirm(Request $request, ProduksiBatch $batch)
    {
        $data = $request->validate([
            'tgl_mulai_mixing' => ['nullable', 'date'],
            'tgl_mixing'       => ['required', 'date'],
        ]);

        $start = $data['tgl_mulai_mixing'] ?? $data['tgl_mixing'];

        $batch->tgl_mulai_mixing = $start;
        $batch->tgl_mixing       = $data['tgl_mixing'];

        // Optional: update status_proses biar kebaca step berikutnya
        $batch->status_proses    = 'MIXING_SELESAI';

        $batch->save();

        return redirect()
            ->route('mixing.index')
            ->with('success', 'Mixing untuk batch tersebut berhasil dikonfirmasi.');
    }
}
