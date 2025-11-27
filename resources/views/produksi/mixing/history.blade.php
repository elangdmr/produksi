@extends('layouts.app')

@section('content')
<section class="app-user">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <div>
        <h4 class="card-title mb-0">Riwayat Mixing</h4>
        <small class="text-muted">Batch yang sudah selesai dan dikonfirmasi mixing.</small>
      </div>

      <a href="{{ route('mixing.index') }}" class="btn btn-sm btn-outline-secondary">
        &laquo; Kembali ke Mixing
      </a>
    </div>

    <div class="card-body">

      {{-- Filter --}}
      <form method="GET" action="{{ route('mixing.history') }}" class="row g-2 mb-3">
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
            <option value="all" {{ $currentBulan === 'all' ? 'selected' : '' }}>Semua Bulan</option>
            @for($m=1; $m<=12; $m++)
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

      {{-- Tabel riwayat mixing --}}
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
              <th>Expected Date</th>
              <th>Tgl Weighing</th>
              <th>Tgl Mulai Mixing</th>
              <th>Tgl Selesai Mixing</th>
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

              <td>{{ $batch->wo_date ? \Illuminate\Support\Carbon::parse($batch->wo_date)->format('d-m-Y') : '-' }}</td>
              <td>{{ $batch->expected_date ? \Illuminate\Support\Carbon::parse($batch->expected_date)->format('d-m-Y') : '-' }}</td>
              <td>{{ $batch->tgl_weighing ? \Illuminate\Support\Carbon::parse($batch->tgl_weighing)->format('d-m-Y') : '-' }}</td>

              <td>{{ $batch->tgl_mulai_mixing ? \Illuminate\Support\Carbon::parse($batch->tgl_mulai_mixing)->format('d-m-Y') : '-' }}</td>
              <td>{{ $batch->tgl_mixing ? \Illuminate\Support\Carbon::parse($batch->tgl_mixing)->format('d-m-Y') : '-' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="11" class="text-center">
                Belum ada riwayat mixing.
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
