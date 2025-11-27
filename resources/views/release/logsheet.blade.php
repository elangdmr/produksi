@extends('layouts.app')

@section('content')
<style>
  /* ===== CUSTOM MODERN TABLE STYLE ===== */
  .logsheet-table thead th {
    background: #f7f7f9 !important;
    font-weight: 600;
    font-size: 12px;
    vertical-align: middle !important;
    border-bottom: 2px solid #e5e5e5 !important;
    white-space: nowrap;
    text-transform: uppercase;
  }

  .logsheet-table tbody td {
    font-size: 12px;
    vertical-align: middle !important;
    text-align: center;
    padding: 6px 8px !important;
    white-space: nowrap;
  }

  .logsheet-table tbody tr:nth-child(odd) {
    background: #fafafa;
  }

  .logsheet-scroll-container {
    max-height: 70vh;
    overflow-y: auto;
    overflow-x: auto;
    scrollbar-width: thin;
  }

  /* sticky header */
  .logsheet-table thead th {
    position: sticky;
    top: 0;
    z-index: 20;
  }

  /* freeze first 3 columns */
  .sticky-col {
    position: sticky;
    left: 0;
    background: #ffffff !important;
    z-index: 30;
  }
  .sticky-col-2 { left: 140px; }
  .sticky-col-3 { left: 280px; }
</style>

@php
  // Helper format tanggal singkat
  $fmt = function ($date) {
      return $date ? \Carbon\Carbon::parse($date)->format('d-M-Y') : '';
  };
@endphp

