@extends('layouts.app')

@section('content')
<section class="app-user">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <div>
        <h4 class="card-title mb-0">Riwayat QC Release</h4>
        <p class="mb-0 text-muted">
          Daftar batch yang sudah dikonfirmasi QC RELEASED.
        </p>
      </div>

      <div>
        <a href="{{ route('qc-release.index') }}"
           class="btn btn-sm btn-outline-secondary">
          &laquo; Kembali ke QC Release
        </a>
      </div>
    </div>

    <div class="card-body">

      {{-- Filter --}}
      <form method="GET" action="{{ route('qc-release.history') }}" class="row g-2 mb-3">
        <div class="col-md-4">
          <input type="text"
                 name="q"
                 class="form-control"
                 placeholder="Cari produk / no batch / kode batch"
                 value="{{ $search ?? request('q') }}">
        </div>

        <div class="col-md-3">
          @php
            $currentBulan = $bulan ?? request('bulan', 'all');
          @endphp
          <select name="bulan" class="form-control">
            <option value="all" {{ $currentBulan === 'all' || $currentBulan === null ? 'selected' : '' }}>
              Semua Bulan
            </option>
            @for($m = 1; $m <= 12; $m++)
              @php $val = (string) $m; @endphp
              <option value="{{ $val }}" {{ (string)$currentBulan === $val ? 'selected' : '' }}>
                {{ str_pad($m, 2, '0', STR_PAD_LEFT) }}
              </option>
            @endfor
          </select>
        </div>

        <div class="col-md-2">
          <input type="number"
                 name="tahun"
                 class="form-control"
                 placeholder="Tahun"
                 value="{{ $tahun ?? request('tahun') }}">
        </div>

        <div class="col-md-3">
          <button class="btn btn-primary w-100">Filter</button>
        </div>
      </form>

      {{-- Tabel QC Release History --}}
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead>
            <tr>
              <th>#</th>
              <th>Produk</th>
              <th>No Batch</th>
              <th>Kode Batch</th>
              <th>Bulan</th>
              <th>Tahun</th>
              <th>WO Date</th>
              <th>Status Proses</th>

              <th class="text-center bg-light">Granul - Datang</th>
              <th class="text-center bg-light">Granul - Analisa</th>
              <th class="text-center bg-light">Granul - Release</th>

              <th class="text-center bg-light">Tablet - Datang</th>
              <th class="text-center bg-light">Tablet - Analisa</th>
              <th class="text-center bg-light">Tablet - Release</th>

              <th class="text-center bg-light">Ruahan - Datang</th>
              <th class="text-center bg-light">Ruahan - Analisa</th>
              <th class="text-center bg-light">Ruahan - Release</th>

              <th class="text-center bg-light">Ruahan Akhir - Datang</th>
              <th class="text-center bg-light">Ruahan Akhir - Analisa</th>
              <th class="text-center bg-light">Ruahan Akhir - Release</th>
            </tr>
          </thead>

          <tbody>
          @forelse($batches as $index => $batch)
            <tr>
              <td>{{ $batches->firstItem() + $index }}</td>
              <td>{{ $batch->produksi->nama_produk ?? $batch->nama_produk }}</td>
              <td>{{ $batch->no_batch }}</td>
              <td>{{ $batch->kode_batch }}</td>
              <td>{{ $batch->bulan }}</td>
              <td>{{ $batch->tahun }}</td>
              <td>{{ optional($batch->wo_date)->format('d-m-Y') }}</td>
              <td>{{ $batch->status_proses }}</td>

              <td>{{ optional($batch->tgl_datang_granul)->format('d-m-Y') }}</td>
              <td>{{ optional($batch->tgl_analisa_granul)->format('d-m-Y') }}</td>
              <td>{{ optional($batch->tgl_rilis_granul)->format('d-m-Y') }}</td>

              <td>{{ optional($batch->tgl_datang_tablet)->format('d-m-Y') }}</td>
              <td>{{ optional($batch->tgl_analisa_tablet)->format('d-m-Y') }}</td>
              <td>{{ optional($batch->tgl_rilis_tablet)->format('d-m-Y') }}</td>

              <td>{{ optional($batch->tgl_datang_ruahan)->format('d-m-Y') }}</td>
              <td>{{ optional($batch->tgl_analisa_ruahan)->format('d-m-Y') }}</td>
              <td>{{ optional($batch->tgl_rilis_ruahan)->format('d-m-Y') }}</td>

              <td>{{ optional($batch->tgl_datang_ruahan_akhir)->format('d-m-Y') }}</td>
              <td>{{ optional($batch->tgl_analisa_ruahan_akhir)->format('d-m-Y') }}</td>
              <td>{{ optional($batch->tgl_rilis_ruahan_akhir)->format('d-m-Y') }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="20" class="text-center">
                Belum ada batch yang berstatus QC RELEASED.
              </td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>

      {{ $batches->links() }}
    </div>
  </div>
</section>
@endsection
