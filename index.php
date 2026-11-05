<?php
// Ana sayfa: sistemde kayıtlı tüm ilanları listeler.
require_once __DIR__ . '/config/database.php';

// Tüm ilanları tarihe göre en yeni olan önce gelecek şekilde çekiyoruz.
$sql = 'SELECT id, baslik, konum, fiyat, oda_sayisi, ilan_tipi FROM ilanlar ORDER BY created_at DESC';
$stmt = $pdo->query($sql);
$ilanlar = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emlak Otomasyon</title>
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
            <h2>İlan Listesi</h2>
            <a href="ekle.php" class="btn btn-secondary">+ Yeni İlan</a>
        </div>

        <?php if (empty($ilanlar)): ?>
            <div class="empty-box">
                Henüz eklenmiş ilan bulunmuyor. İlk ilanınızı ekleyin.
            </div>
        <?php else: ?>
            <div class="cards">
                <?php foreach ($ilanlar as $ilan): ?>
                    <?php
                    $baslik = htmlspecialchars($ilan['baslik'], ENT_QUOTES, 'UTF-8');
                    $konum = htmlspecialchars($ilan['konum'], ENT_QUOTES, 'UTF-8');
                    $fiyat = number_format((float) $ilan['fiyat'], 2, ',', '.');
                    $odaSayisi = (int) $ilan['oda_sayisi'];
                    $tip = $ilan['ilan_tipi'] === 'satilik' ? 'Satılık' : 'Kiralık';
                    $tipClass = $ilan['ilan_tipi'] === 'satilik' ? 'satilik' : 'kiralik';
                    ?>
                    <article class="card">
                        <span class="badge <?= $tipClass ?>"><?= $tip ?></span>
                        <h3><?= $baslik ?></h3>
                        <div class="meta">
                            <p><strong>Lokasyon:</strong> <?= $konum ?></p>
                            <p><strong>Oda:</strong> <?= $odaSayisi ?></p>
                        </div>
                        <div class="price">₺<?= $fiyat ?></div>

                        <div class="card-actions">
                            <a href="duzenle.php?id=<?= (int) $ilan['id'] ?>" class="btn">Düzenle</a>
                            <form method="POST" action="sil.php" onsubmit="return confirm('Bu ilan silinsin mi?');">
                                <input type="hidden" name="id" value="<?= (int) $ilan['id'] ?>">
                                <button type="submit" class="btn btn-danger">Sil</button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
