@extends('layouts.app')

@section('content')
<section class="app-user-list">
  <div class="row" id="basic-table">
    <div class="col-12">

      <div class="card">

        {{-- HEADER --}}
        <div class="card-header d-flex justify-content-between align-items-center">
          <div>
            <h4 class="card-title mb-0">Review After Secondary Pack</h4>
            <p class="mb-0 text-muted">
              Menampilkan batch yang <strong>Qty Batch-nya sudah dikonfirmasi</strong>.
              Kolom Job Sheet QC, Sampling, dan COA menunjukkan progres masing-masing.
              Di sini QA dapat melakukan <strong>Hold / Release / Reject</strong>.
            </p>
          </div>

          <a href="{{ route('review.history') }}"
             class="btn btn-sm btn-outline-secondary">
            Riwayat Review
          </a>
        </div>

        @if (session('ok'))
          <div class="alert alert-success m-2">{{ session('ok') }}</div>
        @endif

        {{-- FILTER --}}
        <div class="card-body border-bottom">
          <form method="GET" class="row g-1">

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
                  <option value="{{ $i }}" {{ (string)($bulan ?? '') === (string)$i ? 'selected' : '' }}>
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
                <option value="">Semua Status Review (aktif)</option>
                <option value="pending"  {{ ($status ?? '') === 'pending'  ? 'selected' : '' }}>Pending</option>
                <option value="hold"     {{ ($status ?? '') === 'hold'     ? 'selected' : '' }}>Hold</option>
                <option value="released" {{ ($status ?? '') === 'released' ? 'selected' : '' }}>Released</option>
                <option value="rejected" {{ ($status ?? '') === 'rejected' ? 'selected' : '' }}>Rejected</option>
              </select>
            </div>

            <div class="col-md-2">
              <button class="btn btn-outline-primary w-100">Filter</button>
            </div>

          </form>
        </div>

        {{-- TABEL --}}
        <div class="table-responsive-sm">
          <table class="table mb-0 align-middle">
            <thead>
              <tr>
                <th>#</th>
                <th>Kode Batch</th>
                <th>Nama Produk</th>
                <th>Bulan</th>
                <th>Tahun</th>
                <th>Qty Batch</th>
                <th>Job Sheet QC</th>
                <th>Sampling</th>
                <th>COA</th>
                <th>Status Review</th>
                <th style="width: 180px;" class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($rows as $idx => $row)

                @php
                  $statusReview = $row->status_review ?? 'pending';
                  switch ($statusReview) {
                    case 'released':
                      $badgeClass = 'badge-light-success';
                      $statusText = 'Released';
                      break;
                    case 'hold':
                      $badgeClass = 'badge-light-warning';
                      $statusText = 'Hold';
                      break;
                    case 'rejected':
                      $badgeClass = 'badge-light-danger';
                      $statusText = 'Rejected';
                      break;
                    default:
                      $badgeClass = 'badge-light-secondary';
                      $statusText = 'Pending';
                  }

                  $isFinal = in_array($statusReview, ['released', 'rejected'], true);
                @endphp

                <tr>
                  <td>{{ $rows->firstItem() + $idx }}</td>
                  <td>{{ $row->kode_batch }}</td>
                  <td>{{ $row->nama_produk }}</td>
                  <td>{{ $row->bulan }}</td>
                  <td>{{ $row->tahun }}</td>

                  {{-- ringkasan Qty --}}
                  <td>
                    {{ $row->qty_batch ?? '-' }}
                    <br>
                    <small class="text-muted">{{ $row->status_qty_batch ?? '-' }}</small>
                  </td>

                  {{-- status step sebelumnya --}}
                  <td>{{ $row->status_jobsheet ?? '-' }}</td>
                  <td>{{ $row->status_sampling ?? '-' }}</td>
                  <td>{{ $row->status_coa ?? '-' }}</td>

                  {{-- status review --}}
                  <td>
                    <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                    @if($row->tgl_review)
                      <br><small class="text-muted">{{ $row->tgl_review }}</small>
                    @endif
                  </td>

                  {{-- AKSI QA --}}
                  <td class="text-center">
                    @if ($isFinal)
                      <span class="badge bg-light text-muted">Tidak ada aksi</span>
                    @else
                      <div class="btn-group">
                        <button type="button"
                                class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">
                          Aksi QA
                        </button>
                        <ul class="dropdown-menu">

                          {{-- HOLD -> buka modal --}}
                          @if($statusReview !== 'hold')
                            <li>
                              <button type="button"
                                      class="dropdown-item text-warning btn-hold-review"
                                      data-id="{{ $row->id }}"
                                      data-kode="{{ $row->kode_batch }}"
                                      data-produk="{{ $row->nama_produk }}">
                                Hold
                              </button>
                            </li>
                          @endif

                          {{-- RELEASE --}}
                          @if($statusReview !== 'released')
                            <li>
                              <form method="POST"
                                    action="{{ route('review.release', $row->id) }}"
                                    onsubmit="return confirm('Release batch ini?');">
                                @csrf
                                <input type="hidden" name="catatan_review"
                                       value="Released oleh QA pada {{ now()->format('d-m-Y') }}">
                                <button type="submit" class="dropdown-item text-success">
                                  Release
                                </button>
                              </form>
                            </li>
                          @endif

                          {{-- REJECT --}}
                          @if($statusReview !== 'rejected')
                            <li>
                              <form method="POST"
                                    action="{{ route('review.reject', $row->id) }}"
                                    onsubmit="return confirm('Yakin REJECT batch ini?');">
                                @csrf
                                <input type="hidden" name="catatan_review"
                                       value="Rejected oleh QA pada {{ now()->format('d-m-Y') }}">
                                <button type="submit" class="dropdown-item text-danger">
                                  Reject
                                </button>
                              </form>
                            </li>
                          @endif

                        </ul>
                      </div>
                    @endif
                  </td>
                </tr>

              @empty
                <tr>
                  <td colspan="11" class="text-center text-muted">
                    Belum ada batch yang siap direview.
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

