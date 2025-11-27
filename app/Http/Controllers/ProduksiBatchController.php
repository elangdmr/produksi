<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Produksi;
use App\Models\ProduksiBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// NOTE: butuh package ini:
// composer require phpoffice/phpspreadsheet
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Carbon\Carbon;

class ProduksiBatchController extends Controller
{
    /* =========================================================
     * LIST + FORM UPLOAD (pengganti Permintaan Bahan)
     * =======================================================*/
    public function index(Request $request)
    {
        $q       = $request->get('q', '');
        $bulan   = $request->get('bulan');              // filter optional
        $tahun   = $request->get('tahun');              // filter optional
        $perPage = (int) $request->get('per_page', 25);
        if ($perPage <= 0) {
            $perPage = 25;
        }

        $rows = ProduksiBatch::with('produksi')
            // search: nama produk / no batch / kode batch
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function ($sub) use ($q) {
                    $sub->where('nama_produk', 'like', "%{$q}%")
                        ->orWhere('no_batch', 'like', "%{$q}%")
                        ->orWhere('kode_batch', 'like', "%{$q}%");
                });
            })
            // filter bulan (kalau diisi)
            ->when($bulan !== null && $bulan !== '', function ($qb) use ($bulan) {
                $qb->where('bulan', (int) $bulan);
            })
            // filter tahun (kalau diisi)
            ->when($tahun !== null && $tahun !== '', function ($qb) use ($tahun) {
                $qb->where('tahun', (int) $tahun);
            })
            // urutkan utama: tahun, bulan, WO date
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->orderBy('wo_date')
            ->orderBy('id')
            ->paginate($perPage);

        return view('produksi_batches.index', compact(
            'rows',
            'q',
            'perPage',
            'bulan',
            'tahun'
        ));
    }

    /* =========================================================
     * PROSES UPLOAD EXCEL (sekaligus set tanggal Weighing)
     * =======================================================*/
    public function upload(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xls,xlsx'],
        ]);

        $file = $request->file('file');

        DB::beginTransaction();
        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet       = $spreadsheet->getActiveSheet();
            $highestRow  = $sheet->getHighestRow();

            // Mapping kolom Excel (format WO baru):
            // A = WO No         -> no_batch + kode_batch
            // B = Description   -> nama produksi (disinkron ke master)
            // C = WO Date       -> wo_date (+ default tgl weighing)
            // D = Expected Date -> expected_date

            for ($row = 2; $row <= $highestRow; $row++) {
                $noBatch = trim((string) $sheet->getCell('A' . $row)->getValue());
                if ($noBatch === '') {
                    // baris kosong, skip
                    continue;
                }

                $namaExcel   = trim((string) $sheet->getCell('B' . $row)->getValue());
                $woDateRaw   = $sheet->getCell('C' . $row)->getValue();
                $expectedRaw = $sheet->getCell('D' . $row)->getValue();

                $woDate       = $this->parseExcelDate($woDateRaw);
                $expectedDate = $this->parseExcelDate($expectedRaw);

                // Hitung bulan & tahun dari WO Date (kalau ada)
                $bulan = null;
                $tahun = null;
                if ($woDate) {
                    $bulan = (int) date('n', strtotime($woDate));
                    $tahun = (int) date('Y', strtotime($woDate));
                }

                // Coba sinkron nama dengan master "produksi"
                $master = Produksi::where('nama_produk', $namaExcel)->first();

                $namaSinkron = $master ? $master->nama_produk : $namaExcel;
                $tipeAlur    = $master ? $master->tipe_alur    : null;
                $produksiId  = $master ? $master->id          : null;

                // Cek apakah batch sudah ada
                $batch = ProduksiBatch::where('no_batch', $noBatch)
                    ->where('kode_batch', $noBatch)
                    ->first();

                if (!$batch) {
                    // ============ BATCH BARU ============
                    ProduksiBatch::create([
                        'no_batch'   => $noBatch,
                        'kode_batch' => $noBatch,

                        'nama_produk'          => $namaSinkron,
                        'produksi_id'          => $produksiId,
                        'batch_ke'             => 1,
                        'bulan'                => $bulan,
                        'tahun'                => $tahun,
                        'tipe_alur'            => $tipeAlur,

                        'wo_date'              => $woDate,
                        'expected_date'        => $expectedDate,

                        // default awal: WO Date = mulai & selesai Weighing
                        'tgl_mulai_weighing'   => $woDate,
                        'tgl_weighing'         => $woDate,

                        // proses lain default null
                        'tgl_mulai_mixing'           => null,
                        'tgl_mixing'                 => null,
                        'tgl_mulai_capsule_filling'  => null,
                        'tgl_capsule_filling'        => null,
                        'tgl_mulai_tableting'        => null,
                        'tgl_tableting'              => null,
                        'tgl_mulai_coating'          => null,
                        'tgl_coating'                => null,
                        'tgl_mulai_primary_pack'     => null,
                        'tgl_primary_pack'           => null,
                        'tgl_mulai_secondary_pack_1' => null,
                        'tgl_secondary_pack_1'       => null,
                        'tgl_mulai_secondary_pack_2' => null,
                        'tgl_secondary_pack_2'       => null,

                        // QC detail default null (nanti diisi QC Release)
                        'tgl_datang_granul'        => null,
                        'tgl_analisa_granul'       => null,
                        'tgl_rilis_granul'         => null,
                        'tgl_datang_tablet'        => null,
                        'tgl_analisa_tablet'       => null,
                        'tgl_rilis_tablet'         => null,
                        'tgl_datang_ruahan'        => null,
                        'tgl_analisa_ruahan'       => null,
                        'tgl_rilis_ruahan'         => null,
                        'tgl_datang_ruahan_akhir'  => null,
                        'tgl_analisa_ruahan_akhir' => null,
                        'tgl_rilis_ruahan_akhir'   => null,

                        'hari_kerja'           => null,
                        'status_proses'        => null,
                    ]);
                } else {
                    // ============ BATCH SUDAH ADA ============
                    // Update header saja, jangan reset tanggal proses yang sudah diisi.
                    $batch->update([
                        'nama_produk'   => $namaSinkron,
                        'produksi_id'   => $produksiId,
                        'bulan'         => $bulan,
                        'tahun'         => $tahun,
                        'tipe_alur'     => $tipeAlur,
                        'wo_date'       => $woDate,
                        'expected_date' => $expectedDate,
                        // opsional: kalau mau, bisa update weighing kalau masih null
                        'tgl_mulai_weighing' => $batch->tgl_mulai_weighing ?: $woDate,
                        'tgl_weighing'       => $batch->tgl_weighing       ?: $woDate,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('show-permintaan')
                ->with('ok', 'File jadwal produksi berhasil diimport.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()
                ->withErrors(['file' => 'Gagal memproses file: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /* =========================================================
     * EDIT TANGGAL-TANGGAL PER BATCH
     * =======================================================*/
    public function edit(ProduksiBatch $batch)
    {
        // Form untuk mengedit tanggal-tanggal proses & QC
        return view('produksi_batches.edit', compact('batch'));
    }

    public function update(Request $request, ProduksiBatch $batch)
    {
        $data = $request->validate([
            'wo_date'              => ['nullable', 'date'],
            'expected_date'        => ['nullable', 'date'],

            'tgl_mulai_weighing'   => ['nullable', 'date'],
            'tgl_weighing'         => ['nullable', 'date'],

            'tgl_mulai_mixing'     => ['nullable', 'date'],
            'tgl_mixing'           => ['nullable', 'date'],

            'tgl_mulai_capsule_filling' => ['nullable', 'date'],
            'tgl_capsule_filling'       => ['nullable', 'date'],

            'tgl_mulai_tableting'  => ['nullable', 'date'],
            'tgl_tableting'        => ['nullable', 'date'],

            'tgl_mulai_coating'    => ['nullable', 'date'],
            'tgl_coating'          => ['nullable', 'date'],

            'tgl_mulai_primary_pack'    => ['nullable', 'date'],
            'tgl_primary_pack'          => ['nullable', 'date'],

            'tgl_mulai_secondary_pack_1'=> ['nullable', 'date'],
            'tgl_secondary_pack_1'      => ['nullable', 'date'],
            'tgl_mulai_secondary_pack_2'=> ['nullable', 'date'],
            'tgl_secondary_pack_2'      => ['nullable', 'date'],

            // QC dates (detail) – PASTIIN namanya sama dengan kolom DB!
            'tgl_datang_granul'        => ['nullable', 'date'],
            'tgl_analisa_granul'       => ['nullable', 'date'],
            'tgl_rilis_granul'         => ['nullable', 'date'],

            'tgl_datang_tablet'        => ['nullable', 'date'],
            'tgl_analisa_tablet'       => ['nullable', 'date'],
            'tgl_rilis_tablet'         => ['nullable', 'date'],

            'tgl_datang_ruahan'        => ['nullable', 'date'],
            'tgl_analisa_ruahan'       => ['nullable', 'date'],
            'tgl_rilis_ruahan'         => ['nullable', 'date'],

            'tgl_datang_ruahan_akhir'  => ['nullable', 'date'],
            'tgl_analisa_ruahan_akhir' => ['nullable', 'date'],
            'tgl_rilis_ruahan_akhir'   => ['nullable', 'date'],

            'hari_kerja'           => ['nullable', 'integer', 'min:0'],
            'status_proses'        => ['nullable', 'string', 'max:50'],
        ]);

        $batch->update($data);

        return redirect()
            ->route('show-permintaan')
            ->with('ok', 'Data batch produksi diperbarui.');
    }

    /* =========================================================
     * HELPER PARSE TANGGAL EXCEL
     * =======================================================*/
    private function parseExcelDate($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Jika numeric => format date serial Excel
        if (is_numeric($value)) {
            try {
                $dt = ExcelDate::excelToDateTimeObject($value);
                return $dt->format('Y-m-d');
            } catch (\Throwable $e) {
                return null;
            }
        }

        // Kalau sudah berupa string tanggal
        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }
}
