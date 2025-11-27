@extends('layouts.app')

@section('content')
<section class="app-user-list">
  <div class="row" id="basic-table">
    <div class="col-12">

      <div class="card">

        {{-- HEADER --}}
        <div class="card-header d-flex justify-content-between align-items-center">
          <div>
            <h4 class="card-title mb-0">Riwayat Review After Secondary Pack</h4>
            <p class="mb-0 text-muted">
              Menampilkan batch yang <strong>proses Review-nya sudah selesai</strong>
              (Released / Rejected). Data di sini bersifat arsip (read only).
            </p>
          </div>

          <a href="{{ route('review.index') }}"
             class="btn btn-sm btn-outline-secondary">
            &laquo; Kembali ke Review
          </a>
        </div>

        @if (session('ok'))
          <div class="alert alert-success m-2">{{ session('ok') }}</div>
        @endif

        {{-- FILTER --}}
        <div class="card-body border-bottom">
          <form method="GET" class="row g-1">

            <div class="col-md-3">
              <input type="text"
                     name="q"
                     value="{{ $q ?? '' }}"
                     class="form-control"
                     placeholder="Cari produk / no batch / kode batch...">
            </div>

            <div class="col-md-2">
              <select name="bulan" class="form-control">
                <option value="">Semua Bulan</option>
                @for ($i = 1; $i <= 12; $i++)
                  <option value="{{ $i }}"
                          {{ (string)($bulan ?? '') === (string)$i ? 'selected' : '' }}>
                    {{ sprintf('%02d', $i) }}
                  </option>
                @endfor
              </select>
            </div>

            <div class="col-md-2">
              <input type="number"
                     name="tahun"
                     value="{{ $tahun ?? '' }}"
                     class="form-control"
                     placeholder="Tahun">
            </div>

            <div class="col-md-2">
              <select name="status" class="form-control">
                <option value="">Semua Status</option>
                <option value="released" {{ ($status ?? '') === 'released' ? 'selected' : '' }}>Released</option>
                <option value="rejected" {{ ($status ?? '') === 'rejected' ? 'selected' : '' }}>Rejected</option>
              </select>
            </div>

            <div class="col-md-2">
              <button class="btn btn-outline-primary w-100">Filter</button>
            </div>

          </form>
        </div>

        {{-- TABEL RIWAYAT --}}
        <div class="table-responsive-sm">
          <table class="table mb-0 align-middle">
            <thead>
              <tr>
                <th>#</th>
                <th>Kode Batch</th>
                <th>Nama Produk</th>
                <th>Bulan</th>
                <th>Tahun</th>
                <th>Qty Batch</th>
                <th>Job Sheet QC</th>
                <th>Sampling</th>
                <th>COA</th>
                <th>Status Review</th>
                <th>Catatan Review</th>
                <th style="width: 140px;" class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($rows as $idx => $row)
                @php
                  $statusReview = $row->status_review ?? 'pending';
                  switch ($statusReview) {
                    case 'released':
                      $badgeClass = 'badge-light-success';
                      $statusText = 'Released';
                      break;
                    case 'rejected':
                      $badgeClass = 'badge-light-danger';
                      $statusText = 'Rejected';
                      break;
                    default:
                      $badgeClass = 'badge-light-secondary';
                      $statusText = ucfirst($statusReview);
                  }
                @endphp

                <tr>
                  <td>{{ $rows->firstItem() + $idx }}</td>
                  <td>{{ $row->kode_batch }}</td>
                  <td>{{ $row->nama_produk }}</td>
                  <td>{{ $row->bulan }}</td>
                  <td>{{ $row->tahun }}</td>

                  {{-- ringkasan Qty --}}
                  <td>
                    {{ $row->qty_batch ?? '-' }}
                    <br>
                    <small class="text-muted">{{ $row->status_qty_batch ?? '-' }}</small>
                  </td>

                  {{-- status step sebelumnya --}}
                  <td>{{ $row->status_jobsheet ?? '-' }}</td>
                  <td>{{ $row->status_sampling ?? '-' }}</td>
                  <td>{{ $row->status_coa ?? '-' }}</td>

                  {{-- status review --}}
                  <td>
                    <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                    @if($row->tgl_review)
                      <br><small class="text-muted">{{ $row->tgl_review }}</small>
                    @endif
                  </td>

                  {{-- catatan review --}}
                  <td>
                    @if($row->catatan_review)
                      <small class="text-muted d-block" style="white-space: pre-line;">
                        {{ $row->catatan_review }}
                      </small>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>

                  {{-- aksi (read only) --}}
                  <td class="text-center">
                    <span class="badge bg-light text-muted">Riwayat / read only</span>
                  </td>
                </tr>

              @empty
                <tr>
                  <td colspan="12" class="text-center text-muted">
                    Belum ada data riwayat review.
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
