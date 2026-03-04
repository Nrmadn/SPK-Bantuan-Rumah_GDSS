<?php
/**
 * Fungsi Perhitungan Metode WEIGHTED BORDA
 * GDSS Bantuan Rumah Keluarga Miskin
 * FIXED VERSION - Sesuai Excel
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Hitung WEIGHTED BORDA dari 3 Decision Maker
 * Menggunakan Skor TOPSIS × Bobot Ranking
 */
function hitungBorda() {
    global $conn;
    
    // 1. Cek apakah semua DM sudah hitung TOPSIS
    $dmList = [2, 3, 4]; // user_id: Kepala Desa, Sekretaris, Ketua RT
    
    foreach ($dmList as $dm) {
        $cek = fetch("SELECT COUNT(*) as total FROM hasil_topsis WHERE user_id = $dm");
        if ($cek['total'] == 0) {
            return ['error' => 'Belum semua Decision Maker melakukan penilaian'];
        }
    }
    
    // 2. Ambil semua alternatif
    $alternatif = fetchAll("SELECT id, kode, nama FROM alternatif ORDER BY kode");
    $jumlahAlternatif = count($alternatif);
    
    // 3. Ambil hasil TOPSIS dari setiap DM
    $topsisData = [];
    foreach ($dmList as $dm) {
        $topsisData[$dm] = fetchAll("SELECT * FROM hasil_topsis WHERE user_id = $dm ORDER BY ranking ASC");
    }
    
    // 4. Buat mapping alternatif_id -> ranking & skor untuk setiap DM
    $rankingMap = [];
    $skorMap = [];
    
    foreach ($dmList as $dm) {
        foreach ($topsisData[$dm] as $row) {
            $rankingMap[$dm][$row['alternatif_id']] = $row['ranking'];
            $skorMap[$dm][$row['alternatif_id']] = $row['skor_topsis'];
        }
    }
    
    // 5. Hitung Weighted Borda untuk setiap alternatif
    $hasilBorda = [];
    
    foreach ($alternatif as $alt) {
        $id = $alt['id'];
        
        // Ambil ranking dari setiap DM
        $rankDM1 = $rankingMap[2][$id] ?? 0;
        $rankDM2 = $rankingMap[3][$id] ?? 0;
        $rankDM3 = $rankingMap[4][$id] ?? 0;
        
        // Ambil skor TOPSIS dari setiap DM
        $skorDM1 = $skorMap[2][$id] ?? 0;
        $skorDM2 = $skorMap[3][$id] ?? 0;
        $skorDM3 = $skorMap[4][$id] ?? 0;
        
        // Konversi ranking ke bobot Borda
        // Rumus: Bobot = (n - ranking + 1)
        $bobotDM1 = $jumlahAlternatif - $rankDM1 + 1;
        $bobotDM2 = $jumlahAlternatif - $rankDM2 + 1;
        $bobotDM3 = $jumlahAlternatif - $rankDM3 + 1;
        
        // WEIGHTED BORDA: Total Poin = Σ(Skor TOPSIS × Bobot Ranking)
        $totalPoin = ($skorDM1 * $bobotDM1) + ($skorDM2 * $bobotDM2) + ($skorDM3 * $bobotDM3);
        
        $hasilBorda[] = [
            'alternatif_id' => $id,
            'kode' => $alt['kode'],
            'nama' => $alt['nama'],
            'rank_dm1' => $rankDM1,
            'rank_dm2' => $rankDM2,
            'rank_dm3' => $rankDM3,
            'skor_dm1' => $skorDM1,
            'skor_dm2' => $skorDM2,
            'skor_dm3' => $skorDM3,
            'bobot_dm1' => $bobotDM1,
            'bobot_dm2' => $bobotDM2,
            'bobot_dm3' => $bobotDM3,
            'total_poin' => $totalPoin
        ];
    }
    
    // 6. Hitung total semua poin untuk normalisasi
    $totalSemuaPoin = array_sum(array_column($hasilBorda, 'total_poin'));
    
    // 7. Hitung bobot (normalisasi 0-1)
    for ($i = 0; $i < count($hasilBorda); $i++) {
        $hasilBorda[$i]['bobot'] = $hasilBorda[$i]['total_poin'] / $totalSemuaPoin;
    }
    
    // 8. Sort by total_poin DESC
    usort($hasilBorda, function($a, $b) {
        return $b['total_poin'] <=> $a['total_poin'];
    });
    
    // 9. Tambahkan ranking final
    for ($i = 0; $i < count($hasilBorda); $i++) {
        $hasilBorda[$i]['ranking_final'] = $i + 1;
    }
    
    return $hasilBorda;
}

/**
 * Simpan hasil Borda ke database
 */