{{-- MODAL HOLD REVIEW --}}
<div class="modal fade" id="modalHoldReview" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">

      <form method="POST" id="formHoldReview">
        @csrf

        <div class="modal-header">
          <h5 class="modal-title">Hold Batch</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"
                  aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <p class="mb-1 text-muted" id="holdInfoBatch"></p>

          {{-- Kembalikan ke mana --}}
          <div class="mb-1">
            <label class="form-label d-block">Kembalikan ke</label>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="return_to"
                     id="return_jobsheet" value="jobsheet" checked>
              <label class="form-check-label" for="return_jobsheet">
                Job Sheet QC
              </label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="return_to"
                     id="return_coa" value="coa">
              <label class="form-check-label" for="return_coa">
                COA QC/QA
              </label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="return_to"
                     id="return_both" value="both">
              <label class="form-check-label" for="return_both">
                Job Sheet &amp; COA
              </label>
            </div>
          </div>

          {{-- Status dokumen --}}
          <div class="mb-1">
            <label class="form-label">Status Dokumen</label>
            <select name="doc_status" class="form-control">
              <option value="belum_lengkap">Dokumen belum lengkap</option>
              <option value="lengkap">Dokumen lengkap (perlu cek ulang)</option>
            </select>
          </div>

          {{-- Catatan tambahan --}}
          <div class="mb-1">
            <label class="form-label">Catatan (opsional)</label>
            <textarea name="catatan_review" class="form-control" rows="3"
                      placeholder="Contoh: Sertakan lampiran hasil analisa COA, tanda tangan QA, dsb."></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary"
                  data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-warning">Simpan Hold</button>
        </div>

      </form>

    </div>
  </div>
</div>

{{-- SCRIPT UNTUK MODAL HOLD --}}
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.btn-hold-review');
    const modalEl = document.getElementById('modalHoldReview');
    const infoEl  = document.getElementById('holdInfoBatch');
    const form    = document.getElementById('formHoldReview');

    if (!modalEl || !buttons.length) return;

    const modal = new bootstrap.Modal(modalEl);

    buttons.forEach(btn => {
      btn.addEventListener('click', function () {
        const id     = this.dataset.id;
        const kode   = this.dataset.kode;
        const produk = this.dataset.produk;

        infoEl.textContent = `Batch ${kode} – ${produk}`;

        // set action form ke route review.hold dengan batch id
        const baseAction = "{{ route('review.hold', ['batch' => '__ID__']) }}";
        form.action = baseAction.replace('__ID__', id);

        // reset form
        form.reset();
        document.getElementById('return_jobsheet').checked = true;

        modal.show();
      });
    });
  });
</script>
@endsection
