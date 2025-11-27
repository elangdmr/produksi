<?php

namespace App\Http\Controllers;

use App\Models\ProduksiBatch;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReleaseController extends Controller
{
    /**
     * INDEX
     * Menampilkan batch yang sudah di-Release QA (status_review = 'released').
     */
    public function index(Request $request)
    {
        $q     = $request->get('q', '');
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');

        $rows = ProduksiBatch::with('produksi')
            ->where('status_review', 'released')

            // search
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function ($s) use ($q) {
                    $s->where('nama_produk', 'like', "%{$q}%")
                      ->orWhere('kode_batch', 'like', "%{$q}%")
                      ->orWhere('no_batch', 'like', "%{$q}%");
                });
            })

            // filter bulan & tahun
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
            ->paginate(25)
            ->withQueryString();

        return view('release.index', compact('rows', 'q', 'bulan', 'tahun'));
    }

    /**
     * PRINT FORM PENYERAHAN PRODUKSI.
     */
    public function print(Request $request)
    {
        $q     = $request->get('q', '');
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');

        $rows = ProduksiBatch::with('produksi')
            ->where('status_review', 'released')
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function ($s) use ($q) {
                    $s->where('nama_produk', 'like', "%{$q}%")
                      ->orWhere('kode_batch', 'like', "%{$q}%")
                      ->orWhere('no_batch', 'like', "%{$q}%");
                });
            })
            ->when($bulan !== null && $bulan !== '', function ($qb) use ($bulan) {
                $qb->where('bulan', (int) $bulan);
            })
            ->when($tahun !== null && $tahun !== '', function ($qb) use ($tahun) {
                $qb->where('tahun', (int) $tahun);
            })
            ->orderBy('nama_produk')
            ->orderBy('no_batch')
            ->get();

        return view('release.print', compact('rows', 'bulan', 'tahun'));
    }

    /**
     * LOGSHEET: tampilan lebar (Weighing s/d Secondary Pack + Job Sheet, Sampling, COA).
     */
    public function logsheet(Request $request)
    {
        $q     = $request->get('q', '');
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');

        $rows = ProduksiBatch::with('produksi')
            ->where('status_review', 'released')
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function ($s) use ($q) {
                    $s->where('nama_produk', 'like', "%{$q}%")
                      ->orWhere('kode_batch', 'like', "%{$q}%")
                      ->orWhere('no_batch', 'like', "%{$q}%");
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
            ->get();

        return view('release.logsheet', compact('rows', 'q', 'bulan', 'tahun'));
    }

    /**
     * EXPORT LOGSHEET KE CSV.
     */
    public function exportCsv(Request $request)
    {
        $q     = $request->get('q', '');
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');

        $rows = ProduksiBatch::with('produksi')
            ->where('status_review', 'released')
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function ($s) use ($q) {
                    $s->where('nama_produk', 'like', "%{$q}%")
                      ->orWhere('kode_batch', 'like', "%{$q}%")
                      ->orWhere('no_batch', 'like', "%{$q}%");
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
            ->get();

        $filename = 'logsheet_release_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');

            // header CSV
            fputcsv($out, [
                'Produk','No Batch','Kode Batch','Batch','Month',

                'Weighing Mulai','Weighing Selesai',
                'Mixing Mulai','Mixing Selesai',
                'Tgl Rilis Granul',
                'Capsule Mulai','Capsule Selesai',
                'Tableting Mulai','Tableting Selesai',
                'Tgl Rilis Tablet',
                'Coating Mulai','Coating Selesai',
                'Tgl Rilis Ruahan',
                'Primary Mulai','Primary Selesai',
                'Tgl Rilis Ruahan Akhir',
                'Secondary Mulai','Secondary Selesai',

                'Konfirmasi Produksi',
                'Terima Job Sheet',
                'Tanggal Sampling',
                'Tgl QC Kirim COA',
                'Tgl QA Terima COA',
                'Status Review',
                'Catatan Review',
            ]);

            $fmt = function ($val) {
                return $val ? Carbon::parse($val)->format('d-M-Y') : '';
            };

            foreach ($rows as $row) {
                fputcsv($out, [
                    $row->produksi->nama_produk ?? $row->nama_produk,
                    $row->no_batch,
                    $row->kode_batch,
                    $row->batch,
                    $row->bulan,

                    $fmt($row->tgl_mulai_weighing),
                    $fmt($row->tgl_weighing),

                    $fmt($row->tgl_mulai_mixing),
                    $fmt($row->tgl_mixing),

                    $fmt($row->tgl_rilis_granul),

                    $fmt($row->tgl_mulai_capsule_filling),
                    $fmt($row->tgl_capsule_filling),

                    $fmt($row->tgl_mulai_tableting),
                    $fmt($row->tgl_tableting),

                    $fmt($row->tgl_rilis_tablet),

                    $fmt($row->tgl_mulai_coating),
                    $fmt($row->tgl_coating),

                    $fmt($row->tgl_rilis_ruahan),

                    $fmt($row->tgl_mulai_primary_pack),
                    $fmt($row->tgl_primary_pack),

                    $fmt($row->tgl_rilis_ruahan_akhir),

                    $fmt($row->tgl_mulai_secondary_pack_1),
                    $fmt($row->tgl_secondary_pack_1),

                    // Job Sheet, Sampling, COA, Review
                    $fmt($row->tgl_konfirmasi_produksi),
                    $fmt($row->tgl_terima_jobsheet),
                    $fmt($row->tgl_sampling),
                    $fmt($row->tgl_qc_kirim_coa),
                    $fmt($row->tgl_qa_terima_coa),
                    $row->status_review,
                    $row->catatan_review,
                ]);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
