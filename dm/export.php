<?php
/**
 * Export TOPSIS Lengkap ke Excel
 * Semua Tabel Perhitungan
 */

session_start();
require_once '../config/database.php';
require_once '../functions/auth.php';
require_once '../functions/topsis.php';

requireDM();

$user_id = $_SESSION['user_id'];
$user_info = fetch("SELECT nama, role FROM users WHERE id = $user_id");

// Hitung TOPSIS
$hasilLengkap = hitungTOPSIS($user_id);

if (isset($hasilLengkap['error'])) {
    die('Error: ' . $hasilLengkap['error']);
}

// Ekstrak data
$matriks = $hasilLengkap['matriks_keputusan'];
$normalized = $hasilLengkap['matriks_ternormalisasi'];
$weighted = $hasilLengkap['matriks_terbobot'];
$idealPositif = $hasilLengkap['ideal_positif'];
$idealNegatif = $hasilLengkap['ideal_negatif'];
$jarakPositif = $hasilLengkap['jarak_positif'];
$jarakNegatif = $hasilLengkap['jarak_negatif'];
$preferensi = $hasilLengkap['preferensi'];
$alternatif = $hasilLengkap['alternatif'];
$kriteria = $hasilLengkap['kriteria'];
$ranking = $hasilLengkap['ranking'];

