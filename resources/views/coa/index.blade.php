@extends('layouts.app')

@section('content')
<section class="app-user-list">
  <div class="row" id="basic-table">
    <div class="col-12">

      <div class="card">

        {{-- Header --}}
        <div class="card-header d-flex justify-content-between align-items-center">
          <div>
            <h4 class="card-title mb-0">COA</h4>
            <p class="mb-0 text-muted">
              Menampilkan batch yang Qty Batch-nya sudah dikonfirmasi dan sedang diproses COA.
            </p>
          </div>

          <a href="{{ route('coa.history') }}"
             class="btn btn-sm btn-outline-secondary">
            Riwayat COA
          </a>
        </div>

        {{-- Flash Message --}}
        @if(session('ok'))
          <div class="alert alert-success m-2">{{ session('ok') }}</div>
        @endif

        {{-- Filter --}}
        <div class="card-body border-bottom">
          <form class="row g-1" method="GET" action="{{ route('coa.index') }}">

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
                <option value="">Semua Status COA</option>
                <option value="pending" {{ ($status ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="done"    {{ ($status ?? '') === 'done'    ? 'selected' : '' }}>Selesai</option>
              </select>
            </div>

            <div class="col-md-2">
              <button class="btn btn-outline-primary w-100">Filter</button>
            </div>

          </form>
        </div>

        {{-- Tabel COA --}}
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
                <th>Status COA</th>
                <th>Status Review</th>
                <th style="max-width: 260px;">Catatan Review</th>
                <th class="text-center" style="width: 230px;">Aksi</th>
              </tr>
            </thead>

            <tbody>
            @forelse($rows as $idx => $row)
              @php
                // ===== Status COA =====
                $statusLabel = $row->status_coa ?? 'pending';
                switch ($statusLabel) {
                    case 'done':
                        $badgeCoa  = 'badge-light-success';
                        $statusCoa = 'Selesai';
                        break;
                    case 'pending':
                    default:
                        $badgeCoa  = 'badge-light-warning';
                        $statusCoa = 'Pending';
                        break;
                }

                // ===== Status Review =====
                $stRev = $row->status_review;
                if (!$stRev || $stRev === 'pending') {
                    // Belum benar-benar direview → jangan tampilkan badge apa pun
                    $badgeReview = '';
                    $statusRev   = '-';
                } else {
                    switch ($stRev) {
                        case 'released':
                            $badgeReview = 'badge-light-success'; $statusRev = 'Released'; break;
                        case 'hold':
                            $badgeReview = 'badge-light-warning'; $statusRev = 'Hold'; break;
                        case 'rejected':
                            $badgeReview = 'badge-light-danger';  $statusRev = 'Rejected'; break;
                        default:
                            $badgeReview = 'badge-light-secondary'; $statusRev = ucfirst($stRev);
                    }
                }

                // ===== Catatan Review =====
                $catatanFull  = trim($row->catatan_review ?? '');
                $catatanShort = $catatanFull ? \Illuminate\Support\Str::limit($catatanFull, 150) : '-';

                // tombol konfirmasi aktif kalau status_coa = 'done'
                $canConfirm = ($statusLabel === 'done');
              @endphp

              <tr>
                <td>{{ $rows->firstItem() + $idx }}</td>
                <td>{{ $row->kode_batch }}</td>
                <td>{{ $row->nama_produk }}</td>
                <td>{{ $row->bulan }}</td>
                <td>{{ $row->tahun }}</td>
                <td>{{ $row->wo_date ? $row->wo_date->format('d-m-Y') : '-' }}</td>
                <td>{{ $row->qty_batch }}</td>

                {{-- Tanggal QC Kirim COA --}}
                <td>
                  {{ $row->tgl_qc_kirim_coa
                        ? \Carbon\Carbon::parse($row->tgl_qc_kirim_coa)->format('d-m-Y')
                        : '-' }}
                </td>

                {{-- Tanggal QA Terima COA --}}
                <td>
                  {{ $row->tgl_qa_terima_coa
                        ? \Carbon\Carbon::parse($row->tgl_qa_terima_coa)->format('d-m-Y')
                        : '-' }}
                </td>

                {{-- Status COA --}}
                <td>
                  <span class="badge {{ $badgeCoa }}">{{ $statusCoa }}</span>
                </td>

                {{-- Status Review --}}
                <td>
                  @if($badgeReview)
                    <span class="badge {{ $badgeReview }}">{{ $statusRev }}</span>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>

                {{-- Catatan Review --}}
                <td style="max-width: 260px;">
                  @if($catatanFull && $stRev && $stRev !== 'pending')
                    <small class="text-muted d-block" title="{{ $catatanFull }}">
                      {{ $catatanShort }}
                    </small>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>

                {{-- Aksi --}}
                <td class="text-center">
                  <div class="d-grid gap-50">
                    {{-- Isi / Lihat COA --}}
                    <a href="{{ route('coa.edit', $row->id) }}"
                       class="btn btn-sm btn-outline-secondary w-100">
                      {{ $statusLabel === 'done' ? 'Lihat / Ubah COA' : 'Isi COA' }}
                    </a>

                    {{-- Konfirmasi COA (QA final, kirim ke review & riwayat) --}}
                    <form action="{{ route('coa.confirm', $row->id) }}"
                          method="POST"
                          onsubmit="return confirm('Konfirmasi COA sebagai SELESAI, kirim ke Review dan pindah ke Riwayat?');">
                      @csrf
                      <button type="submit"
                              class="btn btn-sm btn-success w-100"
                              {{ $canConfirm ? '' : 'disabled' }}>
                        Konfirmasi
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="13" class="text-center text-muted">
                  Belum ada data COA.
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
