<?php
// Bu sayfa sadece POST ile çalışır. İlan silme işlemini burada yapar.
require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if ($id === false || $id <= 0) {
    header('Location: index.php');
    exit;
}

// Silme işlemi PDO prepared statement ile güvenli şekilde yapılır.
$sql = 'DELETE FROM ilanlar WHERE id = :id';
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);

header('Location: index.php');
exit;
