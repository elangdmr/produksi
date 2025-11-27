<?php

namespace App\Http\Controllers;

use App\Models\ProduksiBatch;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Halaman Review After Secondary Pack (AKTIF).
     *
     * Menampilkan batch yang:
     * - Sudah punya Qty Batch
     * - Qty Batch sudah dikonfirmasi (status_qty_batch = confirmed)
     *
     * Default-nya hanya menampilkan batch yang
     *   status_review: NULL / pending / hold
     * (released & rejected pindah ke Riwayat).
     */
    public function index(Request $request)
    {
        $q      = $request->get('q', '');
        $bulan  = $request->get('bulan');
        $tahun  = $request->get('tahun');
        $status = $request->get('status'); // pending|hold|released|rejected|null

        $rows = ProduksiBatch::with('produksi')
            // SYARAT UTAMA: Qty Batch sudah dikonfirmasi
            ->whereNotNull('qty_batch')
            ->where('status_qty_batch', 'confirmed')

            // PENCARIAN
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function ($sub) use ($q) {
                    $sub->where('nama_produk', 'like', "%{$q}%")
                        ->orWhere('no_batch', 'like', "%{$q}%")
                        ->orWhere('kode_batch', 'like', "%{$q}%");
                });
            })

            // FILTER BULAN
            ->when($bulan !== null && $bulan !== '', function ($qb) use ($bulan) {
                $qb->where('bulan', (int) $bulan);
            })

            // FILTER TAHUN
            ->when($tahun !== null && $tahun !== '', function ($qb) use ($tahun) {
                $qb->where('tahun', (int) $tahun);
            })

            // Kalau user PILIH status di filter → pakai apa adanya
            ->when($status !== null && $status !== '', function ($qb) use ($status) {
                if ($status === 'pending') {
                    // pending = NULL atau 'pending'
                    $qb->where(function ($sub) {
                        $sub->whereNull('status_review')
                            ->orWhere('status_review', 'pending');
                    });
                } else {
                    $qb->where('status_review', $status);
                }
            })

            // Kalau user TIDAK pilih status:
            // hanya tampilkan yang BELUM FINAL:
            //   NULL / pending / hold
            ->when($status === null || $status === '', function ($qb) {
                $qb->where(function ($sub) {
                    $sub->whereNull('status_review')
                        ->orWhereIn('status_review', ['pending', 'hold']);
                });
            })

            ->orderBy('tahun')
            ->orderBy('bulan')
            ->orderBy('wo_date')
            ->orderBy('id')
            ->paginate(25);

        return view('review.index', compact(
            'rows',
            'q',
            'bulan',
            'tahun',
            'status'
        ));
    }

    /**
     * RIWAYAT REVIEW:
     * Menampilkan batch yang Review-nya SUDAH FINAL:
     *   status_review IN ('released', 'rejected')
     */
    public function history(Request $request)
    {
        $q      = $request->get('q', '');
        $bulan  = $request->get('bulan');
        $tahun  = $request->get('tahun');
        $status = $request->get('status'); // released|rejected|null

        $rows = ProduksiBatch::with('produksi')
            ->whereNotNull('qty_batch')
            ->where('status_qty_batch', 'confirmed')
            ->whereIn('status_review', ['released', 'rejected'])

            // PENCARIAN
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function ($sub) use ($q) {
                    $sub->where('nama_produk', 'like', "%{$q}%")
                        ->orWhere('no_batch', 'like', "%{$q}%")
                        ->orWhere('kode_batch', 'like', "%{$q}%");
                });
            })

            // FILTER BULAN
            ->when($bulan !== null && $bulan !== '', function ($qb) use ($bulan) {
                $qb->where('bulan', (int) $bulan);
            })

            // FILTER TAHUN
            ->when($tahun !== null && $tahun !== '', function ($qb) use ($tahun) {
                $qb->where('tahun', (int) $tahun);
            })

            // FILTER STATUS (optional)
            ->when($status !== null && $status !== '', function ($qb) use ($status) {
                $qb->where('status_review', $status);
            })

            ->orderBy('tahun')
            ->orderBy('bulan')
            ->orderBy('wo_date')
            ->orderBy('id')
            ->paginate(25);

        return view('review.history', compact(
            'rows',
            'q',
            'bulan',
            'tahun',
            'status'
        ));
    }

    /**
     * HOLD:
     * - status_review = hold
     * - status_jobsheet / status_coa bisa dikembalikan ke pending
     *   tergantung pilihan return_to (jobsheet|coa|both)
     * - kalau ke COA: tgl_qa_terima_coa dikosongkan lagi
     * - catatan_review diisi dengan status dokumen + catatan tambahan
     */
    public function hold(Request $request, ProduksiBatch $batch)
    {
        $data = $request->validate([
            'return_to'      => ['required', 'in:jobsheet,coa,both'],
            'doc_status'     => ['required', 'in:belum_lengkap,lengkap'],
            'catatan_review' => ['nullable', 'string', 'max:1000'],
        ]);

        // Status dokumen
        $infoDoc = $data['doc_status'] === 'belum_lengkap'
            ? 'Dokumen belum lengkap.'
            : 'Dokumen lengkap (perlu pengecekan ulang).';

        // Kembalikan ke modul mana
        switch ($data['return_to']) {
            case 'jobsheet':
                $infoReturn = 'Dikembalikan ke Job Sheet QC.';
                break;
            case 'coa':
                $infoReturn = 'Dikembalikan ke COA QC/QA.';
                break;
            default: // both
                $infoReturn = 'Dikembalikan ke Job Sheet QC dan COA QC/QA.';
        }

        $noteExtra    = trim($data['catatan_review'] ?? '');
        $catatanFinal = trim($infoDoc . ' ' . $infoReturn . ' ' . $noteExtra);

        $update = [
            'status_review'  => 'hold',
            'tgl_review'     => now()->toDateString(),
            'catatan_review' => $catatanFinal,
        ];

        // balik ke Job Sheet QC
        if (in_array($data['return_to'], ['jobsheet', 'both'], true)) {
            $update['status_jobsheet'] = 'pending';
            // tanggal jobsheet tetap, tidak perlu di-null
        }

        // balik ke COA QC/QA
        if (in_array($data['return_to'], ['coa', 'both'], true)) {
            $update['status_coa']        = 'pending';
            $update['tgl_qa_terima_coa'] = null; // QA dianggap belum terima COA lagi
        }

        $batch->update($update);

        return back()->with('ok', 'Batch di-HOLD dan dikembalikan ke modul terkait.');
    }

    /**
     * RELEASE:
     * - status_review = released
     * - setelah itu diarahkan ke halaman Release (release.index)
     */
    public function release(Request $request, ProduksiBatch $batch)
    {
        $catatan = $request->input('catatan_review')
            ?: 'Released oleh QA pada ' . now()->format('d-m-Y');

        $batch->update([
            'status_review'  => 'released',
            'tgl_review'     => now()->toDateString(),
            'catatan_review' => $catatan,
        ]);

        // Langsung pindah ke halaman Release After Secondary Pack
        return redirect()
            ->route('release.index', [
                'bulan' => $batch->bulan,
                'tahun' => $batch->tahun,
            ])
            ->with('ok', 'Batch berhasil di-RELEASE dan masuk ke halaman Release.');
    }

    /**
     * REJECT:
     * - status_review = rejected
     * - wajib ada catatan_review
     */
    public function reject(Request $request, ProduksiBatch $batch)
    {
        $request->validate([
            'catatan_review' => ['required', 'string'],
        ]);

        $batch->update([
            'status_review'  => 'rejected',
            'tgl_review'     => now()->toDateString(),
            'catatan_review' => $request->input('catatan_review'),
        ]);

        return back()->with('ok', 'Batch berhasil di-REJECT.');
    }
}