<section class="app-user-list">
  <div class="row">
    <div class="col-12">

      <div class="card shadow-sm">

        {{-- HEADER --}}
        <div class="card-header d-flex justify-content-between align-items-center">
          <div>
            <h4 class="card-title mb-0 fw-bold">Logsheet Release After Secondary Pack</h4>
            <p class="mb-0 text-muted small">
              Rekap lengkap proses produksi dari <b>Weighing → Secondary Pack + Job Sheet QC, Sampling, COA QC/QA</b>.
            </p>
          </div>

          <div class="d-flex gap-50">
            <a href="{{ route('release.index', request()->query()) }}"
               class="btn btn-sm btn-outline-secondary">
              &laquo; Kembali
            </a>
            <a href="{{ route('release.logsheet.export', request()->query()) }}"
               class="btn btn-sm btn-success">
              Export CSV
            </a>
          </div>
        </div>

        {{-- FILTER --}}
        <div class="card-body border-bottom pb-3">
          <form class="row g-2" method="GET">
            <div class="col-md-3">
              <input type="text" name="q" class="form-control"
                     placeholder="Cari produk / batch..."
                     value="{{ $q ?? '' }}">
            </div>

            <div class="col-md-2">
              <select name="bulan" class="form-control">
                <option value="">Semua Bulan</option>
                @for($i=1;$i<=12;$i++)
                  <option value="{{ $i }}" {{ (string)($bulan ?? '') === (string)$i ? 'selected':'' }}>
                    {{ sprintf('%02d',$i) }}
                  </option>
                @endfor
              </select>
            </div>

            <div class="col-md-2">
              <input type="number" name="tahun" class="form-control"
                     placeholder="Tahun"
                     value="{{ $tahun ?? '' }}">
            </div>

            <div class="col-md-2">
              <button class="btn btn-primary w-100">Filter</button>
            </div>
          </form>
        </div>

        {{-- TABLE --}}
        <div class="logsheet-scroll-container">
          <table class="table table-bordered logsheet-table mb-0">
            <thead class="text-center">
              <tr>
                <th rowspan="2" class="sticky-col">Produk</th>
                <th rowspan="2" class="sticky-col sticky-col-2">No. Batch</th>
                <th rowspan="2" class="sticky-col sticky-col-3">Kode Batch</th>
                <th rowspan="2">Batch</th>
                <th rowspan="2">Month</th>

                <th colspan="2">Weighing</th>
                <th colspan="2">Mixing</th>
                <th rowspan="2">Rilis Granul</th>
                <th colspan="2">Capsule Filling</th>
                <th colspan="2">Tableting</th>
                <th rowspan="2">Rilis Tablet</th>
                <th colspan="2">Coating</th>
                <th rowspan="2">Rilis Ruahan</th>
                <th colspan="2">Primary Pack</th>
                <th rowspan="2">Rilis Ruahan Akhir</th>
                <th colspan="2">Secondary Pack</th>

                {{-- blok tambahan --}}
                <th colspan="2">Job Sheet QC</th>
                <th rowspan="2">Tanggal Sampling</th>
                <th colspan="2">COA QC/QA</th>
                <th rowspan="2">Status</th>
                <th rowspan="2">Catatan</th>
              </tr>

              <tr>
                {{-- Weighing --}}
                <th>Mulai</th><th>Selesai</th>
                {{-- Mixing --}}
                <th>Mulai</th><th>Selesai</th>
                {{-- Capsule --}}
                <th>Mulai</th><th>Selesai</th>
                {{-- Tableting --}}
                <th>Mulai</th><th>Selesai</th>
                {{-- Coating --}}
                <th>Mulai</th><th>Selesai</th>
                {{-- Primary --}}
                <th>Mulai</th><th>Selesai</th>
                {{-- Secondary --}}
                <th>Mulai</th><th>Selesai</th>

                {{-- Job Sheet QC --}}
                <th>Konfirmasi</th>
                <th>Terima Job Sheet</th>

                {{-- COA QC/QA --}}
                <th>QC Kirim COA</th>
                <th>QA Terima COA</th>
              </tr>
            </thead>

            <tbody>
              @forelse($rows as $row)
                <tr>
                  {{-- Identitas batch --}}
                  <td class="sticky-col bg-white">
                    {{ $row->produksi->nama_produk ?? $row->nama_produk }}
                  </td>
                  <td class="sticky-col sticky-col-2 bg-white">{{ $row->no_batch }}</td>
                  <td class="sticky-col sticky-col-3 bg-white">{{ $row->kode_batch }}</td>

                  <td>{{ $row->batch ?? '-' }}</td>
                  <td>{{ $row->bulan }}</td>

                  {{-- WEIGHING --}}
                  <td>{{ $fmt($row->tgl_mulai_weighing) }}</td>
                  <td>{{ $fmt($row->tgl_weighing) }}</td>

                  {{-- MIXING --}}
                  <td>{{ $fmt($row->tgl_mulai_mixing) }}</td>
                  <td>{{ $fmt($row->tgl_mixing) }}</td>

                  {{-- RILIS GRANUL --}}
                  <td>{{ $fmt($row->tgl_rilis_granul) }}</td>

                  {{-- CAPSULE --}}
                  <td>{{ $fmt($row->tgl_mulai_capsule_filling) }}</td>
                  <td>{{ $fmt($row->tgl_capsule_filling) }}</td>

                  {{-- TABLETING --}}
                  <td>{{ $fmt($row->tgl_mulai_tableting) }}</td>
                  <td>{{ $fmt($row->tgl_tableting) }}</td>

                  {{-- RILIS TABLET --}}
                  <td>{{ $fmt($row->tgl_rilis_tablet) }}</td>

                  {{-- COATING --}}
                  <td>{{ $fmt($row->tgl_mulai_coating) }}</td>
                  <td>{{ $fmt($row->tgl_coating) }}</td>

                  {{-- RILIS RUAHAN --}}
                  <td>{{ $fmt($row->tgl_rilis_ruahan) }}</td>

                  {{-- PRIMARY PACK --}}
                  <td>{{ $fmt($row->tgl_mulai_primary_pack) }}</td>
                  <td>{{ $fmt($row->tgl_primary_pack) }}</td>

                  {{-- RILIS RUAHAN AKHIR --}}
                  <td>{{ $fmt($row->tgl_rilis_ruahan_akhir) }}</td>

                  {{-- SECONDARY PACK --}}
                  <td>{{ $fmt($row->tgl_mulai_secondary_pack_1) }}</td>
                  <td>{{ $fmt($row->tgl_secondary_pack_1) }}</td>

                  {{-- JOB SHEET QC --}}
                  <td>{{ $fmt($row->tgl_konfirmasi_produksi) }}</td>
                  <td>{{ $fmt($row->tgl_terima_jobsheet) }}</td>

                  {{-- SAMPLING --}}
                  <td>{{ $fmt($row->tgl_sampling) }}</td>

                  {{-- COA QC/QA --}}
                  <td>{{ $fmt($row->tgl_qc_kirim_coa) }}</td>
                  <td>{{ $fmt($row->tgl_qa_terima_coa) }}</td>

                  {{-- REVIEW --}}
                  <td>
                    @php
                      $status = $row->status_review ?: 'pending';
                      $badgeClass = 'bg-secondary';
                      $text = ucfirst($status);

                      if ($status === 'released') {
                          $badgeClass = 'bg-success';
                          $text = 'Released';
                      } elseif ($status === 'hold') {
                          $badgeClass = 'bg-warning text-dark';
                          $text = 'Hold';
                      } elseif ($status === 'rejected') {
                          $badgeClass = 'bg-danger';
                          $text = 'Rejected';
                      }
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ $text }}</span>
                  </td>

                  <td class="text-start">{{ $row->catatan_review }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="40" class="text-center text-muted">
                    Tidak ada data.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

      </div>

    </div>
  </div>
</section>
@endsection
