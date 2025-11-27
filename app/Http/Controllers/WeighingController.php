<?php

namespace App\Http\Controllers;

use App\Models\ProduksiBatch;
use Illuminate\Http\Request;

class WeighingController extends Controller
{
    /**
     * List jadwal untuk proses Weighing.
     */
    public function index(Request $request)
    {
        $search = trim($request->get('q', ''));
        $bulan  = $request->get('bulan');  // boleh null / "all"
        $tahun  = $request->get('tahun');  // boleh null

        // Ambil semua batch (baik yang sudah maupun yang belum weighing)
        $query = ProduksiBatch::with('produksi');

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

        $batches = $query->orderBy('tahun')
                         ->orderBy('bulan')
                         ->orderBy('wo_date')
                         ->paginate(20)
                         ->withQueryString();

        return view('produksi.weighing.index', [
            'batches' => $batches,
            'search'  => $search,
            'bulan'   => $bulan,
            'tahun'   => $tahun,
        ]);
    }

    /**
     * Simpan tanggal selesai weighing untuk satu batch.
     */
    public function store(Request $request, ProduksiBatch $batch)
    {
        $data = $request->validate([
            'tgl_weighing' => ['required', 'date'],
        ]);

        $batch->tgl_weighing = $data['tgl_weighing'];
        $batch->save();

        // Kalau nanti ada fungsi untuk update status, bisa dipakai di sini.
        // $batch->refreshStatus();

        return back()->with('success', 'Tanggal selesai weighing berhasil disimpan.');
    }
}
