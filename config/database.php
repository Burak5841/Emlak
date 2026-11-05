<?php
// Bu dosya veritabanı bağlantısını kurar ve gerekli tabloyu oluşturur.
// Proje başlatıldığında önce bu dosya çalıştırılır.

define('DB_HOST', 'localhost');
define('DB_NAME', 'emlak_db');
define('DB_USER', 'emlak_user');
define('DB_PASS', '123456');

try {
    // Önce MySQL sunucusuna bağlanıyoruz.
    $serverPdo = new PDO('mysql:host=' . DB_HOST . ';charset=utf8mb4', DB_USER, DB_PASS);
    $serverPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Veritabanı yoksa oluşturuyoruz.
    $serverPdo->exec('CREATE DATABASE IF NOT EXISTS ' . DB_NAME . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

    // Oluşturulan veritabanına tekrar bağlanıyoruz.
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // İlanlar tablosunu oluşturuyoruz. Eğer yoksa ekliyoruz.
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS ilanlar (
            id INT AUTO_INCREMENT PRIMARY KEY,
            baslik VARCHAR(255) NOT NULL,
            konum VARCHAR(255) NOT NULL,
            fiyat DECIMAL(12,2) NOT NULL,
            oda_sayisi INT NOT NULL,
            ilan_tipi ENUM('satilik','kiralik') NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );
} catch (PDOException $e) {
    // Bağlantı sorunu olursa siteye kısa mesaj gösteriyoruz.
    die('Veritabanı bağlantısı kurulamadı: ' . $e->getMessage());
}
