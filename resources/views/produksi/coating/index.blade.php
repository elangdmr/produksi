@extends('layouts.app')

@section('content')
<section class="app-user-list">
  <div class="row" id="basic-table">
    <div class="col-12">
      <div class="card">

        {{-- HEADER --}}
        <div class="card-header d-flex justify-content-between align-items-center">
          <div>
            <h4 class="card-title mb-0">Coating</h4>
            <p class="mb-0 text-muted">
              Input & konfirmasi tanggal Coating per batch.
              Data diambil dari jadwal produksi yang sudah selesai Tableting.
            </p>
          </div>

          <div class="d-flex gap-1">
            {{-- Tombol ke Riwayat Coating --}}
            <a href="{{ route('coating.history') }}" class="btn btn-sm btn-outline-secondary">
              Riwayat Coating
            </a>
          </div>
        </div>

        <div class="card-body">

          {{-- FLASH MESSAGE --}}
          @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
          @endif

          {{-- FILTER --}}
          <form method="GET" action="{{ route('coating.index') }}" class="row g-2 mb-3">
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
                @for ($m = 1; $m <= 12; $m++)
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

          {{-- TABEL COATING --}}
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
                  <th>Tgl Tableting</th>
                  <th>Tgl Mulai Coating</th>
                  <th>Tgl Coating (Selesai)</th>
                  <th style="width:210px;">Aksi</th>
                </tr>
              </thead>

              <tbody>
              @forelse ($batches as $index => $batch)
                @php
                  $formId      = 'coating-form-' . $batch->id;
                  $isEaz       = \Illuminate\Support\Str::contains($batch->kode_batch, 'EAZ-');
                  $canSplitEaz =
                      \Illuminate\Support\Str::contains($batch->kode_batch, 'EA-') &&
                      ! $isEaz;
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
                  <td>{{ $batch->tgl_tableting ? $batch->tgl_tableting->format('d-m-Y') : '-' }}</td>

                  {{-- Input TGL MULAI COATING --}}
                  <td>
                    <input type="date"
                           name="tgl_mulai_coating"
                           form="{{ $formId }}"
                           value="{{ old('tgl_mulai_coating', optional($batch->tgl_mulai_coating)->format('Y-m-d')) }}"
                           class="form-control form-control-sm">
                  </td>

                  {{-- Input TGL SELESAI COATING --}}
                  <td>
                    <input type="date"
                           name="tgl_coating"
                           form="{{ $formId }}"
                           value="{{ old('tgl_coating', optional($batch->tgl_coating)->format('Y-m-d')) }}"
                           class="form-control form-control-sm">
                  </td>

                  <td>
                    <div class="d-grid gap-1">

                      {{-- BUAT MESIN 2 (EAZ) JIKA MASIH EA- --}}
                      @if ($canSplitEaz)
                        <form action="{{ route('coating.split-eaz', $batch->id) }}"
                              method="POST"
                              onsubmit="return confirm('Buat batch mesin Coating 2 (EAZ)?');">
                          @csrf
                          <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                            + Mesin 2 (EAZ)
                          </button>
                        </form>
                      @endif

                      {{-- LINK EDIT DETAIL --}}
                      <a href="{{ route('coating.edit', $batch->id) }}"
                         class="btn btn-sm btn-outline-secondary w-100">
                        Edit
                      </a>

                      {{-- SIMPAN & KONFIRMASI COATING --}}
                      <form id="{{ $formId }}"
                            action="{{ route('coating.store', $batch) }}"
                            method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                          Simpan &amp; Konfirmasi
                        </button>
                      </form>

                      {{-- HAPUS EAZ JIKA KODE EAZ- --}}
                      @if ($isEaz)
                        <form action="{{ route('coating.destroy-eaz', $batch->id) }}"
                              method="POST"
                              onsubmit="return confirm('Hapus baris mesin 2 (EAZ) ini?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                            Hapus EAZ
                          </button>
                        </form>
                      @endif
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="12" class="text-center">
                    Tidak ada data batch untuk Coating.
                  </td>
                </tr>
              @endforelse
              </tbody>
            </table>
          </div>

          {{-- PAGINATION --}}
          {{ $batches->links() }}

        </div>
      </div>
    </div>
  </div>
</section>
@endsection
