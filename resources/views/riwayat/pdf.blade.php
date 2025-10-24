<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>{{ $judul }} — {{ $kode }}</title>
  <style>
    @page { margin: 24px 28px; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color:#222; }
    h1 { font-size: 18px; margin: 0 0 4px 0; }
    .meta{ margin: 0 0 12px 0; }
    .meta div{ margin: 2px 0; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border:1px solid #ddd; padding:6px 8px; vertical-align: top; }
    th { background:#f5f5f5; text-align:left; }
    .small{ color:#666; font-size: 11px; }
  </style>
</head>
<body>
  <h1>{{ $judul }}</h1>
  <div class="meta">
    <div><strong>Kode PB:</strong> {{ $kode }}</div>
    <div><strong>Nama Bahan:</strong> {{ $bahan }}</div>
    <div><strong>Modul:</strong> {{ $modul }}</div>
    <div class="small">Dibuat: {{ \Carbon\Carbon::parse($generated)->format('d/m/Y H:i') }}</div>
  </div>

  <table>
    <thead>
      <tr>
        <th style="width:110px;">Tanggal</th>
        <th style="width:120px;">Modul</th>
        <th>Peristiwa</th>
        <th style="width:160px;">Status</th>
        <th>Keterangan</th>
      </tr>
    </thead>
    <tbody>
    @forelse($events as $e)
      <tr>
        <td>{{ \Carbon\Carbon::parse($e['tanggal'])->format('d/m/Y') }}</td>
        <td>{{ $e['modul'] }}</td>
        <td>{{ $e['peristiwa'] }}</td>
        <td>{{ $e['status'] ?? '-' }}</td>
        <td>{{ $e['keterangan'] ?? '-' }}</td>
      </tr>
    @empty
      <tr><td colspan="5" class="small" style="text-align:center;">Belum ada data.</td></tr>
    @endforelse
    </tbody>
  </table>
</body>
</html>
