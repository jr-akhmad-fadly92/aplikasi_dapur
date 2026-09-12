<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Gizi Harian</title>
</head>
<body>
    <h2>Laporan Data Nutrisi Menu</h2>
    <p><strong>Menu:</strong> {{ $menu->menu ?? 'N/A' }}</p>
    <p><strong>Tanggal Kirim:</strong> {{ \Carbon\Carbon::parse($menu->tanggal_kirim ?? now())->translatedFormat('d F Y') }}</p>
    
    <br/>
    
    <h3>Komposisi Menu</h3>
    <table border="1" cellpadding="10">
        <tr>
            <td><strong>Karbohidrat</strong></td>
            <td>{{ $karbohidrat->nama_resep ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Lauk Protein</strong></td>
            <td>{{ $protein->nama_resep ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Sayur</strong></td>
            <td>{{ $sayur->nama_resep ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Buah</strong></td>
            <td>{{ $buah->nama_resep ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Susu / Pendamping</strong></td>
            <td>{{ $susu->nama_resep ?? '-' }}</td>
        </tr>
    </table>
    
    <br/>
    
    <h3>Data Nutrisi (Akumulasi + Per Komponen)</h3>
    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>Komponen</th>
                <th>Energi (kkal)</th>
                <th>Protein (g)</th>
                <th>Lemak (g)</th>
                <th>Karbohidrat (g)</th>
                <th>Serat (g)</th>
                <th>Natrium (mg)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Akumulasi Total</strong></td>
                <td>{{ number_format((float) ($gizi->energi ?? 0), 2, ',', '.') }}</td>
                <td>{{ number_format((float) ($gizi->protein ?? 0), 2, ',', '.') }}</td>
                <td>{{ number_format((float) ($gizi->lemak ?? 0), 2, ',', '.') }}</td>
                <td>{{ number_format((float) ($gizi->karbohidrat ?? 0), 2, ',', '.') }}</td>
                <td>{{ number_format((float) ($gizi->serat ?? 0), 2, ',', '.') }}</td>
                <td>{{ number_format((float) ($gizi->natrium ?? 0), 2, ',', '.') }}</td>
            </tr>
            @foreach($componentBreakdown as $component)
                <tr>
                    <td><strong>{{ $component['label'] }}</strong></td>
                    <td>{{ number_format((float) ($component['totals']['energi'] ?? 0), 2, ',', '.') }}</td>
                    <td>{{ number_format((float) ($component['totals']['protein'] ?? 0), 2, ',', '.') }}</td>
                    <td>{{ number_format((float) ($component['totals']['lemak'] ?? 0), 2, ',', '.') }}</td>
                    <td>{{ number_format((float) ($component['totals']['karbohidrat'] ?? 0), 2, ',', '.') }}</td>
                    <td>{{ number_format((float) ($component['totals']['serat'] ?? 0), 2, ',', '.') }}</td>
                    <td>{{ number_format((float) ($component['totals']['natrium'] ?? 0), 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <br/>
    <p><em>Laporan ini dibuat pada {{ now()->translatedFormat('d F Y H:i') }}</em></p>
</body>
</html>
