@extends('layouts.app')

@section('content')
<section class="app-user-list">
  <div class="row">
    <div class="col-12">
      <div class="card">

        {{-- ========= Header + Filter Toolbar ========= --}}
        <div class="card-header border-bottom-0 pb-0">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-50">
            <div>
              <h4 class="card-title mb-25">Riwayat Proses</h4>
              <small class="text-muted">Gabungan semua modul, diurutkan dari terbaru.</small>
            </div>

            {{-- Toggle filter (mobile) --}}
            <button class="btn btn-outline-primary d-lg-none" type="button"
                    data-bs-toggle="collapse" data-bs-target="#filterBar" aria-expanded="true">
              <i data-feather="sliders"></i><span class="ms-50">Filter</span>
            </button>
          </div>

          <div id="filterBar" class="collapse show">
            <form class="row g-1 align-items-center filter-toolbar" method="GET" action="{{ route('riwayat.index') }}">
              {{-- Search --}}
              <div class="col-12 col-lg-4">
                <div class="input-group">
                  <span class="input-group-text"><i data-feather="search" class="text-muted"></i></span>
                  <input type="text" class="form-control" name="q"
                         value="{{ request('q','') }}" placeholder="Cari kode / bahan / status">
                </div>
              </div>

              {{-- Modul --}}
              <div class="col-12 col-lg-3">
                <div class="input-group">
                  <span class="input-group-text"><i data-feather="layers" class="text-muted"></i></span>
                  <select class="form-select" name="modul">
                    <option value="">Semua Modul</option>
                    @foreach($modulList as $m)
                      <option value="{{ $m }}" {{ request('modul')===$m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              {{-- Dari tanggal --}}
              <div class="col-6 col-lg-2">
                <div class="input-group">
                  <span class="input-group-text"><i data-feather="calendar" class="text-muted"></i></span>
                  <input type="date" class="form-control" name="from" value="{{ request('from') }}">
                </div>
              </div>

              {{-- Sampai tanggal --}}
              <div class="col-6 col-lg-2">
                <div class="input-group">
                  <span class="input-group-text"><i data-feather="calendar" class="text-muted"></i></span>
                  <input type="date" class="form-control" name="to" value="{{ request('to') }}">
                </div>
              </div>

              {{-- Tombol --}}
              <div class="col-12 col-lg-1 d-grid">
                <button class="btn btn-primary">
                  <i data-feather="filter"></i><span class="ms-50">Filter</span>
                </button>
              </div>

              <div class="col-12 d-flex justify-content-end">
                <a href="{{ route('riwayat.index') }}" class="btn btn-outline-secondary btn-sm">
                  <i data-feather="rotate-ccw"></i><span class="ms-50">Reset</span>
                </a>
              </div>
            </form>
          </div>
        </div>

        {{-- ========= /Header + Filter ========= --}}

        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th style="width:135px">Tanggal</th>
                  <th style="width:105px">ID</th>
                  <th>Nama Bahan</th>
                  <th style="width:120px">Modul</th>
                  <th>Peristiwa</th>
                  <th style="width:160px">Status</th>
                  <th>Keterangan</th>
                  <th style="width:80px">Aksi</th>
                </tr>
              </thead>
              <tbody>
              @forelse($events as $e)
                <tr>
                  <td>{{ \Carbon\Carbon::parse($e['tanggal'])->format('d/m/Y') }}</td>
                  <td><span class="badge rounded-pill bg-secondary">{{ $e['kode'] }}</span></td>
                  <td>{{ $e['bahan'] }}</td>
                  <td>{{ $e['modul'] }}</td>
                  <td>{{ $e['peristiwa'] }}</td>
                  <td>
                    @php
                      $st = (string)($e['status'] ?? '');
                      $badge = 'bg-light-secondary';
                      if (stripos($st,'Lengkap')!==false || stripos($st,'Approved')!==false || stripos($st,'Lulus')!==false) $badge='bg-success';
                      elseif (stripos($st,'Belum')!==false) $badge='bg-warning text-dark';
                      elseif (stripos($st,'Tidak')!==false || stripos($st,'Rejected')!==false || stripos($st,'Ditolak')!==false) $badge='bg-danger';
                    @endphp
                    <span class="badge {{ $badge }}">{{ $st !== '' ? $st : '-' }}</span>
                  </td>
                  <td>{{ $e['keterangan'] ?? '-' }}</td>
                  <td>
                    @if(!empty($e['link']))
                      <a class="btn btn-sm btn-outline-primary" href="{{ $e['link'] }}">Detail</a>
                    @endif
                  </td>
                </tr>
              @empty
                <tr><td colspan="8" class="text-center text-muted">Belum ada data.</td></tr>
              @endforelse
              </tbody>
            </table>
          </div>

          {{-- Optional: pagination support jika $events adalah paginator --}}
          @if($events instanceof \Illuminate\Pagination\AbstractPaginator)
            <div class="mt-1">
              {{ $events->appends(request()->query())->links() }}
            </div>
          @endif
        </div>

      </div>
    </div>
  </div>
</section>

{{-- styling kecil untuk toolbar --}}
<style>
  .filter-toolbar .input-group-text{background:#fff;border-right:0}
  .filter-toolbar .form-control,.filter-toolbar .form-select{border-left:0}
  .filter-toolbar .input-group{border:1px solid #e9ecef;border-radius:.428rem;overflow:hidden;background:#fff}
</style>
@endsection
