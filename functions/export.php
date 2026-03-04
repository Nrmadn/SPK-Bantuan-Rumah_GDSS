<?php
/**
 * Fungsi Export Excel (Simple - tanpa library)
 * GDSS Bantuan Rumah Keluarga Miskin
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Export Hasil Borda ke Excel
 */
function exportHasilBordaExcel() {
    // Ambil data
    $hasil = fetchAll("SELECT hb.*, a.kode, a.nama, a.alamat, a.no_kk
                       FROM hasil_borda hb
                       JOIN alternatif a ON hb.alternatif_id = a.id
                       ORDER BY hb.ranking_final ASC");
    
    if (empty($hasil)) {
        die('Belum ada data hasil Borda');
    }
    
    // Set header untuk download Excel
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Hasil_Konsensus_Borda_" . date('Y-m-d') . ".xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    
    // Buat tabel HTML (Excel akan membacanya)
    echo '
    <html>
    <head>
        <meta charset="utf-8">
        <style>
            table { border-collapse: collapse; width: 100%; }
            th, td { border: 1px solid #000; padding: 8px; text-align: left; }
            th { background-color: #4CAF50; color: white; font-weight: bold; }
            .header { font-size: 18px; font-weight: bold; text-align: center; margin-bottom: 20px; }
        </style>
    </head>
    <body>
        <div class="header">
            <h2>HASIL KONSENSUS BORDA</h2>
            <h3>Group Decision Support System</h3>
            <h4>Penentuan Kelayakan Penerima Bantuan Rumah</h4>
            <p>Tanggal: ' . date('d F Y') . '</p>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Ranking Final</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>No. KK</th>
                    <th>Rank DM1</th>
                    <th>Rank DM2</th>
                    <th>Rank DM3</th>
                    <th>Total Poin</th>
                    <th>Bobot</th>
                </tr>
            </thead>
            <tbody>';
    
    foreach ($hasil as $h) {
        echo '<tr>
                <td style="text-align: center;">' . $h['ranking_final'] . '</td>
                <td>' . $h['kode'] . '</td>
                <td>' . $h['nama'] . '</td>
                <td>' . $h['alamat'] . '</td>
                <td>' . $h['no_kk'] . '</td>
                <td style="text-align: center;">' . $h['rank_dm1'] . '</td>
                <td style="text-align: center;">' . $h['rank_dm2'] . '</td>
                <td style="text-align: center;">' . $h['rank_dm3'] . '</td>
                <td style="text-align: center;">' . $h['total_poin'] . '</td>
                <td style="text-align: center;">' . number_format($h['bobot'], 3) . '</td>
            </tr>';
    }
    
    echo '
            </tbody>
        </table>
        
        <br><br>
        <p><strong>Keterangan:</strong></p>
        <ul>
            <li>DM1 = Kepala Desa</li>
            <li>DM2 = Sekretaris Desa</li>
            <li>DM3 = Ketua RT/RW</li>
        </ul>
        
        <br>
        <p><strong>Pemenang:</strong> ' . $hasil[0]['nama'] . ' (Ranking #1)</p>
        <p><strong>Total Poin Borda:</strong> ' . $hasil[0]['total_poin'] . '</p>
        <p><strong>Bobot:</strong> ' . number_format($hasil[0]['bobot'], 3) . '</p>
    </body>
    </html>';
    
    exit;
}

/**
 * Export Hasil TOPSIS ke Excel
 */
function exportHasilTOPSISExcel($user_id) {
    // Ambil data
    $hasil = fetchAll("SELECT ht.*, a.kode, a.nama, a.alamat, a.no_kk
                       FROM hasil_topsis ht
                       JOIN alternatif a ON ht.alternatif_id = a.id
                       WHERE ht.user_id = $user_id
                       ORDER BY ht.ranking ASC");
    
    if (empty($hasil)) {
        die('Belum ada data hasil TOPSIS');
    }
    
    $user = fetch("SELECT nama, role FROM users WHERE id = $user_id");
    
    // Set header
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Hasil_TOPSIS_" . str_replace(' ', '_', $user['nama']) . "_" . date('Y-m-d') . ".xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    
    echo '
    <html>
    <head>
        <meta charset="utf-8">
        <style>
            table { border-collapse: collapse; width: 100%; }
            th, td { border: 1px solid #000; padding: 8px; text-align: left; }
            th { background-color: #2196F3; color: white; font-weight: bold; }
        </style>
    </head>
    <body>
        <h2>HASIL PERHITUNGAN TOPSIS</h2>
        <p><strong>Decision Maker:</strong> ' . $user['nama'] . '</p>
        <p><strong>Role:</strong> ' . getRoleDisplay($user['role']) . '</p>
        <p><strong>Tanggal:</strong> ' . date('d F Y') . '</p>
        <br>
        
        <table>
            <thead>
                <tr>
                    <th>Ranking</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>Skor TOPSIS</th>
                </tr>
            </thead>
            <tbody>';
    
    foreach ($hasil as $h) {
        echo '<tr>
                <td style="text-align: center;">' . $h['ranking'] . '</td>
                <td>' . $h['kode'] . '</td>
                <td>' . $h['nama'] . '</td>
                <td>' . $h['alamat'] . '</td>
                <td style="text-align: center;">' . number_format($h['skor_topsis'], 4) . '</td>
            </tr>';
    }
    
    echo '
            </tbody>
        </table>
    </body>
    </html>';
    
    exit;
}

/**
 * Export Data Penilaian ke Excel
 */
function exportPenilaianExcel($user_id) {
    // Ambil data
    $penilaian = fetchAll("SELECT p.*, a.kode, a.nama
                           FROM penilaian p
                           JOIN alternatif a ON p.alternatif_id = a.id
                           WHERE p.user_id = $user_id
                           ORDER BY a.kode");
    
    if (empty($penilaian)) {
        die('Belum ada data penilaian');
    }
    
    $user = fetch("SELECT nama FROM users WHERE id = $user_id");
    
    // Set header
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Data_Penilaian_" . str_replace(' ', '_', $user['nama']) . "_" . date('Y-m-d') . ".xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    
    echo '
    <html>
    <head>
        <meta charset="utf-8">
        <style>
            table { border-collapse: collapse; width: 100%; }
            th, td { border: 1px solid #000; padding: 8px; text-align: center; }
            th { background-color: #FF9800; color: white; font-weight: bold; }
        </style>
    </head>
    <body>
        <h2>DATA PENILAIAN</h2>
        <p><strong>Decision Maker:</strong> ' . $user['nama'] . '</p>
        <p><strong>Tanggal:</strong> ' . date('d F Y') . '</p>
        <br>
        
        <table>
            <thead>
                <tr>
                    <th rowspan="2">Kode</th>
                    <th rowspan="2">Nama Alternatif</th>
                    <th colspan="6">Nilai Kriteria</th>
                </tr>
                <tr>
                    <th>C1<br>Pekerjaan</th>
                    <th>C2<br>Tanggungan</th>
                    <th>C3<br>Penghasilan</th>
                    <th>C4<br>Kondisi Rumah</th>
                    <th>C5<br>Status Rumah</th>
                    <th>C6<br>Kepemilikan</th>
                </tr>
            </thead>
            <tbody>';
    
    foreach ($penilaian as $p) {
        echo '<tr>
                <td>' . $p['kode'] . '</td>
                <td>' . $p['nama'] . '</td>
                <td>' . $p['c1_pekerjaan'] . '</td>
                <td>' . $p['c2_tanggungan'] . '</td>
                <td>' . $p['c3_penghasilan'] . '</td>
                <td>' . $p['c4_kondisi_rumah'] . '</td>
                <td>' . $p['c5_status_rumah'] . '</td>
                <td>' . $p['c6_kepemilikan'] . '</td>
            </tr>';
    }
    
    echo '
            </tbody>
        </table>
        
        <br><br>
        <p><strong>Keterangan Kriteria:</strong></p>
        <ul>
            <li>C1 = Pekerjaan Orang Tua (Cost) - Skala 1-6</li>
            <li>C2 = Jumlah Tanggungan (Benefit) - Skala 1-6</li>
            <li>C3 = Sumber Penghasilan (Cost) - Skala 1-6</li>
            <li>C4 = Kondisi Rumah (Cost) - Skala 1-6</li>
            <li>C5 = Status Rumah (Cost) - Skala 1-6</li>
            <li>C6 = Kepemilikan Rumah Lain (Cost) - Skala 1-6</li>
        </ul>
    </body>
    </html>';
    
    exit;
}
?>