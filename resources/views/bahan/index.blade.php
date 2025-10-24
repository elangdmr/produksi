@extends('layouts.app')

@section('content')
<section class="app-user-list">
  <div class="row"><div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <div>
          <h4 class="card-title mb-0">Master Bahan</h4>
          <small class="text-muted">Kelola daftar nama bahan untuk dipakai modul lain.</small>
        </div>
        <a href="{{ route('bahan.create') }}" class="btn btn-primary">+ Tambah Bahan</a>
      </div>

      <div class="card-body">
        @if(session('ok'))
          <div class="alert alert-success py-1 mb-1">{{ session('ok') }}</div>
        @endif

        <form class="row g-1 mb-1" method="get" action="{{ route('bahan.index') }}">
          <div class="col-md-6">
            <input class="form-control" name="q" value="{{ $q }}" placeholder="Cari nama / satuan / kategori...">
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
            <a href="{{ route('bahan.index') }}" class="btn btn-outline-dark w-100">Reset</a>
          </div>
        </form>

        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr class="text-muted">
                <th width="50">#</th>
                <th>Nama Bahan</th>
                <th width="150">Satuan Default</th>
                <th width="180">Kategori Default</th>
                <th width="160">Dibuat</th>
                <th width="130" class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($rows as $i => $b)
                <tr>
                  <td>{{ $rows->firstItem() + $i }}</td>
                  <td>{{ $b->nama }}</td>
                  <td>{{ $b->satuan_default }}</td>
                  <td>{{ $b->kategori_default }}</td>
                  <td>{{ $b->created_at?->format('d/m/Y') }}</td>
                  <td class="text-end">
                    <a href="{{ route('bahan.edit',$b->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                    <form method="POST" action="{{ route('bahan.destroy',$b->id) }}" class="d-inline"
                          onsubmit="return confirm('Hapus bahan ini?');">
                      @csrf @method('DELETE')
                      <button class="btn btn-sm btn-outline-danger">Hapus</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr><td colspan="6" class="text-center text-muted">Belum ada data.</td></tr>
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
