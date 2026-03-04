<?php
/**
 * Fungsi Perhitungan Metode TOPSIS
 * GDSS Bantuan Rumah Keluarga Miskin
 * FINAL VERSION - Metode Standar TOPSIS (Tanpa Pembalikan)
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Cek apakah penilaian sudah lengkap
 */
function cekPenilaianLengkap($user_id) {
    $totalAlternatif = fetch("SELECT COUNT(*) as total FROM alternatif")['total'];
    $totalPenilaian = fetch("SELECT COUNT(*) as total FROM penilaian WHERE user_id = $user_id")['total'];
    return $totalPenilaian >= $totalAlternatif;
}

/**
 * Hitung TOPSIS lengkap dengan detail semua step
 * Metode Standar TOPSIS
 */
function hitungTOPSIS($user_id) {
    // 1. Ambil data
    $alternatif = fetchAll("SELECT * FROM alternatif ORDER BY kode");
    $kriteria = fetchAll("SELECT * FROM kriteria ORDER BY kode");
    
    if (empty($alternatif)) {
        return ['error' => 'Belum ada data alternatif'];
    }
    
    // 2. Buat matriks keputusan (X) - TANPA PEMBALIKAN
    $matriks = [];
    
    foreach ($alternatif as $alt) {
        $penilaian = fetch("SELECT * FROM penilaian 
                           WHERE user_id = $user_id AND alternatif_id = {$alt['id']}");
        
        if (!$penilaian) {
            return ['error' => 'Penilaian belum lengkap'];
        }
        
        $matriks[] = [
            $penilaian['c1_pekerjaan'],
            $penilaian['c2_tanggungan'],
            $penilaian['c3_penghasilan'],
            $penilaian['c4_kondisi_rumah'],
            $penilaian['c5_status_rumah'],
            $penilaian['c6_kepemilikan']
        ];
    }
    
    // 3. Normalisasi (R) - Euclidean
    $normalized = [];
    $m = count($matriks);
    $n = count($matriks[0]);
    
    // Hitung pembagi untuk setiap kolom
    $divisors = [];
    for ($j = 0; $j < $n; $j++) {
        $sum = 0;
        for ($i = 0; $i < $m; $i++) {
            $sum += pow($matriks[$i][$j], 2);
        }
        $divisors[$j] = sqrt($sum);
    }
    
    // Normalisasi
    for ($i = 0; $i < $m; $i++) {
        $normalized[$i] = [];
        for ($j = 0; $j < $n; $j++) {
            $normalized[$i][$j] = $matriks[$i][$j] / $divisors[$j];
        }
    }
    
    // 4. Matriks Terbobot (Y)
    $weighted = [];
    $bobot = [];
    foreach ($kriteria as $k) {
        $bobot[] = $k['bobot'];
    }
    
    for ($i = 0; $i < $m; $i++) {
        $weighted[$i] = [];
        for ($j = 0; $j < $n; $j++) {
            $weighted[$i][$j] = $normalized[$i][$j] * $bobot[$j];
        }
    }
    
    // 5. Solusi Ideal Positif (A+) dan Negatif (A-)
    $idealPositif = [];
    $idealNegatif = [];
    
    for ($j = 0; $j < $n; $j++) {
        $kolom = array_column($weighted, $j);
        
        // Cek jenis kriteria
        if ($kriteria[$j]['jenis'] == 'benefit') {
            // Benefit: A+ = max, A- = min
            $idealPositif[$j] = max($kolom);
            $idealNegatif[$j] = min($kolom);
        } else {
            // Cost: A+ = min, A- = max
            $idealPositif[$j] = min($kolom);
            $idealNegatif[$j] = max($kolom);
        }
    }
    
    // 6. Hitung Jarak D+ dan D-
    $jarakPositif = [];
    $jarakNegatif = [];
    
    for ($i = 0; $i < $m; $i++) {
        $sumPlus = 0;
        $sumMinus = 0;
        
        for ($j = 0; $j < $n; $j++) {
            $sumPlus += pow($weighted[$i][$j] - $idealPositif[$j], 2);
            $sumMinus += pow($weighted[$i][$j] - $idealNegatif[$j], 2);
        }
        
        $jarakPositif[$i] = sqrt($sumPlus);
        $jarakNegatif[$i] = sqrt($sumMinus);
    }
    
    // 7. Hitung Skor Preferensi (Vi)
    $preferensi = [];
    for ($i = 0; $i < $m; $i++) {
        $preferensi[$i] = $jarakNegatif[$i] / ($jarakPositif[$i] + $jarakNegatif[$i]);
    }
    
    // 8. Ranking
    $ranking = [];
    foreach ($alternatif as $idx => $alt) {
        $ranking[] = [
            'alternatif_id' => $alt['id'],
            'kode' => $alt['kode'],
            'nama' => $alt['nama'],
            'skor' => $preferensi[$idx]
        ];
    }
    
    // Sort by skor DESC
    usort($ranking, function($a, $b) {
        return $b['skor'] <=> $a['skor'];
    });
    
    // Tambahkan ranking
    for ($i = 0; $i < count($ranking); $i++) {
        $ranking[$i]['ranking'] = $i + 1;
    }
    
    // Return semua data
    return [
        'matriks_keputusan' => $matriks,
        'matriks_ternormalisasi' => $normalized,
        'matriks_terbobot' => $weighted,
        'ideal_positif' => $idealPositif,
        'ideal_negatif' => $idealNegatif,
        'jarak_positif' => $jarakPositif,
        'jarak_negatif' => $jarakNegatif,
        'preferensi' => $preferensi,
        'ranking' => $ranking,
        'alternatif' => $alternatif,
        'kriteria' => $kriteria
    ];
}

/**
 * Simpan hasil TOPSIS ke database
 */
function simpanHasilTOPSIS($user_id, $hasil) {
    // Hapus hasil lama
    query("DELETE FROM hasil_topsis WHERE user_id = $user_id");
    
    // Simpan hasil baru
    foreach ($hasil['ranking'] as $r) {
        $alternatif_id = $r['alternatif_id'];
        $skor = $r['skor'];
        $ranking = $r['ranking'];
        
        query("INSERT INTO hasil_topsis (user_id, alternatif_id, skor_topsis, ranking) 
               VALUES ($user_id, $alternatif_id, $skor, $ranking)");
    }
    
    // Log aktivitas
    $userName = fetch("SELECT nama FROM users WHERE id = $user_id")['nama'];
    query("INSERT INTO log_aktivitas (user_id, aktivitas, keterangan) 
           VALUES ($user_id, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk $userName berhasil dihitung')");
    
    return true;
}

/**
 * Ambil hasil TOPSIS dari database
 */
function getHasilTOPSIS($user_id) {
    return fetchAll("SELECT ht.*, a.kode, a.nama, a.alamat
                     FROM hasil_topsis ht
                     JOIN alternatif a ON ht.alternatif_id = a.id
                     WHERE ht.user_id = $user_id
                     ORDER BY ht.ranking ASC");
}
?>