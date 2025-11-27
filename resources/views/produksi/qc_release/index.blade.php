@extends('layouts.app')

@section('content')
<section class="app-user">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <div>
        <h4 class="card-title mb-0">QC Release (Tanggal Datang / Analisa / Release)</h4>
        <p class="mb-0 text-muted">
          Input tanggal QC per tahap (Granul → Tablet → Ruahan → Ruahan Akhir)
          mengikuti urutan proses produksi.
        </p>
      </div>

      <div>
        <a href="{{ route('qc-release.history') }}"
           class="btn btn-sm btn-outline-secondary">
          Riwayat QC Release
        </a>
      </div>
    </div>

    <div class="card-body">

      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      {{-- Filter --}}
      <form method="GET" action="{{ route('qc-release.index') }}" class="row g-2 mb-3">
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

      {{-- Tabel QC Release --}}
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead>
            {{-- Header baris pertama: grup kolom --}}
            <tr>
              <th rowspan="2">#</th>
              <th rowspan="2">Produk</th>
              <th rowspan="2">No Batch</th>
              <th rowspan="2">Kode Batch</th>
              <th rowspan="2">Bulan</th>
              <th rowspan="2">Tahun</th>
              <th rowspan="2">WO Date</th>
              <th rowspan="2">Mixing</th>

              <th colspan="3" class="text-center bg-light">Produk Antara Granul</th>
              <th colspan="3" class="text-center bg-light">Produk Antara Tablet</th>
              <th colspan="3" class="text-center bg-light">Produk Ruahan</th>
              <th colspan="3" class="text-center bg-light">Produk Ruahan Akhir</th>

              <th rowspan="2">Aksi</th>
            </tr>

            {{-- Header baris kedua: sub-kolom --}}
            <tr>
              <th class="text-center">Tgl Datang</th>
              <th class="text-center">Tgl Analisa</th>
              <th class="text-center">Tgl Release</th>

              <th class="text-center">Tgl Datang</th>
              <th class="text-center">Tgl Analisa</th>
              <th class="text-center">Tgl Release</th>

              <th class="text-center">Tgl Datang</th>
              <th class="text-center">Tgl Analisa</th>
              <th class="text-center">Tgl Release</th>

              <th class="text-center">Tgl Datang</th>
              <th class="text-center">Tgl Analisa</th>
              <th class="text-center">Tgl Release</th>
            </tr>
          </thead>

          <tbody>
          @forelse($batches as $index => $batch)
            @php
              $formId = 'qc-form-' . $batch->id;

              // LOGIKA ALUR
              $canGranul       = !empty($batch->tgl_mixing);
              $canTablet       = !empty($batch->tgl_tableting);
              $canRuahan       = !empty($batch->tgl_coating);
              $canRuahanAkhir  = !empty($batch->tgl_primary_pack);
            @endphp
            <tr>
              <td>{{ $batches->firstItem() + $index }}</td>
              <td>{{ $batch->produksi->nama_produk ?? $batch->nama_produk }}</td>
              <td>{{ $batch->no_batch }}</td>
              <td>{{ $batch->kode_batch }}</td>
              <td>{{ $batch->bulan }}</td>
              <td>{{ $batch->tahun }}</td>
              <td>{{ optional($batch->wo_date)->format('d-m-Y') }}</td>
              <td>{{ optional($batch->tgl_mixing)->format('d-m-Y') }}</td>

              {{-- ====== Granul ====== --}}
              <td>
                <input type="date"
                       name="tgl_datang_granul"
                       form="{{ $formId }}"
                       value="{{ old('tgl_datang_granul', optional($batch->tgl_datang_granul)->format('Y-m-d')) }}"
                       class="form-control form-control-sm"
                       {{ $canGranul ? '' : 'disabled' }}>
                @unless($canGranul)
                  <small class="text-muted" style="font-size:0.7rem;">
                    Menunggu proses Mixing.
                  </small>
                @endunless
              </td>
              <td>
                <input type="date"
                       name="tgl_analisa_granul"
                       form="{{ $formId }}"
                       value="{{ old('tgl_analisa_granul', optional($batch->tgl_analisa_granul)->format('Y-m-d')) }}"
                       class="form-control form-control-sm"
                       {{ $canGranul ? '' : 'disabled' }}>
              </td>
              <td>
                <input type="date"
                       name="tgl_rilis_granul"
                       form="{{ $formId }}"
                       value="{{ old('tgl_rilis_granul', optional($batch->tgl_rilis_granul)->format('Y-m-d')) }}"
                       class="form-control form-control-sm"
                       {{ $canGranul ? '' : 'disabled' }}>
              </td>

              {{-- ====== Tablet ====== --}}
              <td>
                <input type="date"
                       name="tgl_datang_tablet"
                       form="{{ $formId }}"
                       value="{{ old('tgl_datang_tablet', optional($batch->tgl_datang_tablet)->format('Y-m-d')) }}"
                       class="form-control form-control-sm"
                       {{ $canTablet ? '' : 'disabled' }}>
                @unless($canTablet)
                  <small class="text-muted" style="font-size:0.7rem;">
                    Menunggu proses Tableting.
                  </small>
                @endunless
              </td>
              <td>
                <input type="date"
                       name="tgl_analisa_tablet"
                       form="{{ $formId }}"
                       value="{{ old('tgl_analisa_tablet', optional($batch->tgl_analisa_tablet)->format('Y-m-d')) }}"
                       class="form-control form-control-sm"
                       {{ $canTablet ? '' : 'disabled' }}>
              </td>
              <td>
                <input type="date"
                       name="tgl_rilis_tablet"
                       form="{{ $formId }}"
                       value="{{ old('tgl_rilis_tablet', optional($batch->tgl_rilis_tablet)->format('Y-m-d')) }}"
                       class="form-control form-control-sm"
                       {{ $canTablet ? '' : 'disabled' }}>
              </td>

              {{-- ====== Ruahan ====== --}}
              <td>
                <input type="date"
                       name="tgl_datang_ruahan"
                       form="{{ $formId }}"
                       value="{{ old('tgl_datang_ruahan', optional($batch->tgl_datang_ruahan)->format('Y-m-d')) }}"
                       class="form-control form-control-sm"
                       {{ $canRuahan ? '' : 'disabled' }}>
                @unless($canRuahan)
                  <small class="text-muted" style="font-size:0.7rem;">
                    Menunggu proses Coating.
                  </small>
                @endunless
              </td>
              <td>
                <input type="date"
                       name="tgl_analisa_ruahan"
                       form="{{ $formId }}"
                       value="{{ old('tgl_analisa_ruahan', optional($batch->tgl_analisa_ruahan)->format('Y-m-d')) }}"
                       class="form-control form-control-sm"
                       {{ $canRuahan ? '' : 'disabled' }}>
              </td>
              <td>
                <input type="date"
                       name="tgl_rilis_ruahan"
                       form="{{ $formId }}"
                       value="{{ old('tgl_rilis_ruahan', optional($batch->tgl_rilis_ruahan)->format('Y-m-d')) }}"
                       class="form-control form-control-sm"
                       {{ $canRuahan ? '' : 'disabled' }}>
              </td>

              {{-- ====== Ruahan Akhir ====== --}}
              <td>
                <input type="date"
                       name="tgl_datang_ruahan_akhir"
                       form="{{ $formId }}"
                       value="{{ old('tgl_datang_ruahan_akhir', optional($batch->tgl_datang_ruahan_akhir)->format('Y-m-d')) }}"
                       class="form-control form-control-sm"
                       {{ $canRuahanAkhir ? '' : 'disabled' }}>
                @unless($canRuahanAkhir)
                  <small class="text-muted" style="font-size:0.7rem;">
                    Menunggu Primary Pack selesai.
                  </small>
                @endunless
              </td>
              <td>
                <input type="date"
                       name="tgl_analisa_ruahan_akhir"
                       form="{{ $formId }}"
                       value="{{ old('tgl_analisa_ruahan_akhir', optional($batch->tgl_analisa_ruahan_akhir)->format('Y-m-d')) }}"
                       class="form-control form-control-sm"
                       {{ $canRuahanAkhir ? '' : 'disabled' }}>
              </td>
              <td>
                <input type="date"
                       name="tgl_rilis_ruahan_akhir"
                       form="{{ $formId }}"
                       value="{{ old('tgl_rilis_ruahan_akhir', optional($batch->tgl_rilis_ruahan_akhir)->format('Y-m-d')) }}"
                       class="form-control form-control-sm"
                       {{ $canRuahanAkhir ? '' : 'disabled' }}>
              </td>

              {{-- Aksi --}}
              <td>
                <form id="{{ $formId }}"
                      action="{{ route('qc-release.update', $batch) }}"
                      method="POST">
                  @csrf
                  @method('PUT')

                  <div class="d-flex flex-column flex-lg-row gap-1">
                    <button type="submit"
                            name="action"
                            value="save"
                            class="btn btn-sm btn-outline-primary w-100">
                      Simpan
                    </button>

                    <button type="submit"
                            name="action"
                            value="confirm"
                            class="btn btn-sm btn-primary w-100"
                            onclick="return confirm('Yakin ingin mengkonfirmasi rilis QC untuk batch {{ $batch->no_batch }}?');">
                      Konfirmasi
                    </button>
                  </div>

                  @if($batch->status_proses === 'QC RELEASED')
                    <small class="text-success d-block mt-1">
                      Sudah dikonfirmasi (QC RELEASED).
                    </small>
                  @endif
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="22" class="text-center">
                Tidak ada batch yang menunggu rilis QC.
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
