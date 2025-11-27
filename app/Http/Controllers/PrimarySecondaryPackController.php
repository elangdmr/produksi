<?php

namespace App\Http\Controllers;

use App\Models\ProduksiBatch;
use Illuminate\Http\Request;

class PrimarySecondaryPackController extends Controller
{
    /**
     * List batch + form input tanggal Primary & Secondary Pack.
     * Hanya untuk batch yang MASIH PROSES (belum selesai qty)
     * dan SUDAH melewati proses sebelum Primary (minimal Coating).
     */
    public function index(Request $request)
    {
        $q     = $request->get('q', '');
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');

        $rows = ProduksiBatch::with('produksi')
            // WAJIB: hanya muncul setelah Coating selesai
            ->whereNotNull('tgl_coating')
            // (opsional) kalau mau exclude yang qty-nya sudah final
            // ->whereNull('qty_batch')
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

        return view('primary_secondary_pack.index', compact(
            'rows',
            'q',
            'bulan',
            'tahun'
        ));
    }

    /**
     * RIWAYAT: batch yang sudah selesai Secondary Pack
     * (biasanya sudah / siap diinput Qty Batch).
     */
    public function history(Request $request)
    {
        $q     = $request->get('q', '');
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');

        $rows = ProduksiBatch::with('produksi')
            ->whereNotNull('tgl_secondary_pack_1')
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

        return view('primary_secondary_pack.history', compact(
            'rows',
            'q',
            'bulan',
            'tahun'
        ));
    }

    /**
     * Validasi payload tanggal.
     */
    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'tgl_mulai_primary_pack'     => ['nullable', 'date'],
            'tgl_primary_pack'           => ['nullable', 'date'],
            'tgl_mulai_secondary_pack_1' => ['nullable', 'date'],
            'tgl_secondary_pack_1'       => ['nullable', 'date'],
        ]);
    }

    /**
     * SIMPAN tanggal primary & secondary pack untuk 1 batch (tanpa konfirmasi).
     */
    public function store(Request $request, ProduksiBatch $batch)
    {
        $data = $this->validatePayload($request);

        $batch->update($data);

        return back()->with('ok', 'Tanggal Primary & Secondary Pack berhasil disimpan.');
    }

    /**
     * KONFIRMASI tanggal primary & secondary pack.
     * Setelah konfirmasi, user diarahkan ke halaman input QTY batch.
     */
    public function confirm(Request $request, ProduksiBatch $batch)
    {
        $data = $this->validatePayload($request);
        $batch->update($data);

        // Pastikan Secondary Pack selesai sudah terisi
        $secondaryDone = $data['tgl_secondary_pack_1'] ?? $batch->tgl_secondary_pack_1;

        if (empty($secondaryDone)) {
            return back()->withErrors([
                'secondary' => 'Tanggal Secondary Pack (Selesai) harus diisi sebelum konfirmasi Qty Batch.',
            ]);
        }

        return redirect()
            ->route('primary-secondary.qty.form', $batch->id)
            ->with('ok', 'Tanggal Primary & Secondary Pack berhasil dikonfirmasi. Silakan input Qty Batch.');
    }

    /**
     * Tampilkan form input QTY untuk 1 batch.
     */
    public function qtyForm(ProduksiBatch $batch)
    {
        return view('primary_secondary_pack.qty_form', compact('batch'));
    }

    /**
     * Simpan QTY batch.
     */
    public function qtySave(Request $request, ProduksiBatch $batch)
    {
        $data = $request->validate([
            'qty_batch' => ['required', 'integer', 'min:0'],
        ]);

        $batch->update([
            'qty_batch'        => $data['qty_batch'],
            'status_qty_batch' => 'pending',   // default status setelah input
        ]);

        return redirect()
            ->route('qty-batch.index')
            ->with('ok', 'Qty Batch berhasil disimpan.');
    }
}
