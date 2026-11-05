<?php
// Bu sayfa yeni ilan ekleme formunu gösterir ve veriyi veritabanına kaydeder.
require_once __DIR__ . '/config/database.php';

$hatalar = [];
$baslik = '';
$konum = '';
$fiyat = '';
$odaSayisi = '';
$ilanTipi = 'satilik';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kullanıcıdan gelen verileri güvenli bir şekilde alıyoruz.
    $baslik = trim($_POST['baslik'] ?? '');
    $konum = trim($_POST['konum'] ?? '');
    $fiyat = trim($_POST['fiyat'] ?? '');
    $odaSayisi = trim($_POST['oda_sayisi'] ?? '');
    $ilanTipi = $_POST['ilan_tipi'] ?? 'satilik';

    // Basit doğrulama işlemleri
    if ($baslik === '') {
        $hatalar[] = 'İlan başlığı boş olamaz.';
    }

    if ($konum === '') {
        $hatalar[] = 'Konum bilgisi boş olamaz.';
    }

    if ($fiyat === '' || !is_numeric($fiyat) || (float) $fiyat <= 0) {
        $hatalar[] = 'Fiyat sayısal ve pozitif olmalıdır.';
    }

    if ($odaSayisi === '' || !ctype_digit((string) $odaSayisi) || (int) $odaSayisi <= 0) {
        $hatalar[] = 'Oda sayısı pozitif tam sayı olmalıdır.';
    }

    if (!in_array($ilanTipi, ['satilik', 'kiralik'], true)) {
        $hatalar[] = 'İlan tipi geçersiz.';
    }

    // Doğrulama başarılıysa veritabanına ekleme yapıyoruz.
    if (empty($hatalar)) {
        $sql = 'INSERT INTO ilanlar (baslik, konum, fiyat, oda_sayisi, ilan_tipi) VALUES (:baslik, :konum, :fiyat, :oda_sayisi, :ilan_tipi)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':baslik' => $baslik,
            ':konum' => $konum,
            ':fiyat' => (float) $fiyat,
            ':oda_sayisi' => (int) $odaSayisi,
            ':ilan_tipi' => $ilanTipi,
        ]);

        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni İlan Ekle</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container header-content">
            <h1>Emlak Otomasyon</h1>
            <nav>
                <a href="index.php">Ana Sayfa</a>
                <a href="ekle.php">İlan Ekle</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="page-header">
            <h2>Yeni İlan Ekle</h2>
            <a href="index.php" class="btn">Geri Dön</a>
        </div>

        <?php if (!empty($hatalar)): ?>
            <div class="alert">
                <?php foreach ($hatalar as $hata): ?>
                    <div><?= htmlspecialchars($hata, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="ekle.php">
            <div class="form-row">
                <div>
                    <label for="baslik">İlan Başlığı</label>
                    <input id="baslik" name="baslik" type="text" value="<?= htmlspecialchars($baslik, ENT_QUOTES, 'UTF-8') ?>" placeholder="Örnek: 3+1 Daire">
                </div>
                <div>
                    <label for="konum">Konum</label>
                    <input id="konum" name="konum" type="text" value="<?= htmlspecialchars($konum, ENT_QUOTES, 'UTF-8') ?>" placeholder="Örnek: İzmir / Bornova">
                </div>
            </div>

            <div class="form-row">
                <div>
                    <label for="fiyat">Fiyat</label>
                    <input id="fiyat" name="fiyat" type="number" step="0.01" min="0" value="<?= htmlspecialchars($fiyat, ENT_QUOTES, 'UTF-8') ?>" placeholder="2500000">
                </div>
                <div>
                    <label for="oda_sayisi">Oda Sayısı</label>
                    <input id="oda_sayisi" name="oda_sayisi" type="number" min="1" value="<?= htmlspecialchars($odaSayisi, ENT_QUOTES, 'UTF-8') ?>" placeholder="3">
                </div>
            </div>

            <div class="form-row">
                <div>
                    <label for="ilan_tipi">İlan Tipi</label>
                    <select id="ilan_tipi" name="ilan_tipi">
                        <option value="satilik" <?= $ilanTipi === 'satilik' ? 'selected' : '' ?>>Satılık</option>
                        <option value="kiralik" <?= $ilanTipi === 'kiralik' ? 'selected' : '' ?>>Kiralık</option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-secondary">İlanı Kaydet</button>
                <a href="index.php" class="btn">İptal</a>
            </div>
        </form>
    </main>
</body>
</html>
