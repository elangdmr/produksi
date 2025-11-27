@extends('layouts.app')

@section('content')
<section class="app-user-list">
  <div class="row" id="basic-table">
    <div class="col-12">

      <div class="card">

        {{-- Header --}}
        <div class="card-header d-flex justify-content-between align-items-center">
          <div>
            <h4 class="card-title mb-0">Riwayat COA</h4>
            <p class="mb-0 text-muted">
              Menampilkan COA yang sudah dikonfirmasi oleh QA (selesai & final).
            </p>
          </div>

          <a href="{{ route('coa.index') }}"
             class="btn btn-sm btn-outline-secondary">
            &laquo; Kembali ke Data Aktif
          </a>
        </div>

        {{-- Filter --}}
        <div class="card-body border-bottom">
          <form class="row g-1" method="GET">

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
              <button class="btn btn-outline-primary w-100">Filter</button>
            </div>

          </form>
        </div>

        {{-- Tabel Riwayat --}}
        <div class="table-responsive">
          <table class="table mb-0 align-middle">
            <thead>
              <tr>
                <th>#</th>
                <th>Kode Batch</th>
                <th>Nama Produk</th>
                <th>Bulan</th>
                <th>Tahun</th>
                <th>WO Date</th>
                <th>Qty Batch</th>
                <th>Tgl QC Kirim COA</th>
                <th>Tgl QA Terima COA</th>
                <th>Status Review</th>
                <th style="max-width: 260px;">Catatan Review</th>
              </tr>
            </thead>

            <tbody>
            @forelse($rows as $idx => $row)
              @php
                $statusReview = $row->status_review ?? 'pending';
                switch ($statusReview) {
                    case 'released':
                        $badgeReview = 'badge-light-success';
                        $statusText  = 'Released';
                        break;
                    case 'hold':
                        $badgeReview = 'badge-light-warning';
                        $statusText  = 'Hold';
                        break;
                    case 'rejected':
                        $badgeReview = 'badge-light-danger';
                        $statusText  = 'Rejected';
                        break;
                    default:
                        $badgeReview = 'badge-light-secondary';
                        $statusText  = 'Pending';
                }
              @endphp

              <tr>
                <td>{{ $rows->firstItem() + $idx }}</td>
                <td>{{ $row->kode_batch }}</td>
                <td>{{ $row->nama_produk }}</td>
                <td>{{ $row->bulan }}</td>
                <td>{{ $row->tahun }}</td>
                <td>{{ $row->wo_date ? $row->wo_date->format('d-m-Y') : '-' }}</td>
                <td>{{ $row->qty_batch }}</td>

                <td>
                  {{ $row->tgl_qc_kirim_coa
                        ? \Carbon\Carbon::parse($row->tgl_qc_kirim_coa)->format('d-m-Y')
                        : '-' }}
                </td>
                <td>
                  {{ $row->tgl_qa_terima_coa
                        ? \Carbon\Carbon::parse($row->tgl_qa_terima_coa)->format('d-m-Y')
                        : '-' }}
                </td>

                <td>
                  <span class="badge {{ $badgeReview }}">{{ $statusText }}</span>
                </td>

                <td style="max-width: 260px;">
                  @if($row->catatan_review)
                    <small class="text-muted d-block">
                      {{ $row->catatan_review }}
                    </small>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="11" class="text-center text-muted">
                  Belum ada riwayat COA.
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
