<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Form Laporan Hasil Penyerahan Produksi</title>
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #000; padding: 3px 5px; }
    th { text-align: center; }
    .no-border { border: none; }
    .text-center { text-align:center; }
    .text-right { text-align:right; }
  </style>
</head>
<body>

  <table class="no-border" style="width:100%; border:0; margin-bottom:10px;">
    <tr class="no-border">
      <td class="no-border" style="width:40%;">
        <strong>PT. SAMCO FARMA</strong><br>
        DEPARTEMEN PRODUKSI
      </td>
      <td class="no-border text-center" style="width:30%;">
        <strong>FORM LAPORAN HASIL<br>PENYERAHAN PRODUKSI</strong>
      </td>
      <td class="no-border text-right" style="width:30%;">
        Tanggal :
        @if($bulan && $tahun)
          {{ str_pad($bulan,2,'0',STR_PAD_LEFT) }} / {{ $tahun }}
        @else
          ................................
        @endif
        <br>
        No. Dokumen : SF/PRU-DG 032 {{-- contoh --}}
      </td>
    </tr>
  </table>

  <table>
    <thead>
      <tr>
        <th style="width:30px;">No</th>
        <th>Nama Produk</th>
        <th style="width:70px;">Batch No</th>
        <th style="width:70px;">Exp Date</th>
        <th style="width:80px;">Kemasan</th>
        <th>Isi</th>
        <th style="width:60px;">Jumlah</th>
        <th style="width:80px;">Total</th>
      </tr>
    </thead>
    <tbody>
      @php $no = 1; @endphp
      @foreach($rows as $row)
        <tr>
          <td class="text-center">{{ $no++ }}</td>
          <td>{{ $row->produksi->nama_produk ?? $row->nama_produk }}</td>
          <td class="text-center">{{ $row->no_batch }}</td>
          <td class="text-center">{{ $row->exp_date ? \Carbon\Carbon::parse($row->exp_date)->format('M-y') : '-' }}</td>
          <td class="text-center">{{ $row->kemasan ?? '-' }}</td>
          <td class="text-center">{{ $row->isi_kemasan ?? '-' }}</td>
          <td class="text-center">{{ $row->jumlah_kemasan ?? '-' }}</td>
          <td class="text-center">{{ $row->total_kemasan ?? '-' }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <br><br>

  <table class="no-border" style="width:100%; border:0;">
    <tr class="no-border">
      <td class="no-border text-center">MENGETAHUI<br><br><br><br>PRODUKSI</td>
      <td class="no-border text-center">MENERIMA<br><br><br><br>R&D</td>
    </tr>
  </table>

  <script>
    // auto buka dialog print kalau mau
    // window.print();
  </script>
</body>
</html>
