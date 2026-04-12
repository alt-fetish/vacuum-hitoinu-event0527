<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
    setFlash('login_error', '不正なリクエストです。');
    header('Location: index.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === ADMIN_USER && password_verify($password, ADMIN_PASS_HASH)) {
    session_regenerate_id(true);
    $_SESSION['admin_logged_in'] = true;
    header('Location: index.php');
} else {
    setFlash('login_error', 'ユーザー名またはパスワードが正しくありません。');
    header('Location: index.php');
}
exit;
