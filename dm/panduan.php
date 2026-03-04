<?php
$pageTitle = 'Panduan Penggunaan';
require_once '../includes/header.php';
requireDM();

$kriteria = fetchAll("SELECT * FROM kriteria ORDER BY kode");
?>

<!-- Panduan Umum -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="bi bi-book-fill"></i> Panduan Penggunaan Sistem
        </h5>
    </div>
    <div class="card-body">
        <h5>Selamat datang di GDSS Bantuan Rumah!</h5>
        <p>
            Sistem ini dirancang untuk membantu proses pengambilan keputusan dalam menentukan 
            kelayakan penerima bantuan rumah untuk keluarga miskin secara objektif dan transparan.
        </p>
        
        <h6 class="mt-4"><strong>Alur Kerja Sistem:</strong></h6>
        <ol>
            <li>
                <strong>Login</strong> - Masuk menggunakan akun Decision Maker Anda
            </li>
            <li>
                <strong>Input Penilaian</strong> - Berikan penilaian untuk setiap calon penerima 
                berdasarkan 6 kriteria (skala 1-6)
            </li>
            <li>
                <strong>Perhitungan TOPSIS</strong> - Sistem akan otomatis menghitung ranking 
                berdasarkan penilaian Anda
            </li>
            <li>
                <strong>Konsensus Borda</strong> - Admin menggabungkan hasil dari semua Decision Maker 
                menggunakan metode Borda
            </li>
            <li>
                <strong>Hasil Final</strong> - Lihat penerima bantuan terpilih berdasarkan konsensus kelompok
            </li>
        </ol>
    </div>
</div>

<!-- Kriteria & Skala -->
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0">
            <i class="bi bi-list-ol"></i> Kriteria Penilaian & Skala
        </h5>
    </div>
    <div class="card-body">
        <?php foreach ($kriteria as $k): ?>
        <div class="mb-4 pb-3 border-bottom">
            <h6 class="text-primary">
                <strong><?= $k['kode'] ?> - <?= $k['nama_kriteria'] ?></strong>
                <span class="badge bg-<?= $k['jenis'] == 'cost' ? 'danger' : 'success' ?>">
                    <?= strtoupper($k['jenis']) ?>
                </span>
            </h6>
            
            <?php if ($k['kode'] == 'C1'): ?>
                <p><strong>Skala Penilaian:</strong></p>
                <ul>
                    <li><strong>1</strong> = Pengangguran (tidak bekerja)</li>
                    <li><strong>2</strong> = Buruh Harian Lepas</li>
                    <li><strong>3</strong> = Petani/Nelayan</li>
                    <li><strong>4</strong> = Wiraswasta Kecil</li>
                    <li><strong>5</strong> = Pegawai Swasta</li>
                    <li><strong>6</strong> = PNS/TNI/Polri</li>
                </ul>
                <p class="text-muted"><em>Nilai kecil = lebih layak dapat bantuan</em></p>
            
            <?php elseif ($k['kode'] == 'C2'): ?>
                <p><strong>Skala Penilaian:</strong></p>
                <ul>
                    <li><strong>1</strong> = 1 orang tanggungan</li>
                    <li><strong>2</strong> = 2 orang tanggungan</li>
                    <li><strong>3</strong> = 3 orang tanggungan</li>
                    <li><strong>4</strong> = 4 orang tanggungan</li>
                    <li><strong>5</strong> = 5 orang tanggungan</li>
                    <li><strong>6</strong> = 6 orang atau lebih</li>
                </ul>
                <p class="text-muted"><em>Nilai besar = lebih layak dapat bantuan</em></p>
            
            <?php elseif ($k['kode'] == 'C3'): ?>
                <p><strong>Skala Penilaian (Penghasilan per bulan):</strong></p>
                <ul>
                    <li><strong>1</strong> = < Rp 500.000</li>
                    <li><strong>2</strong> = Rp 500.000 - Rp 1.000.000</li>
                    <li><strong>3</strong> = Rp 1.000.000 - Rp 1.500.000</li>
                    <li><strong>4</strong> = Rp 1.500.000 - Rp 2.000.000</li>
                    <li><strong>5</strong> = Rp 2.000.000 - Rp 3.000.000</li>
                    <li><strong>6</strong> = > Rp 3.000.000</li>
                </ul>
                <p class="text-muted"><em>Nilai kecil = lebih layak dapat bantuan</em></p>
            
            <?php elseif ($k['kode'] == 'C4'): ?>
                <p><strong>Skala Penilaian:</strong></p>
                <ul>
                    <li><strong>1</strong> = Sangat Tidak Layak Huni (hampir roboh)</li>
                    <li><strong>2</strong> = Tidak Layak Huni (banyak kerusakan)</li>
                    <li><strong>3</strong> = Kurang Layak (perlu renovasi besar)</li>
                    <li><strong>4</strong> = Cukup Layak (perlu perbaikan kecil)</li>
                    <li><strong>5</strong> = Layak (kondisi baik sederhana)</li>
                    <li><strong>6</strong> = Sangat Layak (kondisi bagus/permanen)</li>
                </ul>
                <p class="text-muted"><em>Nilai kecil = lebih layak dapat bantuan</em></p>
            
            <?php elseif ($k['kode'] == 'C5'): ?>
                <p><strong>Skala Penilaian:</strong></p>
                <ul>
                    <li><strong>1</strong> = Menumpang (di rumah orang lain)</li>
                    <li><strong>2</strong> = Kontrak/Sewa</li>
                    <li><strong>3</strong> = Milik Orang Tua (belum atas nama sendiri)</li>
                    <li><strong>4</strong> = Milik Bersama/Warisan</li>
                    <li><strong>5</strong> = Milik Sendiri (belum sertifikat)</li>
                    <li><strong>6</strong> = Milik Sendiri (bersertifikat)</li>
                </ul>
                <p class="text-muted"><em>Nilai kecil = lebih layak dapat bantuan</em></p>
            
            <?php elseif ($k['kode'] == 'C6'): ?>
                <p><strong>Skala Penilaian:</strong></p>
                <ul>
                    <li><strong>1</strong> = Tidak punya rumah lain sama sekali</li>
                    <li><strong>2</strong> = Punya 1 rumah (sangat rusak)</li>
                    <li><strong>3</strong> = Punya 1 rumah (tidak layak)</li>
                    <li><strong>4</strong> = Punya 1 rumah (layak)</li>
                    <li><strong>5</strong> = Punya 2 rumah</li>
                    <li><strong>6</strong> = Punya 3 rumah atau lebih</li>
                </ul>
                <p class="text-muted"><em>Nilai kecil = lebih layak dapat bantuan</em></p>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Tips & Trik -->
