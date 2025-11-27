@extends('layouts.app')

@section('content')
<section class="app-user-list">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card">

        @php
          $statusCoa = $batch->status_coa ?? 'pending';
          switch ($statusCoa) {
              case 'confirmed':
                  $badgeCoa = 'badge-light-primary';
                  $textCoa  = 'Confirmed';
                  break;
              case 'done':
                  $badgeCoa = 'badge-light-success';
                  $textCoa  = 'Selesai';
                  break;
              default:
                  $badgeCoa = 'badge-light-warning';
                  $textCoa  = 'Pending';
          }

          // Kalau sudah confirmed, form dikunci
          $isLocked = ($statusCoa === 'confirmed');
        @endphp

        <div class="card-header d-flex justify-content-between align-items-center">
          <div>
            <h4 class="card-title mb-0">COA</h4>
            <p class="mb-0 text-muted">
              Isi tanggal QC kirim COA dan tanggal QA terima COA untuk batch ini.
              @if($statusCoa)
                <br>
                <span class="badge {{ $badgeCoa }} mt-25">
                  Status COA: {{ $textCoa }}
                </span>
              @endif
            </p>
          </div>

          <a href="{{ route('coa.index') }}" class="btn btn-sm btn-outline-secondary">
            &laquo; Kembali
          </a>
        </div>

        @if(session('ok'))
          <div class="alert alert-success m-2">{{ session('ok') }}</div>
        @endif

        @if($errors->any())
          <div class="alert alert-danger m-2">{{ $errors->first() }}</div>
        @endif

        @if($isLocked)
          <div class="alert alert-info m-2">
            COA untuk batch ini sudah <strong>dikonfirmasi</strong> oleh QA dan tersimpan di
            <strong>Riwayat COA</strong>, sehingga tidak dapat diubah lagi.
          </div>
        @endif

        <div class="card-body">

          {{-- Info batch --}}
          <div class="mb-1">
            <label class="form-label">Nama Produk</label>
            <input class="form-control" value="{{ $batch->nama_produk }}" disabled>
          </div>

          <div class="row mb-1">
            <div class="col-md-4 mb-1">
              <label class="form-label">Kode Batch</label>
              <input class="form-control" value="{{ $batch->kode_batch }}" disabled>
            </div>
            <div class="col-md-2 mb-1">
              <label class="form-label">Bulan</label>
              <input class="form-control" value="{{ $batch->bulan }}" disabled>
            </div>
            <div class="col-md-2 mb-1">
              <label class="form-label">Tahun</label>
              <input class="form-control" value="{{ $batch->tahun }}" disabled>
            </div>
            <div class="col-md-4 mb-1">
              <label class="form-label">WO Date</label>
              <input class="form-control"
                     value="{{ $batch->wo_date ? $batch->wo_date->format('d-m-Y') : '-' }}"
                     disabled>
            </div>
          </div>

          {{-- Form COA --}}
          <form action="{{ route('coa.update', $batch->id) }}" method="POST">
            @csrf

            <div class="mb-1">
              <label class="form-label">Tanggal QC Kirim COA ke QA</label>
              <input type="date"
                     name="tgl_qc_kirim_coa"
                     class="form-control"
                     value="{{ old('tgl_qc_kirim_coa', optional($batch->tgl_qc_kirim_coa)->toDateString()) }}"
                     {{ $isLocked ? 'disabled' : '' }}>
            </div>

            <div class="mb-1">
              <label class="form-label">Tanggal QA Terima COA</label>
              <input type="date"
                     name="tgl_qa_terima_coa"
                     class="form-control"
                     value="{{ old('tgl_qa_terima_coa', optional($batch->tgl_qa_terima_coa)->toDateString()) }}"
                     {{ $isLocked ? 'disabled' : '' }}>
            </div>

            <div class="mt-2 d-flex justify-content-between">
              <a href="{{ route('coa.index') }}" class="btn btn-outline-secondary">
                Kembali
              </a>

              @unless($isLocked)
                <button type="submit" class="btn btn-primary">
                  Simpan COA
                </button>
              @endunless
            </div>

          </form>

        </div>

      </div>
    </div>
  </div>
</section>
@endsection
