@extends('layouts.app')

@section('content')
<section class="app-user-list">
  <div class="row" id="basic-table">
    <div class="col-12">
      <div class="card">

        <div class="card-header d-flex align-items-center justify-content-between">
          <h4 class="card-title">Trial R&amp;D</h4>
          <ul class="nav nav-tabs">
            <li class="nav-item">
              <a class="nav-link active" data-bs-toggle="tab" href="#tab-pending">Proses Trial</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="tab" href="#tab-history">Riwayat Trial</a>
            </li>
          </ul>
        </div>

        <div class="tab-content">
          {{-- PROSES TRIAL --}}
          <div class="tab-pane active" id="tab-pending">
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>Tanggal</th>
                    <th>ID Permintaan</th>
                    <th>Nama Bahan</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($pending as $r)
                    <tr>
                      <td>{{ \Carbon\Carbon::parse($r->updated_at)->format('d/m/Y') }}</td>
                      <td>{{ $r->kode }}</td>
                      <td>{{ $r->bahan_nama ?? '-' }}</td>
                      <td>
                        <span class="badge rounded-pill {{ $r->status_badge }}">{{ $r->status_label }}</span>
                      </td>
                      <td class="text-end">
                        <a href="{{ route('trial-rnd.edit', $r->id) }}" class="btn btn-outline-primary btn-sm">Edit</a>
                        {{-- Tombol Konfirmasi dipindah ke halaman Edit --}}
                      </td>
                    </tr>
                  @empty
                    <tr><td colspan="5" class="text-center text-muted">Tidak ada data</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>

          {{-- RIWAYAT --}}
          <div class="tab-pane" id="tab-history">
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>Tanggal</th>
                    <th>ID Permintaan</th>
                    <th>Nama Bahan</th>
                    <th>Hasil</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($history as $r)
                    <tr>
                      <td>{{ \Carbon\Carbon::parse($r->updated_at)->format('d/m/Y') }}</td>
                      <td>{{ $r->kode }}</td>
                      <td>{{ $r->bahan_nama ?? '-' }}</td>
                      <td>
                        <span class="badge rounded-pill {{ $r->status_badge }}">{{ $r->status_label }}</span>
                      </td>
                    </tr>
                  @empty
                    <tr><td colspan="4" class="text-center text-muted">Belum ada riwayat</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>
@endsection
