@extends('layouts.app')

@section('content')
<section class="app-user-list">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">
          <div>
            <h4 class="card-title mb-0">Edit Coating</h4>
            <p class="mb-0 text-muted">
              Ubah tanggal mulai & selesai Coating untuk batch ini.
            </p>
          </div>

          <a href="{{ route('coating.index') }}" class="btn btn-sm btn-outline-secondary">
            &laquo; Kembali ke Daftar Coating
          </a>
        </div>

        @if(session('success'))
          <div class="alert alert-success m-2">{{ session('success') }}</div>
        @endif

        @if($errors->any())
          <div class="alert alert-danger m-2">
            {{ $errors->first() }}
          </div>
        @endif

        <div class="card-body">
          {{-- Info batch (readonly) --}}
          <div class="row mb-1">
            <div class="col-md-6 mb-1">
              <label class="form-label">Nama Produk</label>
              <input type="text"
                     class="form-control"
                     value="{{ $batch->produksi->nama_produk ?? $batch->nama_produk }}"
                     disabled>
            </div>
            <div class="col-md-3 mb-1">
              <label class="form-label">No Batch</label>
              <input type="text"
                     class="form-control"
                     value="{{ $batch->no_batch }}"
                     disabled>
            </div>
            <div class="col-md-3 mb-1">
              <label class="form-label">Kode Batch</label>
              <input type="text"
                     class="form-control"
                     value="{{ $batch->kode_batch }}"
                     disabled>
            </div>
          </div>

          <div class="row mb-1">
            <div class="col-md-2 mb-1">
              <label class="form-label">Bulan</label>
              <input type="text"
                     class="form-control"
                     value="{{ $batch->bulan }}"
                     disabled>
            </div>
            <div class="col-md-2 mb-1">
              <label class="form-label">Tahun</label>
              <input type="text"
                     class="form-control"
                     value="{{ $batch->tahun }}"
                     disabled>
            </div>
            <div class="col-md-4 mb-1">
              <label class="form-label">WO Date</label>
              <input type="text"
                     class="form-control"
                     value="{{ $batch->wo_date ? $batch->wo_date->format('d-m-Y') : '-' }}"
                     disabled>
            </div>
            <div class="col-md-4 mb-1">
              <label class="form-label">Expected Date</label>
              <input type="text"
                     class="form-control"
                     value="{{ $batch->expected_date ? $batch->expected_date->format('d-m-Y') : '-' }}"
                     disabled>
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-md-4 mb-1">
              <label class="form-label">Tgl Tableting</label>
              <input type="text"
                     class="form-control"
                     value="{{ $batch->tgl_tableting ? $batch->tgl_tableting->format('d-m-Y') : '-' }}"
                     disabled>
            </div>
          </div>

          {{-- Form edit tanggal coating --}}
          <form action="{{ route('coating.store', $batch) }}" method="POST">
            @csrf

            <div class="row mb-1">
              <div class="col-md-6 mb-1">
                <label class="form-label">Tanggal Mulai Coating</label>
                <input type="date"
                       name="tgl_mulai_coating"
                       class="form-control"
                       value="{{ old('tgl_mulai_coating', optional($batch->tgl_mulai_coating)->format('Y-m-d')) }}">
              </div>

              <div class="col-md-6 mb-1">
                <label class="form-label">Tanggal Coating (Selesai)</label>
                <input type="date"
                       name="tgl_coating"
                       class="form-control"
                       value="{{ old('tgl_coating', optional($batch->tgl_coating)->format('Y-m-d')) }}">
              </div>
            </div>

            <div class="mt-2 d-flex justify-content-between">
              <a href="{{ route('coating.index') }}" class="btn btn-outline-secondary">
                Kembali
              </a>

              <button type="submit" class="btn btn-primary">
                Simpan Tanggal Coating
              </button>
            </div>
          </form>
        </div>

      </div>
    </div>
  </div>
</section>
@endsection
