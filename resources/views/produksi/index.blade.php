@extends('layouts.app')

@section('content')
<section class="app-user-list">
  <div class="row"><div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <div>
          <h4 class="card-title mb-0">Master Produk Produksi</h4>
          <small class="text-muted">Kelola daftar produk & tipe alur produksi.</small>
        </div>
        <a href="{{ route('produksi.create') }}" class="btn btn-primary">+ Tambah Produk</a>
      </div>

      <div class="card-body">
        @if(session('ok'))
          <div class="alert alert-success py-1 mb-1">{{ session('ok') }}</div>
        @endif

        {{-- Form filter / pencarian --}}
        <form class="row g-1 mb-1" method="get" action="{{ route('produksi.index') }}">
          <div class="col-md-6">
            <input class="form-control" name="q" value="{{ $q }}"
                   placeholder="Cari kode / nama produk / bentuk sediaan / tipe alur...">
          </div>
          <div class="col-md-2">
            <select class="form-select" name="per_page" onchange="this.form.submit()">
              @foreach([10,15,25,50,100] as $n)
                <option value="{{ $n }}" {{ request('per_page',15)==$n?'selected':'' }}>{{ $n }}/hal</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <button class="btn btn-outline-secondary w-100">Cari</button>
          </div>
          <div class="col-md-2">
            <a href="{{ route('produksi.index') }}" class="btn btn-outline-dark w-100">Reset</a>
          </div>
        </form>

        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr class="text-muted">
                <th width="50">#</th>
                <th width="120">Kode Produk</th>
                <th>Nama Produk</th>
                <th width="150">Bentuk Sediaan</th>
                <th width="170">Tipe Alur Produksi</th>
                <th width="130">Leadtime Target (hari)</th>
                <th width="120">Status</th>
                <th width="160">Dibuat</th>
                <th width="130" class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($rows as $i => $p)
                <tr>
                  <td>{{ $rows->firstItem() + $i }}</td>
                  <td>{{ $p->kode_produk }}</td>
                  <td>{{ $p->nama_produk }}</td>
                  <td>{{ $p->bentuk_sediaan }}</td>
                  <td>{{ $p->tipe_alur }}</td>
                  <td>{{ $p->leadtime_target ?? '-' }}</td>
                  <td>
                    @if($p->is_aktif)
                      <span class="badge bg-success">Aktif</span>
                    @else
                      <span class="badge bg-secondary">Nonaktif</span>
                    @endif
                  </td>
                  <td>{{ $p->created_at?->format('d/m/Y') }}</td>
                  <td class="text-end">
                    <a href="{{ route('produksi.edit',$p->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                    <form method="POST" action="{{ route('produksi.destroy',$p->id) }}" class="d-inline"
                          onsubmit="return confirm('Hapus produk ini?');">
                      @csrf @method('DELETE')
                      <button class="btn btn-sm btn-outline-danger">Hapus</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr><td colspan="9" class="text-center text-muted">Belum ada data.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-1">
          {{ $rows->links() }}
        </div>
      </div>
    </div>
  </div></div>
</section>
@endsection
