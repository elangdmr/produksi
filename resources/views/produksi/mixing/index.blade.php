@extends('layouts.app')

@section('content')
<section class="app-user">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <div>
        <h4 class="card-title mb-0">Mixing</h4>
        <small class="text-muted">Menampilkan batch yang sudah weighing tapi belum selesai mixing.</small>
      </div>

      <a href="{{ route('mixing.history') }}" class="btn btn-sm btn-outline-secondary">
        Riwayat Mixing
      </a>
    </div>

    <div class="card-body">

      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
      @endif

      {{-- Filter --}}
      <form method="GET" action="{{ route('mixing.index') }}" class="row g-2 mb-3">
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

      {{-- Tabel mixing --}}
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
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>

          @forelse($batches as $index => $batch)
            @php
              $formId = 'form-'.$batch->id;

              $valMulai = $batch->tgl_mulai_mixing
                ? \Illuminate\Support\Carbon::parse($batch->tgl_mulai_mixing)->format('Y-m-d')
                : now()->format('Y-m-d');

              // karena di index kita filter whereNull(tgl_mixing), default isi hari ini
              $valSelesai = $batch->tgl_mixing
                ? \Illuminate\Support\Carbon::parse($batch->tgl_mixing)->format('Y-m-d')
                : now()->format('Y-m-d');
            @endphp

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

              {{-- TGL MULAI MIXING --}}
              <td>
                <input type="date"
                       name="tgl_mulai_mixing"
                       form="{{ $formId }}"
                       value="{{ $valMulai }}"
                       class="form-control form-control-sm">
              </td>

              {{-- TGL SELESAI MIXING --}}
              <td>
                <input type="date"
                       name="tgl_mixing"
                       form="{{ $formId }}"
                       value="{{ $valSelesai }}"
                       class="form-control form-control-sm">
              </td>

              {{-- AKSI --}}
              <td class="text-center">
                <form id="{{ $formId }}"
                      action="{{ route('mixing.confirm', $batch) }}"
                      method="POST">
                  @csrf
                </form>

                <button type="submit"
                        form="{{ $formId }}"
                        class="btn btn-sm btn-primary px-3"
                        style="white-space: nowrap;"
                        onclick="return confirm('Konfirmasi selesai mixing untuk batch ini?');">
                  Konfirmasi Mixing
                </button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="12" class="text-center">
                Tidak ada batch yang menunggu proses mixing.
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
