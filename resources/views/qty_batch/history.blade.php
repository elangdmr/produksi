@extends('layouts.app')

@section('content')
<section class="app-user-list">
  <div class="row" id="basic-table">
    <div class="col-12">
      <div class="card">

        {{-- HEADER --}}
        <div class="card-header d-flex justify-content-between align-items-center">
          <div>
            <h4 class="card-title mb-0">Riwayat Qty Batch</h4>
            <p class="mb-0 text-muted">
              Menampilkan Qty Batch yang sudah <strong>dikonfirmasi</strong>.
              Data pada halaman ini bersifat historis dan tidak bisa diubah dari sini.
            </p>
          </div>

          <a href="{{ route('qty-batch.index') }}"
             class="btn btn-sm btn-outline-secondary">
            &laquo; Kembali ke Data Aktif
          </a>
        </div>

        {{-- FILTER BAR --}}
        <div class="card-body border-bottom">
          <form method="GET" class="row g-1">

            {{-- search --}}
            <div class="col-md-3">
              <input type="text"
                     name="q"
                     class="form-control"
                     placeholder="Cari produk / no batch / kode batch..."
                     value="{{ $q ?? '' }}">
            </div>

            {{-- bulan --}}
            <div class="col-md-2">
              <select name="bulan" class="form-control">
                <option value="">Semua Bulan</option>
                @for($m = 1; $m <= 12; $m++)
                  @php $val = (string) $m; @endphp
                  <option value="{{ $val }}"
                    {{ (string)($bulan ?? '') === $val ? 'selected' : '' }}>
                    {{ str_pad($m, 2, '0', STR_PAD_LEFT) }}
                  </option>
                @endfor
              </select>
            </div>

            {{-- tahun --}}
            <div class="col-md-2">
              <input type="number"
                     name="tahun"
                     class="form-control"
                     placeholder="Tahun"
                     value="{{ $tahun ?? '' }}">
            </div>

            <div class="col-md-2">
              <button class="btn btn-outline-primary w-100">
                Filter
              </button>
            </div>
          </form>
        </div>

        {{-- TABEL RIWAYAT --}}
        <div class="table-responsive">
          <table class="table table-striped mb-0 align-middle">
            <thead>
              <tr>
                <th style="width: 40px;">#</th>
                <th>Kode Batch</th>
                <th>Nama Produk</th>
                <th>Bulan</th>
                <th>Tahun</th>
                <th>WO Date</th>
                <th>Secondary Pack (Mulai)</th>
                <th>Secondary Pack (Selesai)</th>
                <th class="text-end">Qty Batch</th>
                <th>Status</th>
                <th>Tgl Konfirmasi Produksi</th>
              </tr>
            </thead>

            <tbody>
            @forelse($rows as $idx => $row)
              <tr>
                {{-- nomor --}}
                <td>{{ $rows->firstItem() + $idx }}</td>

                {{-- kode & nama --}}
                <td>{{ $row->kode_batch }}</td>
                <td>{{ $row->produksi->nama_produk ?? $row->nama_produk }}</td>

                {{-- bulan & tahun --}}
                <td>{{ $row->bulan }}</td>
                <td>{{ $row->tahun }}</td>

                {{-- tanggal --}}
                <td>{{ $row->wo_date ? $row->wo_date->format('d-m-Y') : '-' }}</td>
                <td>{{ $row->tgl_mulai_secondary_pack_1 ? $row->tgl_mulai_secondary_pack_1->format('d-m-Y') : '-' }}</td>
                <td>{{ $row->tgl_secondary_pack_1 ? $row->tgl_secondary_pack_1->format('d-m-Y') : '-' }}</td>

                {{-- qty --}}
                <td class="text-end">
                  {{ number_format((float) $row->qty_batch, 2) }}
                </td>

                {{-- status selalu dikonfirmasi --}}
                <td>
                  <span class="badge badge-light-success">Dikonfirmasi</span>
                </td>

                <td>
                  {{ $row->tgl_konfirmasi_produksi
                        ? $row->tgl_konfirmasi_produksi->format('d-m-Y')
                        : '-' }}
                </td>
              </tr>

            @empty
              <tr>
                <td colspan="11" class="text-center text-muted">
                  Belum ada riwayat Qty Batch yang dikonfirmasi.
                </td>
              </tr>
            @endforelse
            </tbody>
          </table>
        </div>

        {{-- PAGINATION --}}
        <div class="card-body">
          {{ $rows->withQueryString()->links() }}
        </div>

      </div>
    </div>
  </div>
</section>
@endsection