function simpanHasilBorda($hasil) {
    global $conn;
    
    // Hapus hasil Borda lama
    query("DELETE FROM hasil_borda");
    
    // Simpan hasil baru
    foreach ($hasil as $row) {
        $alternatif_id = $row['alternatif_id'];
        $rank_dm1 = $row['rank_dm1'];
        $rank_dm2 = $row['rank_dm2'];
        $rank_dm3 = $row['rank_dm3'];
        $skor_dm1 = $row['skor_dm1'];
        $skor_dm2 = $row['skor_dm2'];
        $skor_dm3 = $row['skor_dm3'];
        $total_poin = $row['total_poin'];
        $bobot = $row['bobot'];
        $ranking_final = $row['ranking_final'];
        
        query("INSERT INTO hasil_borda 
               (alternatif_id, rank_dm1, rank_dm2, rank_dm3, 
                skor_dm1, skor_dm2, skor_dm3, 
                total_poin, bobot, ranking_final) 
               VALUES ($alternatif_id, $rank_dm1, $rank_dm2, $rank_dm3, 
                       $skor_dm1, $skor_dm2, $skor_dm3,
                       $total_poin, $bobot, $ranking_final)");
    }
    
    // Log aktivitas
    query("INSERT INTO log_aktivitas (user_id, aktivitas, keterangan) 
           VALUES (1, 'Konsensus Borda', 'Perhitungan Weighted Borda berhasil dilakukan')");
    
    return true;
}

/**
 * Ambil hasil Borda dari database
 */
function getHasilBorda() {
    return fetchAll("SELECT hb.*, a.kode, a.nama, a.alamat, a.no_kk
                     FROM hasil_borda hb
                     JOIN alternatif a ON hb.alternatif_id = a.id
                     ORDER BY hb.ranking_final ASC");
}

/**
 * Cek apakah sudah ada hasil Borda
 */
function cekHasilBordaAda() {
    $cek = fetch("SELECT COUNT(*) as total FROM hasil_borda");
    return $cek['total'] > 0;
}

/**
 * Get pemenang (ranking 1)
 */
function getPemenang() {
    return fetch("SELECT hb.*, a.kode, a.nama, a.alamat, a.no_kk, a.keterangan
                  FROM hasil_borda hb
                  JOIN alternatif a ON hb.alternatif_id = a.id
                  WHERE hb.ranking_final = 1");
}

/**
 * Get detail perhitungan Borda lengkap (untuk tampilan tabel)
 */
function getDetailBordaLengkap() {
    $alternatif = fetchAll("SELECT id, kode, nama FROM alternatif ORDER BY kode");
    $dmList = [2, 3, 4];
    $jumlahAlternatif = count($alternatif);
    
    $detail = [];
    
    foreach ($alternatif as $alt) {
        $id = $alt['id'];
        
        // Ambil ranking dan skor dari setiap DM
        $data = [
            'alternatif_id' => $id,
            'kode' => $alt['kode'],
            'nama' => $alt['nama']
        ];
        
        // Array untuk menyimpan skor per ranking position
        $skorPerRanking = [1 => 0, 2 => 0, 3 => 0, 4 => 0];
        
        foreach ($dmList as $dmIdx => $dm) {
            $hasil = fetch("SELECT ranking, skor_topsis FROM hasil_topsis 
                           WHERE user_id = $dm AND alternatif_id = $id");
            
            if ($hasil) {
                $ranking = $hasil['ranking'];
                $skor = $hasil['skor_topsis'];
                
                // Tambahkan skor ke kolom ranking yang sesuai
                $skorPerRanking[$ranking] += $skor;
                
                $data["rank_dm" . ($dmIdx + 1)] = $ranking;
                $data["skor_dm" . ($dmIdx + 1)] = $skor;
            }
        }
        
        $data['skor_rank_1'] = $skorPerRanking[1];
        $data['skor_rank_2'] = $skorPerRanking[2];
        $data['skor_rank_3'] = $skorPerRanking[3];
        $data['skor_rank_4'] = $skorPerRanking[4];
        
        // Hitung total poin weighted borda
        $totalPoin = ($skorPerRanking[1] * 4) + ($skorPerRanking[2] * 3) + 
                     ($skorPerRanking[3] * 2) + ($skorPerRanking[4] * 1);
        
        $data['total_poin'] = $totalPoin;
        
        $detail[] = $data;
    }
    
    // Sort by total poin DESC
    usort($detail, function($a, $b) {
        return $b['total_poin'] <=> $a['total_poin'];
    });
    
    // Hitung total semua poin
    $totalSemuaPoin = array_sum(array_column($detail, 'total_poin'));
    
    // Tambah bobot dan ranking
    for ($i = 0; $i < count($detail); $i++) {
        $detail[$i]['bobot'] = $detail[$i]['total_poin'] / $totalSemuaPoin;
        $detail[$i]['ranking_final'] = $i + 1;
    }
    
    return $detail;
}
?>