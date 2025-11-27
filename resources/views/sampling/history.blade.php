@extends('layouts.app')

@section('content')
<section class="app-user-list">

  <div class="row">
    <div class="col-12">

      <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">
          <div>
            <h4 class="card-title mb-0">Riwayat Sampling</h4>
            <p class="text-muted mb-0">
              Menampilkan batch yang Sampling-nya sudah di-ACC atau ditolak.
            </p>
          </div>

          <a href="{{ route('sampling.index') }}"
             class="btn btn-sm btn-outline-secondary">
            &laquo; Kembali ke Data Aktif
          </a>
        </div>

        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>Kode Batch</th>
                <th>Produk</th>
                <th>Qty</th>
                <th>Bulan</th>
                <th>Tahun</th>
                <th>Status</th>
                <th>Tanggal Sampling</th>
              </tr>
            </thead>

            <tbody>
              @forelse ($rows as $idx => $row)
                @php
                  $status = $row->status_sampling;
                  $badge  = $status === 'accepted'
                              ? 'badge-light-success'
                              : 'badge-light-danger';
                @endphp

                <tr>
                  <td>{{ $rows->firstItem() + $idx }}</td>
                  <td>{{ $row->kode_batch }}</td>
                  <td>{{ $row->nama_produk }}</td>
                  <td>{{ $row->qty_batch }}</td>
                  <td>{{ $row->bulan }}</td>
                  <td>{{ $row->tahun }}</td>
                  <td><span class="badge {{ $badge }}">{{ ucfirst($status) }}</span></td>
                  <td>{{ $row->tgl_sampling ?: '-' }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="text-center text-muted">
                    Belum ada riwayat sampling.
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
