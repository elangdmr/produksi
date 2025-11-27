@extends('layouts.app')

@php
  $title = $isEdit ? 'Edit Produk Produksi' : 'Tambah Produk Produksi';
@endphp

@section('content')
<section class="app-user-list">
  <div class="row"><div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">{{ $title }}</h4>
        <a href="{{ route('produksi.index') }}" class="btn btn-outline-secondary">Kembali</a>
      </div>

      <div class="card-body">
        <form method="POST"
              action="{{ $isEdit ? route('produksi.update',$produk->id) : route('produksi.store') }}">
          @csrf
          @if($isEdit) @method('PUT') @endif

          <div class="row g-1">
            {{-- Kode Produk --}}
            <div class="col-md-4">
              <label class="form-label">Kode Produk <span class="text-danger">*</span></label>
              <input type="text" name="kode_produk" class="form-control"
                     value="{{ old('kode_produk', $produk->kode_produk ?? '') }}" required>
              @error('kode_produk')<div class="text-danger mt-25">{{ $message }}</div>@enderror
            </div>

            {{-- Nama Produk --}}
            <div class="col-md-8">
              <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
              <input type="text" name="nama_produk" class="form-control"
                     value="{{ old('nama_produk', $produk->nama_produk ?? '') }}" required>
              @error('nama_produk')<div class="text-danger mt-25">{{ $message }}</div>@enderror
            </div>

            {{-- Bentuk Sediaan --}}
            <div class="col-md-4 mt-1">
              <label class="form-label">Bentuk Sediaan <span class="text-danger">*</span></label>
              <select name="bentuk_sediaan" class="form-select" required>
                @foreach($bentukOptions as $opt)
                  <option value="{{ $opt }}"
                    {{ old('bentuk_sediaan', $produk->bentuk_sediaan ?? '') == $opt ? 'selected' : '' }}>
                    {{ $opt }}
                  </option>
                @endforeach
              </select>
              @error('bentuk_sediaan')<div class="text-danger mt-25">{{ $message }}</div>@enderror
            </div>

            {{-- Tipe Alur Produksi --}}
            <div class="col-md-4 mt-1">
              <label class="form-label">Tipe Alur Produksi <span class="text-danger">*</span></label>
              <select name="tipe_alur" class="form-select" required>
                @foreach($tipeAlurOptions as $key => $label)
                  @php
                    // boleh kirim array assoc ['TABLET_SALUT' => 'Tablet + Coating', ...]
                    $value = is_int($key) ? $label : $key;
                    $text  = is_int($key) ? $label : $label;
                  @endphp
                  <option value="{{ $value }}"
                    {{ old('tipe_alur', $produk->tipe_alur ?? '') == $value ? 'selected' : '' }}>
                    {{ $text }}
                  </option>
                @endforeach
              </select>
              @error('tipe_alur')<div class="text-danger mt-25">{{ $message }}</div>@enderror
            </div>

            {{-- Leadtime Target --}}
            <div class="col-md-2 mt-1">
              <label class="form-label">Leadtime Target (hari)</label>
              <input type="number" min="0" name="leadtime_target" class="form-control"
                     value="{{ old('leadtime_target', $produk->leadtime_target ?? '') }}">
              @error('leadtime_target')<div class="text-danger mt-25">{{ $message }}</div>@enderror
            </div>

            {{-- Status Aktif --}}
            <div class="col-md-2 mt-4 d-flex align-items-center">
              @php
                $isAktif = old('is_aktif', ($produk->is_aktif ?? true)) ? true : false;
              @endphp
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="is_aktif"
                       name="is_aktif" value="1" {{ $isAktif ? 'checked' : '' }}>
                <label class="form-check-label" for="is_aktif">Aktif</label>
              </div>
            </div>
          </div>

          <div class="mt-2 d-grid">
            <button class="btn btn-success">Simpan</button>
          </div>
        </form>
      </div>

    </div>
  </div></div>
</section>
@endsection
