@extends('layouts.app')

@section('content')
<section class="app-user-list">
  <div class="row" id="basic-table">
    <div class="col-12">
      <div class="card">

        {{-- HEADER --}}
        <div class="card-header d-flex justify-content-between align-items-center">
          <div>
            <h4 class="card-title mb-0">Qty Batch (Setelah Secondary Pack)</h4>
            <p class="mb-0 text-muted">
              Menampilkan batch yang sudah selesai Secondary Pack dan memiliki Qty Batch.
              Batch yang sudah <strong>dikonfirmasi</strong> akan pindah ke halaman
              <em>Riwayat Qty Batch</em>.
            </p>
          </div>

          <a href="{{ route('qty-batch.history') }}"
             class="btn btn-sm btn-outline-secondary">
            Riwayat Qty Batch
          </a>
        </div>

        {{-- FLASH MESSAGE --}}
        @if(session('ok'))
          <div class="alert alert-success m-2">
            {{ session('ok') }}
          </div>
        @endif

        {{-- FILTER BAR --}}
        <div class="card-body border-bottom">
          <form method="GET" action="{{ route('qty-batch.index') }}" class="row g-1">

            {{-- search --}}
            <div class="col-md-3">
              <input type="text"
                     name="q"
                     class="form-control"
                     placeholder="Cari produk / no batch / kode batch..."
                     value="{{ $q ?? '' }}">
            </div>

            {{-- bulan --}}
            <div class="col-md-2">
              <select name="bulan" class="form-control">
                <option value="">Semua Bulan</option>
                @for($m = 1; $m <= 12; $m++)
                  @php $val = (string) $m; @endphp
                  <option value="{{ $val }}"
                    {{ (string)($bulan ?? '') === $val ? 'selected' : '' }}>
                    {{ str_pad($m, 2, '0', STR_PAD_LEFT) }}
                  </option>
                @endfor
              </select>
            </div>

            {{-- tahun --}}
            <div class="col-md-2">
              <input type="number"
                     name="tahun"
                     class="form-control"
                     placeholder="Tahun"
                     value="{{ $tahun ?? '' }}">
            </div>

            {{-- status (hanya pending / rejected di halaman aktif) --}}
            <div class="col-md-2">
              @php $currentStatus = $status ?? ''; @endphp
              <select name="status" class="form-control">
                <option value="">Semua Status</option>
                <option value="pending"  {{ $currentStatus === 'pending'  ? 'selected' : '' }}>Pending</option>
                <option value="rejected" {{ $currentStatus === 'rejected' ? 'selected' : '' }}>Ditolak</option>
              </select>
            </div>

            {{-- tombol filter --}}
            <div class="col-md-2">
              <button class="btn btn-outline-primary w-100">
                Filter
              </button>
            </div>
          </form>
        </div>

        {{-- TABEL --}}
        <div class="table-responsive">
          <table class="table table-striped mb-0 align-middle">
            <thead>
              <tr>
                <th style="width: 40px;">#</th>
                <th>Kode Batch</th>
                <th>Nama Produk</th>
                <th>Bulan</th>
                <th>Tahun</th>
                <th>WO Date</th>
                <th>Secondary Pack (Mulai)</th>
                <th>Secondary Pack (Selesai)</th>
                <th class="text-end">Qty Batch</th>
                <th>Status</th>
                <th class="text-center" style="width: 200px;">Aksi</th>
              </tr>
            </thead>

            <tbody>
            @forelse($rows as $idx => $row)
              @php
                $statusLabel = $row->status_qty_batch ?? 'pending';

                switch ($statusLabel) {
                  case 'confirmed':
                    $badgeClass = 'badge-light-success';
                    $statusText = 'Dikonfirmasi';
                    break;
                  case 'rejected':
                    $badgeClass = 'badge-light-danger';
                    $statusText = 'Ditolak';
                    break;
                  default:
                    $badgeClass = 'badge-light-warning';
                    $statusText = 'Pending';
                }
              @endphp

              <tr>
                {{-- nomor --}}
                <td>{{ $rows->firstItem() + $idx }}</td>

                {{-- kode & nama --}}
                <td>{{ $row->kode_batch }}</td>
                <td>{{ $row->produksi->nama_produk ?? $row->nama_produk }}</td>

                {{-- bulan & tahun --}}
                <td>{{ $row->bulan }}</td>
                <td>{{ $row->tahun }}</td>

                {{-- tanggal: diasumsikan sudah cast ke date di model --}}
                <td>{{ $row->wo_date ? $row->wo_date->format('d-m-Y') : '-' }}</td>
                <td>{{ $row->tgl_mulai_secondary_pack_1 ? $row->tgl_mulai_secondary_pack_1->format('d-m-Y') : '-' }}</td>
                <td>{{ $row->tgl_secondary_pack_1 ? $row->tgl_secondary_pack_1->format('d-m-Y') : '-' }}</td>

                {{-- qty --}}
                <td class="text-end">
                  {{ number_format((float) $row->qty_batch, 2) }}
                </td>

                {{-- status badge --}}
                <td>
                  <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                </td>

                {{-- aksi --}}
                <td class="text-center">
                  <div class="d-grid gap-50">

                    {{-- tombol konfirmasi: hanya saat belum confirmed & belum rejected --}}
                    @if($statusLabel === 'pending')
                      <form action="{{ route('qty-batch.confirm', $row->id) }}"
                            method="POST"
                            onsubmit="return confirm('Konfirmasi Qty Batch ini?');">
                        @csrf
                        <button type="submit"
                                class="btn btn-sm btn-primary w-100">
                          Konfirmasi
                        </button>
                      </form>

                      <form action="{{ route('qty-batch.reject', $row->id) }}"
                            method="POST"
                            onsubmit="return confirm('Tolak Qty Batch ini?');">
                        @csrf
                        <button type="submit"
                                class="btn btn-sm btn-outline-danger w-100">
                          Tolak
                        </button>
                      </form>
                    @else
                      <span class="text-muted small">Tidak ada aksi</span>
                    @endif

                  </div>
                </td>
              </tr>

            @empty
              <tr>
                <td colspan="11" class="text-center text-muted">
                  Belum ada data Qty Batch yang memenuhi kriteria.
                </td>
              </tr>
            @endforelse
            </tbody>
          </table>
        </div>

        {{-- PAGINATION --}}
        <div class="card-body">
          {{ $rows->withQueryString()->links() }}
        </div>

      </div>
    </div>
  </div>
</section>
@endsection
