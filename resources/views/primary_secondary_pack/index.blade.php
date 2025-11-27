@extends('layouts.app')

@section('content')
<section class="app-user-list">
  <div class="row" id="basic-table">
    <div class="col-12">
      <div class="card">

        {{-- Header --}}
        <div class="card-header d-flex justify-content-between align-items-center">
          <div>
            <h4 class="card-title">Primary &amp; Secondary Pack</h4>
            <p class="mb-0 text-muted">
              Input tanggal mulai dan selesai proses Primary Pack dan Secondary Pack per batch
              sesuai alur produksi (Coating → Ruahan → Primary → Ruahan Akhir → Secondary).
            </p>
          </div>

          {{-- Tombol menuju halaman riwayat --}}
          <div>
            <a href="{{ route('primary-secondary.history') }}"
               class="btn btn-sm btn-outline-secondary">
              Riwayat Primary &amp; Secondary
            </a>
          </div>
        </div>

        {{-- Flash message --}}
        @if(session('ok'))
          <div class="alert alert-success m-2">{{ session('ok') }}</div>
        @endif

        @if($errors->any())
          <div class="alert alert-danger m-2">
            {{ $errors->first() }}
          </div>
        @endif

        {{-- Filter --}}
        <div class="card-body border-bottom">
          <form class="row g-1" method="GET" action="{{ route('primary-secondary.index') }}">
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
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($rows as $idx => $row)
                @php
                  // ========= LOGIKA ALUR PER BATCH =========

                  $tipeAlur = $row->produksi->tipe_alur ?? null;

                  $hasCoating          = !empty($row->tgl_coating);
                  $hasRuahanAwalRilis  = !empty($row->tgl_rilis_ruahan);
                  $hasRuahanAkhirRilis = !empty($row->tgl_rilis_ruahan_akhir);
                  $hasPrimaryDone      = !empty($row->tgl_primary_pack);

                  // Default: alur TANPA ruahan → Primary boleh kalau Coating selesai
                  $canPrimary = $hasCoating;

                  // Kalau tipenya pakai Ruahan (Awal & Akhir), Primary boleh
                  // setelah Ruahan Awal rilis.
                  if ($tipeAlur === 'ruahan_awal_akhir') {
                      $canPrimary = $hasRuahanAwalRilis;
                  }

                  // Secondary boleh kalau:
                  // - Primary sudah selesai, dan
                  // - Kalau ada Ruahan Akhir, rilis Ruahan Akhir juga sudah ada
                  if ($tipeAlur === 'ruahan_awal_akhir') {
                      $canSecondary = $hasPrimaryDone && $hasRuahanAkhirRilis;
                  } else {
                      $canSecondary = $hasPrimaryDone;
                  }
                @endphp

                <tr>
                  <td>{{ $rows->firstItem() + $idx }}</td>
                  <td>{{ $row->kode_batch }}</td>
                  <td>{{ $row->produksi->nama_produk ?? $row->nama_produk }}</td>
                  <td>{{ $row->bulan }}</td>
                  <td>{{ $row->tahun }}</td>
                  <td>{{ optional($row->wo_date)->format('d-m-Y') }}</td>

                  {{-- 1 form per baris --}}
                  <form action="{{ route('primary-secondary.store', $row->id) }}"
                        method="POST">
                    @csrf

                    {{-- PRIMARY PACK MULAI --}}
                    <td style="min-width: 150px;">
                      <input type="date"
                             name="tgl_mulai_primary_pack"
                             class="form-control form-control-sm"
                             value="{{ optional($row->tgl_mulai_primary_pack)->format('Y-m-d') }}"
                             {{ $canPrimary ? '' : 'disabled' }}>
                      @unless($canPrimary)
                        <small class="text-muted d-block mt-25" style="font-size: 0.7rem;">
                          Menunggu proses sebelum Primary (Coating / Ruahan Awal).
                        </small>
                      @endunless
                    </td>

                    {{-- PRIMARY PACK SELESAI --}}
                    <td style="min-width: 150px;">
                      <input type="date"
                             name="tgl_primary_pack"
                             class="form-control form-control-sm"
                             value="{{ optional($row->tgl_primary_pack)->format('Y-m-d') }}"
                             {{ $canPrimary ? '' : 'disabled' }}>
                    </td>

                    {{-- SECONDARY PACK MULAI --}}
                    <td style="min-width: 150px;">
                      <input type="date"
                             name="tgl_mulai_secondary_pack_1"
                             class="form-control form-control-sm"
                             value="{{ optional($row->tgl_mulai_secondary_pack_1)->format('Y-m-d') }}"
                             {{ $canSecondary ? '' : 'disabled' }}>
                      @if(!$canSecondary)
                        <small class="text-muted d-block mt-25" style="font-size: 0.7rem;">
                          Isi setelah Primary selesai
                          @if($tipeAlur === 'ruahan_awal_akhir')
                            &amp; Ruahan Akhir rilis.
                          @endif
                        </small>
                      @endif
                    </td>

                    {{-- SECONDARY PACK SELESAI --}}
                    <td style="min-width: 150px;">
                      <input type="date"
                             name="tgl_secondary_pack_1"
                             class="form-control form-control-sm"
                             value="{{ optional($row->tgl_secondary_pack_1)->format('Y-m-d') }}"
                             {{ $canSecondary ? '' : 'disabled' }}>
                    </td>

                    {{-- AKSI --}}
                    <td class="text-center" style="white-space: nowrap;">
                      {{-- SIMPAN: hanya update tanggal --}}
                      <button type="submit"
                              class="btn btn-sm btn-outline-primary"
                              style="padding: 4px 10px;">
                        Simpan
                      </button>

                      {{-- KONFIRMASI: update tanggal + redirect ke form Qty Batch --}}
                      <button type="submit"
                              class="btn btn-sm btn-primary ms-50"
                              formaction="{{ route('primary-secondary.confirm', $row->id) }}"
                              onclick="return confirm('Konfirmasi tanggal Primary & Secondary Pack untuk batch ini?');"
                              style="padding: 4px 10px;"
                              {{ $row->tgl_secondary_pack_1 ? '' : 'disabled' }}>
                        Konfirmasi
                      </button>
                    </td>
                  </form>
                </tr>
              @empty
                <tr>
                  <td colspan="11" class="text-center text-muted">
                    Belum ada data produksi.
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
