<?php

namespace App\Http\Controllers;

use App\Models\Produksi;
use Illuminate\Http\Request;

class ProduksiController extends Controller
{
    /* ================== INDEX ================== */
    public function index(Request $request)
    {
        $q       = $request->get('q', '');
        $perPage = (int) $request->get('per_page', 15);
        if ($perPage <= 0) $perPage = 15;

        $rows = Produksi::query()
            ->when($q, function ($qBuilder) use ($q) {
                $qBuilder->where(function ($sub) use ($q) {
                    $sub->where('kode_produk', 'like', "%{$q}%")
                        ->orWhere('nama_produk', 'like', "%{$q}%")
                        ->orWhere('bentuk_sediaan', 'like', "%{$q}%")
                        ->orWhere('tipe_alur', 'like', "%{$q}%");
                });
            })
            ->orderBy('nama_produk')
            ->paginate($perPage);

        return view('produksi.index', compact('rows', 'q'));
    }

    /* ================== CREATE ================== */
    public function create()
    {
        $isEdit = false;
        $produk = new Produksi();

        $bentukOptions = $this->bentukOptions();
        $tipeAlurOptions = $this->tipeAlurOptions();

        return view('produksi.form', compact(
            'isEdit',
            'produk',
            'bentukOptions',
            'tipeAlurOptions'
        ));
    }

    /* ================== STORE ================== */
    public function store(Request $request)
    {
        $data = $request->validate([
            'kode_produk'     => 'required|string|max:50|unique:produksi,kode_produk',
            'nama_produk'     => 'required|string|max:150',
            'bentuk_sediaan'  => 'required|string|max:50',
            'tipe_alur'       => 'required|string|max:50',
            'leadtime_target' => 'nullable|integer|min:0',
            'is_aktif'        => 'nullable|boolean',
        ]);

        // Checkbox aktif
        $data['is_aktif'] = $request->has('is_aktif');

        Produksi::create($data);

        return redirect()
            ->route('produksi.index')
            ->with('ok', 'Produk produksi berhasil ditambahkan.');
    }

    /* ================== EDIT ================== */
    public function edit(Produksi $produksi)
    {
        $isEdit = true;
        $produk = $produksi;

        $bentukOptions   = $this->bentukOptions();
        $tipeAlurOptions = $this->tipeAlurOptions();

        return view('produksi.form', compact(
            'isEdit',
            'produk',
            'bentukOptions',
            'tipeAlurOptions'
        ));
    }

    /* ================== UPDATE ================== */
    public function update(Request $request, Produksi $produksi)
    {
        $data = $request->validate([
            'kode_produk'     => 'required|string|max:50|unique:produksi,kode_produk,' . $produksi->id,
            'nama_produk'     => 'required|string|max:150',
            'bentuk_sediaan'  => 'required|string|max:50',
            'tipe_alur'       => 'required|string|max:50',
            'leadtime_target' => 'nullable|integer|min:0',
            'is_aktif'        => 'nullable|boolean',
        ]);

        $data['is_aktif'] = $request->has('is_aktif');

        $produksi->update($data);

        return redirect()
            ->route('produksi.index')
            ->with('ok', 'Produk produksi berhasil diupdate.');
    }

    /* ================== DESTROY ================== */
    public function destroy(Produksi $produksi)
    {
        $produksi->delete();

        return redirect()
            ->route('produksi.index')
            ->with('ok', 'Produk produksi berhasil dihapus.');
    }

    /* ========== Helper pilihan dropdown ========== */

    private function bentukOptions(): array
    {
        return [
            'Tablet Film Coating',
            'Tablet Salut Gula',
            'Kapsul',
            'Dry Syrup',
            'Tablet Non Salut',
            'CLO',
            'Obat Luar',
        ];
    }

   private function tipeAlurOptions(): array
{
    return [
        'TABLET_NON_SALUT' => 'Tablet / Kaplet Non Salut',
        'TABLET_SALUT'     => 'Tablet / Kaplet Salut / Coating',
        'DRY_SYRUP'        => 'Dry Syrup',
        'CLO'              => 'CLO / Softcaps',
        'CAIRAN_LUAR'      => 'Cairan / Obat Luar',
        'KAPSUL'           => 'Kapsul',
    ];
}

}