<div class="card mb-4">
    <div class="card-header bg-warning">
        <h5 class="mb-0">
            <i class="bi bi-lightbulb-fill"></i> Tips & Trik Penilaian
        </h5>
    </div>
    <div class="card-body">
        <ol>
            <li class="mb-2">
                <strong>Objektif</strong> - Berikan penilaian berdasarkan kondisi riil yang Anda ketahui, 
                bukan karena kedekatan personal.
            </li>
            <li class="mb-2">
                <strong>Konsisten</strong> - Gunakan standar yang sama untuk semua alternatif.
            </li>
            <li class="mb-2">
                <strong>Akurat</strong> - Jika ragu, cek kembali data atau kondisi di lapangan.
            </li>
            <li class="mb-2">
                <strong>Jujur</strong> - Penilaian Anda akan mempengaruhi hasil akhir, berikan nilai yang sejujurnya.
            </li>
            <li class="mb-2">
                <strong>Edit Jika Perlu</strong> - Anda bisa mengubah penilaian sebelum Admin melakukan konsensus.
            </li>
        </ol>
    </div>
</div>

<!-- FAQ -->
<div class="card">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">
            <i class="bi bi-question-circle-fill"></i> Pertanyaan yang Sering Diajukan (FAQ)
        </h5>
    </div>
    <div class="card-body">
        <div class="accordion" id="accordionFAQ">
            <!-- FAQ 1 -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                        <strong>1. Apa itu metode TOPSIS?</strong>
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#accordionFAQ">
                    <div class="accordion-body">
                        TOPSIS (Technique for Order of Preference by Similarity to Ideal Solution) adalah 
                        metode untuk menentukan ranking alternatif berdasarkan kedekatan dengan solusi ideal. 
                        Sistem akan otomatis menghitung setelah Anda selesai input penilaian.
                    </div>
                </div>
            </div>
            
            <!-- FAQ 2 -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                        <strong>2. Apa itu metode Borda?</strong>
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                    <div class="accordion-body">
                        Metode Borda adalah metode voting untuk menggabungkan ranking dari beberapa Decision Maker. 
                        Alternatif dengan total poin tertinggi akan menjadi pemenang. Ini dilakukan oleh Admin 
                        setelah semua DM selesai input penilaian.
                    </div>
                </div>
            </div>
            
            <!-- FAQ 3 -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                        <strong>3. Apakah saya bisa mengubah penilaian?</strong>
                    </button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                    <div class="accordion-body">
                        Ya, Anda bisa mengubah penilaian kapan saja sebelum Admin melakukan konsensus Borda. 
                        Sistem akan otomatis menghitung ulang hasil TOPSIS Anda.
                    </div>
                </div>
            </div>
            
            <!-- FAQ 4 -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                        <strong>4. Apa bedanya Cost dan Benefit?</strong>
                    </button>
                </h2>
                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                    <div class="accordion-body">
                        <ul>
                            <li><strong>Cost</strong>: Semakin kecil nilai, semakin baik (contoh: Penghasilan - rendah lebih layak)</li>
                            <li><strong>Benefit</strong>: Semakin besar nilai, semakin baik (contoh: Tanggungan - banyak lebih layak)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>