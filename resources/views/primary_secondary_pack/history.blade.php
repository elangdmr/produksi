@extends('layouts.app')

@section('content')
<section class="app-user-list">
  <div class="row" id="basic-table">
    <div class="col-12">
      <div class="card">

        {{-- Header --}}
        <div class="card-header d-flex justify-content-between align-items-center">
          <div>
            <h4 class="card-title mb-0">Riwayat Primary &amp; Secondary Pack</h4>
            <p class="mb-0 text-muted">
              Daftar batch yang sudah menyelesaikan proses Secondary Pack.
            </p>
          </div>

          <div>
            <a href="{{ route('primary-secondary.index') }}"
               class="btn btn-sm btn-outline-secondary">
              &laquo; Kembali ke Input Primary &amp; Secondary
            </a>
          </div>
        </div>

        {{-- Filter --}}
        <div class="card-body border-bottom">
          <form class="row g-1" method="GET" action="{{ route('primary-secondary.history') }}">
            <div class="col-md-3">
              <input type="text"
                     name="q"
                     value="{{ $q }}"
                     class="form-control"
                     placeholder="Cari produk / no batch / kode batch...">
            </div>

            <div class="col-md-2">
              <select name="bulan" class="form-control">
                <option value="">Semua Bulan</option>
                @for($i = 1; $i <= 12; $i++)
                  <option value="{{ $i }}" {{ (isset($bulan) && (int)$bulan === $i) ? 'selected' : '' }}>
                    {{ sprintf('%02d', $i) }}
                  </option>
                @endfor
              </select>
            </div>

            <div class="col-md-2">
              <input type="number"
                     name="tahun"
                     value="{{ $tahun }}"
                     class="form-control"
                     placeholder="Tahun">
            </div>

            <div class="col-md-2">
              <button class="btn btn-outline-primary w-100">Filter</button>
            </div>
          </form>
        </div>

        {{-- Tabel --}}
        <div class="table-responsive">
          <table class="table mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>Kode Batch</th>
                <th>Nama Produk</th>
                <th>Bulan</th>
                <th>Tahun</th>
                <th>WO Date</th>

                <th>Primary Pack (Mulai)</th>
                <th>Primary Pack (Selesai)</th>
                <th>Secondary Pack (Mulai)</th>
                <th>Secondary Pack (Selesai)</th>
              </tr>
            </thead>
            <tbody>
              @forelse($rows as $idx => $row)
                <tr>
                  <td>{{ $rows->firstItem() + $idx }}</td>
                  <td>{{ $row->kode_batch }}</td>
                  <td>{{ $row->produksi->nama_produk ?? $row->nama_produk }}</td>
                  <td>{{ $row->bulan }}</td>
                  <td>{{ $row->tahun }}</td>
                  <td>{{ optional($row->wo_date)->format('d-m-Y') }}</td>

                  <td>{{ optional($row->tgl_mulai_primary_pack)->format('d-m-Y') }}</td>
                  <td>{{ optional($row->tgl_primary_pack)->format('d-m-Y') }}</td>
                  <td>{{ optional($row->tgl_mulai_secondary_pack_1)->format('d-m-Y') }}</td>
                  <td>{{ optional($row->tgl_secondary_pack_1)->format('d-m-Y') }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="10" class="text-center text-muted">
                    Belum ada riwayat Primary &amp; Secondary Pack.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="card-body">
          {{ $rows->withQueryString()->links() }}
        </div>

      </div>
    </div>
  </div>
</section>
@endsection
