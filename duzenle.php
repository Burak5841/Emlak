<?php
// Bu sayfa mevcut bir ilanı güncellemek için kullanılır.
require_once __DIR__ . '/config/database.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// İlk önce ilgili ilanı veritabanından çekeriz.
$stmt = $pdo->prepare('SELECT id, baslik, konum, fiyat, oda_sayisi, ilan_tipi FROM ilanlar WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $id]);
$ilan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ilan) {
    header('Location: index.php');
    exit;
}

$hatalar = [];
$baslik = $ilan['baslik'];
$konum = $ilan['konum'];
$fiyat = $ilan['fiyat'];
$odaSayisi = $ilan['oda_sayisi'];
$ilanTipi = $ilan['ilan_tipi'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Formdan gelen verileri tekrar alıyoruz.
    $baslik = trim($_POST['baslik'] ?? $ilan['baslik']);
    $konum = trim($_POST['konum'] ?? $ilan['konum']);
    $fiyat = trim($_POST['fiyat'] ?? $ilan['fiyat']);
    $odaSayisi = trim($_POST['oda_sayisi'] ?? $ilan['oda_sayisi']);
    $ilanTipi = $_POST['ilan_tipi'] ?? $ilan['ilan_tipi'];

    // Kontrolleri yapıyoruz.
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

    // Hata yoksa güncelleme işlemi yapıyoruz.
    if (empty($hatalar)) {
        $sql = 'UPDATE ilanlar SET baslik = :baslik, konum = :konum, fiyat = :fiyat, oda_sayisi = :oda_sayisi, ilan_tipi = :ilan_tipi WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':baslik' => $baslik,
            ':konum' => $konum,
            ':fiyat' => (float) $fiyat,
            ':oda_sayisi' => (int) $odaSayisi,
            ':ilan_tipi' => $ilanTipi,
            ':id' => $id,
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
    <title>İlanı Düzenle</title>
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
            <h2>İlanı Düzenle</h2>
            <a href="index.php" class="btn">Geri Dön</a>
        </div>

        <?php if (!empty($hatalar)): ?>
            <div class="alert">
                <?php foreach ($hatalar as $hata): ?>
                    <div><?= htmlspecialchars($hata, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="duzenle.php?id=<?= (int) $id ?>">
            <div class="form-row">
                <div>
                    <label for="baslik">İlan Başlığı</label>
                    <input id="baslik" name="baslik" type="text" value="<?= htmlspecialchars($baslik, ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div>
                    <label for="konum">Konum</label>
                    <input id="konum" name="konum" type="text" value="<?= htmlspecialchars($konum, ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </div>

            <div class="form-row">
                <div>
                    <label for="fiyat">Fiyat</label>
                    <input id="fiyat" name="fiyat" type="number" step="0.01" min="0" value="<?= htmlspecialchars((string) $fiyat, ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div>
                    <label for="oda_sayisi">Oda Sayısı</label>lstat
                    <input id="oda_sayisi" name="oda_sayisi" type="number" min="1" value="<?= htmlspecialchars((string) $odaSayisi, ENT_QUOTES, 'UTF-8') ?>">
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
                <button type="submit" class="btn btn-secondary">Güncelle</button>
                <a href="index.php" class="btn">İptal</a>
            </div>
        </form>
    </main>
</body>
</html>
