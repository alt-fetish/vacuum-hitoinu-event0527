<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

if (empty($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
    setFlash('admin_msg', 'CSRFトークンが不正です。');
    header('Location: index.php');
    exit;
}

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    setFlash('admin_msg', 'IDが不正です。');
    header('Location: index.php');
    exit;
}

$db = getDB();
$stmt = $db->prepare('DELETE FROM reservations WHERE id = ?');
$stmt->execute([$id]);

$count = $stmt->rowCount();
setFlash('admin_msg', $count > 0 ? "予約 #{$id} を削除しました。" : "予約 #{$id} は見つかりませんでした。");

header('Location: index.php');
exit;