// Set header untuk download Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=TOPSIS_Lengkap_" . str_replace(' ', '_', $user_info['nama']) . "_" . date('Y-m-d') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        table { border-collapse: collapse; width: 100%; margin-bottom: 30px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: center; }
        th { background-color: #4CAF50; color: white; font-weight: bold; }
        .header { font-size: 18px; font-weight: bold; text-align: center; margin: 20px 0; }
        .section-title { background-color: #FFA500; font-weight: bold; padding: 10px; margin-top: 20px; }
        .result-highlight { background-color: #FFD700; font-weight: bold; }
    </style>
</head>
<body>
    <!-- HEADER -->
    <div class="header">
        <h1>HASIL PERHITUNGAN TOPSIS LENGKAP</h1>
        <h2>Group Decision Support System</h2>
        <h3>Penentuan Kelayakan Penerima Bantuan Rumah</h3>
        <p>Decision Maker: <strong><?= $user_info['nama'] ?></strong></p>
        <p>Role: <?= getRoleDisplay($user_info['role']) ?></p>
        <p>Tanggal: <?= date('d F Y H:i:s') ?></p>
    </div>

    <hr>

    <!-- TABEL 1: KRITERIA DAN BOBOT -->
    <div class="section-title">TABEL 1: KRITERIA DAN BOBOT KRITERIA</div>
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Kriteria</th>
                <th>Bobot</th>
                <th>Tipe</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($kriteria as $k): ?>
            <tr>
                <td><?= $k['kode'] ?></td>
                <td><?= $k['nama_kriteria'] ?></td>
                <td><?= $k['bobot'] ?></td>
                <td><?= strtoupper($k['jenis']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- TABEL 2: MATRIKS KEPUTUSAN -->
    <div class="section-title">TABEL 2: MATRIKS KEPUTUSAN DENGAN NILAI NUMERIK (X)</div>
    <table>
        <thead>
            <tr>
                <th>Alternatif</th>
                <?php foreach ($kriteria as $k): ?>
                <th><?= $k['kode'] ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php for ($i = 0; $i < count($alternatif); $i++): ?>
            <tr>
                <td><?= $alternatif[$i]['nama'] ?></td>
                <?php for ($j = 0; $j < count($matriks[$i]); $j++): ?>
                <td><?= $matriks[$i][$j] ?></td>
                <?php endfor; ?>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>

    <!-- TABEL 3: MATRIKS TERNORMALISASI -->
    <div class="section-title">TABEL 3: MATRIKS KEPUTUSAN TERNORMALISASI (R)</div>
    <p><strong>Rumus:</strong> r<sub>ij</sub> = x<sub>ij</sub> / √(Σx<sub>ij</sub>²)</p>
    <table>
        <thead>
            <tr>
                <th>Alternatif</th>
                <?php foreach ($kriteria as $k): ?>
                <th><?= $k['kode'] ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php for ($i = 0; $i < count($alternatif); $i++): ?>
            <tr>
                <td><?= $alternatif[$i]['nama'] ?></td>
                <?php for ($j = 0; $j < count($normalized[$i]); $j++): ?>
                <td><?= number_format($normalized[$i][$j], 8) ?></td>
                <?php endfor; ?>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>

    <!-- TABEL 4: MATRIKS TERBOBOT -->
    <div class="section-title">TABEL 4: MATRIKS KEPUTUSAN TERNORMALISASI TERBOBOT (Y)</div>
    <p><strong>Rumus:</strong> y<sub>ij</sub> = r<sub>ij</sub> × w<sub>j</sub></p>
    <table>
        <thead>
            <tr>
                <th>Alternatif</th>
                <?php foreach ($kriteria as $k): ?>
                <th><?= $k['kode'] ?><br>(w=<?= $k['bobot'] ?>)</th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php for ($i = 0; $i < count($alternatif); $i++): ?>
            <tr>
                <td><?= $alternatif[$i]['nama'] ?></td>
                <?php for ($j = 0; $j < count($weighted[$i]); $j++): ?>
                <td><?= number_format($weighted[$i][$j], 8) ?></td>
                <?php endfor; ?>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>

    <!-- TABEL 5: SOLUSI IDEAL -->
    <div class="section-title">TABEL 5: SOLUSI IDEAL POSITIF DAN NEGATIF</div>
    <p><strong>Aturan:</strong></p>
    <ul>
        <li><strong>Benefit:</strong> A<sup>+</sup> = max(y<sub>ij</sub>), A<sup>-</sup> = min(y<sub>ij</sub>)</li>
        <li><strong>Cost:</strong> A<sup>+</sup> = min(y<sub>ij</sub>), A<sup>-</sup> = max(y<sub>ij</sub>)</li>
    </ul>
    <table>
        <thead>
            <tr>
                <th>Solusi Ideal</th>
                <?php foreach ($kriteria as $k): ?>
                <th><?= $k['kode'] ?><br>(<?= strtoupper($k['jenis']) ?>)</th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <tr style="background-color: #90EE90;">
                <td><strong>A<sup>+</sup> (Positif)</strong></td>
                <?php foreach ($idealPositif as $val): ?>
                <td><?= number_format($val, 8) ?></td>
                <?php endforeach; ?>
            </tr>
            <tr style="background-color: #FFB6C1;">
                <td><strong>A<sup>-</sup> (Negatif)</strong></td>
                <?php foreach ($idealNegatif as $val): ?>
                <td><?= number_format($val, 8) ?></td>
                <?php endforeach; ?>
            </tr>
        </tbody>
    </table>

    <!-- TABEL 6: JARAK -->
    <div class="section-title">TABEL 6: JARAK TIAP ALTERNATIF DARI SOLUSI IDEAL POSITIF DAN NEGATIF</div>
    <p><strong>Rumus:</strong></p>
    <ul>
        <li>D<sup>+</sup> = √(Σ(y<sub>ij</sub> - A<sup>+</sup>)<sup>2</sup>)</li>
        <li>D<sup>-</sup> = √(Σ(y<sub>ij</sub> - A<sup>-</sup>)<sup>2</sup>)</li>
    </ul>
    <table>
        <thead>
            <tr>
                <th>Alternatif</th>
                <th>D<sup>+</sup> (Jarak ke A+)</th>
                <th>D<sup>-</sup> (Jarak ke A-)</th>
            </tr>
        </thead>
        <tbody>
            <?php for ($i = 0; $i < count($alternatif); $i++): ?>
            <tr>
                <td><?= $alternatif[$i]['nama'] ?></td>
                <td><?= number_format($jarakPositif[$i], 8) ?></td>
                <td><?= number_format($jarakNegatif[$i], 8) ?></td>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>

    <!-- TABEL 7: SKOR PREFERENSI -->
    <div class="section-title">TABEL 7: SKOR AKHIR DARI SETIAP ALTERNATIF</div>
    <p><strong>Rumus:</strong> V<sub>i</sub> = D<sup>-</sup> / (D<sup>+</sup> + D<sup>-</sup>)</p>
    <table>
        <thead>
            <tr>
                <th>Alternatif</th>
                <th>V<sub>i</sub> (Skor Preferensi)</th>
            </tr>
        </thead>
        <tbody>
            <?php for ($i = 0; $i < count($alternatif); $i++): ?>
            <tr>
                <td><?= $alternatif[$i]['nama'] ?></td>
                <td><strong><?= number_format($preferensi[$i], 8) ?></strong></td>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>

    <!-- TABEL 8: RANKING FINAL -->
    <div class="section-title">TABEL 8: HASIL AKHIR DAN RANKING</div>
    <table>
        <thead>
            <tr>
                <th>Ranking</th>
                <th>Kode</th>
                <th>Nama Alternatif</th>
                <th>Alamat</th>
                <th>Skor (V<sub>i</sub>)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ranking as $h): ?>
            <tr <?= $h['ranking'] == 1 ? 'class="result-highlight"' : '' ?>>
                <td><strong><?= $h['ranking'] ?></strong></td>
                <td><?= $h['kode'] ?></td>
                <td><?= $h['nama'] ?></td>
                <td><?php 
                    $alt_detail = array_values(array_filter($alternatif, fn($a) => $a['id'] == $h['alternatif_id']))[0] ?? null;
                    echo $alt_detail ? $alt_detail['alamat'] : '-';
                ?></td>
                <td><strong><?= number_format($h['skor'], 8) ?></strong></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- KESIMPULAN -->
    <div class="section-title">KESIMPULAN</div>
    <?php $pemenang = $ranking[0]; ?>
    <table>
        <tr class="result-highlight">
            <td colspan="2" style="text-align: center; padding: 20px;">
                <h2>🏆 ALTERNATIF TERBAIK</h2>
                <h1><?= $pemenang['nama'] ?></h1>
                <h3>Skor V<sub>i</sub> = <?= number_format($pemenang['skor'], 8) ?></h3>
            </td>
        </tr>
    </table>

    <br><br>
    <p><strong>Catatan:</strong></p>
    <ul>
        <li>Metode: TOPSIS (Technique for Order of Preference by Similarity to Ideal Solution)</li>
        <li>Jumlah Alternatif: <?= count($alternatif) ?></li>
        <li>Jumlah Kriteria: <?= count($kriteria) ?> (<?= count(array_filter($kriteria, fn($k) => $k['jenis'] == 'cost')) ?> Cost, <?= count(array_filter($kriteria, fn($k) => $k['jenis'] == 'benefit')) ?> Benefit)</li>
        <li>Semakin tinggi skor V<sub>i</sub> (mendekati 1), semakin layak alternatif tersebut</li>
    </ul>

    <br><br>
    <p style="text-align: center;">
        <em>Dokumen ini di-generate otomatis oleh sistem GDSS</em><br>
        <em>Tanggal: <?= date('d F Y H:i:s') ?> WIB</em>
    </p>
</body>
</html>