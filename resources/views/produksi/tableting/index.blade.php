@extends('layouts.app')

@section('content')
<section class="app-user-list">
  <div class="row" id="basic-table">
    <div class="col-12">
      <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">
          <div>
            <h4 class="card-title mb-0">Tableting</h4>
            <p class="mb-0 text-muted">
              Menampilkan batch tablet yang sudah selesai Mixing namun belum dikonfirmasi Tableting.
            </p>
          </div>

          <a href="{{ route('tableting.history') }}" class="btn btn-sm btn-outline-secondary">
            Riwayat Tableting
          </a>
        </div>

        <div class="card-body">

          @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
          @endif

          @if($errors->any())
            <div class="alert alert-danger">
              {{ $errors->first() }}
            </div>
          @endif

          {{-- Filter --}}
          <form method="GET" action="{{ route('tableting.index') }}" class="row g-2 mb-3">
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

          {{-- Tabel Tableting --}}
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
                  <th>Tgl Mixing</th>
                  <th>Tgl Mulai Tableting</th>
                  <th>Tgl Selesai Tableting</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>

              @forelse($batches as $index => $batch)
                @php
                  $formId = 'tableting-form-' . $batch->id;

                  $valMulai = $batch->tgl_mulai_tableting
                    ? \Illuminate\Support\Carbon::parse($batch->tgl_mulai_tableting)->format('Y-m-d')
                    : now()->format('Y-m-d');

                  // di index pasti tgl_tableting masih null, jadi default hari ini
                  $valSelesai = $batch->tgl_tableting
                    ? \Illuminate\Support\Carbon::parse($batch->tgl_tableting)->format('Y-m-d')
                    : now()->format('Y-m-d');
                @endphp
                <tr>
                  <td>{{ $batches->firstItem() + $index }}</td>

                  <td>{{ $batch->produksi->nama_produk ?? $batch->nama_produk }}</td>
                  <td>{{ $batch->no_batch }}</td>
                  <td>{{ $batch->kode_batch }}</td>
                  <td>{{ $batch->bulan }}</td>
                  <td>{{ $batch->tahun }}</td>

                  <td>
                    {{ $batch->wo_date
                        ? \Illuminate\Support\Carbon::parse($batch->wo_date)->format('d-m-Y')
                        : '-' }}
                  </td>
                  <td>
                    {{ $batch->expected_date
                        ? \Illuminate\Support\Carbon::parse($batch->expected_date)->format('d-m-Y')
                        : '-' }}
                  </td>
                  <td>
                    {{ $batch->tgl_mixing
                        ? \Illuminate\Support\Carbon::parse($batch->tgl_mixing)->format('d-m-Y')
                        : '-' }}
                  </td>

                  {{-- input TGL MULAI TABLETING --}}
                  <td>
                    <input type="date"
                           name="tgl_mulai_tableting"
                           form="{{ $formId }}"
                           value="{{ $valMulai }}"
                           class="form-control form-control-sm">
                  </td>

                  {{-- input TGL SELESAI TABLETING --}}
                  <td>
                    <input type="date"
                           name="tgl_tableting"
                           form="{{ $formId }}"
                           value="{{ $valSelesai }}"
                           class="form-control form-control-sm">
                  </td>

                  <td class="text-center">
                    <form id="{{ $formId }}"
                          action="{{ route('tableting.confirm', $batch) }}"
                          method="POST">
                      @csrf
                    </form>

                    <button type="submit"
                            form="{{ $formId }}"
                            class="btn btn-sm btn-primary w-100"
                            onclick="return confirm('Konfirmasi selesai Tableting untuk batch ini?');">
                      Konfirmasi
                    </button>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="12" class="text-center">
                    Tidak ada data batch yang menunggu proses Tableting.
                  </td>
                </tr>
              @endforelse

              </tbody>
            </table>
          </div>

          {{ $batches->links() }}

        </div>
      </div>
    </div>
  </div>
</section>
@endsection
