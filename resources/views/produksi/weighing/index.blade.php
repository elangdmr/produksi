@extends('layouts.app')

@section('content')
<section class="app-user">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h4 class="card-title mb-0">Weighing</h4>
    </div>

    <div class="card-body">

      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      {{-- Filter --}}
      <form method="GET" action="{{ route('weighing.index') }}" class="row g-2 mb-3">
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

      {{-- Tabel batch --}}
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
              <th>Tgl Selesai Weighing + Simpan / Konfirmasi</th>
            </tr>
          </thead>
          <tbody>
          @forelse($batches as $index => $batch)
            @php
              $hasWeighing = !is_null($batch->tgl_weighing);
              $defaultWeighing = old(
                  'tgl_weighing',
                  optional($batch->tgl_weighing)->format('Y-m-d') ?? now()->format('Y-m-d')
              );

              // cek apakah route mixing.index ada
              $routeMixingExists = \Route::has('mixing.index');
              $canGoMixing       = $hasWeighing && $routeMixingExists;
            @endphp

            <tr>
              <td>{{ $batches->firstItem() + $index }}</td>
              <td>{{ $batch->produksi->nama_produk ?? $batch->nama_produk }}</td>
              <td>{{ $batch->no_batch }}</td>
              <td>{{ $batch->kode_batch }}</td>
              <td>{{ $batch->bulan }}</td>
              <td>{{ $batch->tahun }}</td>
              <td>{{ $batch->wo_date ? $batch->wo_date->format('d-m-Y') : '-' }}</td>
              <td>{{ $batch->expected_date ? $batch->expected_date->format('d-m-Y') : '-' }}</td>

              {{-- kolom tanggal + tombol simpan + konfirmasi mixing --}}
              <td>
                <form action="{{ route('weighing.store', $batch) }}"
                      method="POST"
                      class="d-flex align-items-center flex-wrap gap-1">
                  @csrf

                  <input type="date"
                         name="tgl_weighing"
                         value="{{ $defaultWeighing }}"
                         class="form-control form-control-sm me-1">

                  <button class="btn btn-sm btn-primary me-1">
                    Simpan
                  </button>

                  @if($canGoMixing)
                    {{-- route mixing.index sudah ada dan tanggal weighing sudah diisi --}}
                    <a href="{{ route('mixing.index', ['batch' => $batch->id]) }}"
                       class="btn btn-sm btn-outline-secondary">
                      Konfirmasi Mixing
                    </a>
                  @else
                    {{-- belum ada tanggal weighing / route mixing belum dibuat --}}
                    <button type="button"
                            class="btn btn-sm btn-outline-secondary"
                            disabled>
                      Konfirmasi Mixing
                    </button>
                  @endif
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="text-center">
                Tidak ada batch yang menunggu proses weighing.
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
