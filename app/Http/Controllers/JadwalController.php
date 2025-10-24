<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * Controller ini sekarang dipakai untuk Permintaan Bahan Baku.
 * Nama class dibiarkan "JadwalController" agar menu/route lama tidak patah.
 */
class JadwalController extends Controller
{
    /** @var string nama tabel permintaan (ubah jika perlu) */
    protected string $permintaanTable = 'permintaan_bahan';

    /** @var string|null nama tabel master bahan (null jika tidak ada) */
    protected ?string $bahanTable = 'bahans'; // set ke null jika tidak punya tabel bahan

    /** Opsi dropdown sederhana (boleh sesuaikan dari DB kalau ada) */
    protected array $satuans = ['gr','kg','ml','L','pcs'];
    protected array $kategoriOpts = ['Bahan Aktif','Bahan Penolong','Kemasan','Lainnya'];

    /**
     * INDEX — daftar permintaan + modal tambah
     * (Alias lama: route('show-jadwal-belajar'))
     */
    public function showJadwal()
    {
        $q = DB::table($this->permintaanTable)->orderByDesc('created_at');

        // join tabel bahan bila tersedia
        if (!empty($this->bahanTable)) {
            $q->leftJoin($this->bahanTable, "{$this->bahanTable}.id", '=', "{$this->permintaanTable}.bahan_id")
              ->addSelect("{$this->permintaanTable}.*", "{$this->bahanTable}.nama as bahan_nama");
        } else {
            $q->select("{$this->permintaanTable}.*");
        }

        $requests = $q->paginate(10);

        // list bahan untuk select (kalau tabel bahan ada)
        $bahans = [];
        if (!empty($this->bahanTable)) {
            $bahans = DB::table($this->bahanTable)->orderBy('nama')->get();
        }

        // kirim ke view "show_permintaan" (pakai layout baru)
        return view('permintaan_bahan.show_permintaan', [
            'requests'     => $requests,
            'bahans'       => $bahans,
            'satuans'      => $this->satuans,
            'kategoriOpts' => $this->kategoriOpts,
        ]);
    }

    /**
     * STORE — simpan permintaan dari modal di halaman index.
     * (Alias lama: POST /input-jadwal, tapi sekarang rute baru juga ada)
     */
    public function store(Request $request)
    {
        $request->validate([
            'bahan_id'          => ['nullable', 'integer'],
            'jumlah'            => ['required', 'numeric', 'min:0'],
            'satuan'            => ['required', 'string', 'max:20'],
            'kategori'          => ['required', 'string', 'max:50'],
            'tanggal_kebutuhan' => ['required', 'date'],
            'alasan'            => ['nullable', 'string', 'max:1000'],
            'status'            => ['nullable', 'in:Pending,Approved,Rejected'],
        ]);

        DB::table($this->permintaanTable)->insert([
            'bahan_id'          => $request->bahan_id,
            'jumlah'            => $request->jumlah,
            'satuan'            => $request->satuan,
            'kategori'          => $request->kategori,
            'tanggal_kebutuhan' => $request->tanggal_kebutuhan,
            'alasan'            => $request->alasan,
            'status'            => $request->input('status', 'Pending'),
            'user_id'           => Auth::id(),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        // arahkan ke index permintaan (alias route lama tetap bekerja)
        return redirect()->route('show-permintaan')->with('success', 'Permintaan berhasil diajukan.');
    }

    /**
     * EDIT — tampilkan form edit permintaan.
     * (Alias lama: route('edit-jadwal'))
     */
    public function editJadwal($id)
    {
        $q = DB::table($this->permintaanTable)->where("{$this->permintaanTable}.id", $id);

        if (!empty($this->bahanTable)) {
            $q->leftJoin($this->bahanTable, "{$this->bahanTable}.id", '=', "{$this->permintaanTable}.bahan_id")
              ->addSelect("{$this->permintaanTable}.*", "{$this->bahanTable}.nama as bahan_nama");
        } else {
            $q->select("{$this->permintaanTable}.*");
        }

        $permintaan = $q->firstOrFail();

        $bahans = [];
        if (!empty($this->bahanTable)) {
            $bahans = DB::table($this->bahanTable)->orderBy('nama')->get();
        }

        return view('permintaan_bahan.edit_permintaan', [
            'permintaan'   => $permintaan,
            'bahans'       => $bahans,
            'satuans'      => $this->satuans,
            'kategoriOpts' => $this->kategoriOpts,
        ]);
    }

    /**
     * UPDATE — simpan perubahan.
     * (Alias lama: route('update-jadwal') yang dulu punya parameter tambahan, sekarang diabaikan)
     */
    public function updateJadwal(Request $request, $id /* , $kode_kelas = null */)
    {
        $request->validate([
            'bahan_id'          => ['nullable', 'integer'],
            'jumlah'            => ['required', 'numeric', 'min:0'],
            'satuan'            => ['required', 'string', 'max:20'],
            'kategori'          => ['required', 'string', 'max:50'],
            'tanggal_kebutuhan' => ['required', 'date'],
            'alasan'            => ['nullable', 'string', 'max:1000'],
            'status'            => ['required', 'in:Pending,Approved,Rejected'],
        ]);

        DB::table($this->permintaanTable)
            ->where('id', $id)
            ->update([
                'bahan_id'          => $request->bahan_id,
                'jumlah'            => $request->jumlah,
                'satuan'            => $request->satuan,
                'kategori'          => $request->kategori,
                'tanggal_kebutuhan' => $request->tanggal_kebutuhan,
                'alasan'            => $request->alasan,
                'status'            => $request->status,
                'updated_at'        => now(),
            ]);

        return redirect()->route('show-permintaan')->with('success', 'Permintaan diperbarui.');
    }

    /**
     * DESTROY — hapus data.
     * (Alias lama: route('delete-jadwal'))
     */
    public function destroy($id)
    {
        DB::table($this->permintaanTable)->where('id', $id)->delete();
        return redirect()->route('show-permintaan')->with('success', 'Permintaan dihapus.');
    }

    /* =======================
     * METHOD LAMA (optional):
     * showJadwalMengajar / showJadwalGuru / detailJadwal / inputJadwal
     * Kalau masih dipakai menu lain, bisa ditambahkan ulang di sini.
     * Untuk sekarang DIKOSONGKAN supaya tidak bentrok.
     * ======================= */

    // public function detailJadwal($id) { return redirect()->route('show-permintaan'); }
    // public function inputJadwal()     { return redirect()->route('show-permintaan'); }
    // public function showJadwalMengajar() { abort(404); }
    // public function showJadwalGuru(Request $r) { abort(404); }
}
