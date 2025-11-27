<?php

namespace App\Http\Controllers;

use App\Models\ProduksiBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CoatingController extends Controller
{
    /* =========================================================
     * INDEX – daftar batch yang BELUM selesai Coating
     * (sudah Tableting, tgl_coating masih NULL)
     * =======================================================*/
    public function index(Request $request)
    {
        $search = trim($request->get('q', ''));
        $bulan  = $request->get('bulan');
        $tahun  = $request->get('tahun');

        $query = ProduksiBatch::with('produksi')
            ->whereNotNull('tgl_tableting')  // setelah Tableting
            ->whereNull('tgl_coating');      // belum selesai Coating (belum dikonfirmasi)

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
            ->paginate(25);

        $batches->appends($request->query());

        return view('produksi.coating.index', compact(
            'batches',
            'search',
            'bulan',
            'tahun'
        ));
    }

    /* =========================================================
     * HISTORY – daftar batch yang SUDAH selesai Coating
     * (tgl_coating TIDAK NULL)
     * =======================================================*/
    public function history(Request $request)
    {
        $search = trim($request->get('q', ''));
        $bulan  = $request->get('bulan');
        $tahun  = $request->get('tahun');

        $query = ProduksiBatch::with('produksi')
            ->whereNotNull('tgl_tableting')
            ->whereNotNull('tgl_coating'); // sudah ada tanggal Coating (riwayat)

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
            ->paginate(25);

        $batches->appends($request->query());

        return view('produksi.coating.history', compact(
            'batches',
            'search',
            'bulan',
            'tahun'
        ));
    }

    /* =========================================================
     * SIMPAN INLINE DARI TABEL INDEX
     * (Simpan & Konfirmasi Coating)
     * =======================================================*/
    public function store(Request $request, ProduksiBatch $batch)
    {
        $data = $request->validate([
            'tgl_mulai_coating' => ['nullable', 'date'],
            'tgl_coating'       => ['nullable', 'date'],
        ]);

        $batch->tgl_mulai_coating = $data['tgl_mulai_coating'] ?? null;
        $batch->tgl_coating       = $data['tgl_coating'] ?? null;
        $batch->save();

        return back()->with('success', 'Tanggal Coating berhasil disimpan.');
    }

    /* =========================================================
     * FORM EDIT SATU BATCH
     * =======================================================*/
    public function edit(ProduksiBatch $batch)
    {
        return view('produksi.coating.edit', compact('batch'));
    }

    /* =========================================================
     * UPDATE DARI HALAMAN EDIT
     * =======================================================*/
    public function update(Request $request, ProduksiBatch $batch)
    {
        $data = $request->validate([
            'tgl_mulai_coating' => ['nullable', 'date'],
            'tgl_coating'       => ['nullable', 'date'],
        ]);

        $batch->update($data);

        return redirect()
            ->route('coating.index')
            ->with('success', 'Data Coating berhasil diperbarui.');
    }

    /* =========================================================
     * MESIN 2 (EAZ) – SPLIT & DELETE
     * =======================================================*/

    /**
     * Duplikasi batch untuk mesin 2 dengan kode EAZ-...
     */
    public function splitEaz(ProduksiBatch $batch)
    {
        // hanya boleh dari kode "EA-"
        if (! Str::contains($batch->kode_batch, 'EA-')) {
            return back()->with('success', 'Batch ini tidak bisa di-split ke EAZ.');
        }

        // generate kode EAZ baru
        $kodeEaz = Str::replaceFirst('EA-', 'EAZ-', $batch->kode_batch);

        // jangan buat kalau sudah ada EAZ-nya
        $sudahAda = ProduksiBatch::where('kode_batch', $kodeEaz)
            ->where('no_batch', $batch->no_batch)
            ->exists();

        if ($sudahAda) {
            return back()->with('success', 'Mesin 2 (EAZ) sudah pernah dibuat.');
        }

        // clone record
        $new = $batch->replicate();
        $new->kode_batch = $kodeEaz;
        $new->save();

        return back()->with('success', 'Batch mesin 2 (EAZ) berhasil dibuat.');
    }

    /**
     * Hapus baris mesin 2 (EAZ).
     */
    public function destroyEaz(ProduksiBatch $batch)
    {
        // safety: pastikan memang EAZ
        if (! Str::contains($batch->kode_batch, 'EAZ-')) {
            return back()->with('success', 'Batch ini bukan mesin 2 (EAZ). Tidak dihapus.');
        }

        $batch->delete();

        return back()->with('success', 'Batch mesin 2 (EAZ) berhasil dihapus.');
    }
}
