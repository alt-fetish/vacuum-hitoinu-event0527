<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/mail.php';

// Only POST allowed
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// CSRF check
if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
    setFlash('errors', ['不正なリクエストです。もう一度お試しください。']);
    header('Location: index.php#reserve');
    exit;
}

// Validate input
$result = validateReservation($_POST);
$errors = $result['errors'];
$name = $result['name'];
$email = $result['email'];
$x_account = $result['x_account'];
$rubber_trial = $result['rubber_trial'];
$notes = $result['notes'];
$slot_date = $result['slot_date'];
$slot_time = $result['slot_time'];

$flashFormData = [
    'name'         => $name,
    'email'        => $email,
    'x_account'    => $x_account,
    'rubber_trial' => $rubber_trial,
    'notes'        => $notes,
    'slot'         => ($slot_date && $slot_time) ? slotValue($slot_date, $slot_time) : '',
];

if (!empty($errors)) {
    setFlash('errors', $errors);
    setFlash('form_data', $flashFormData);
    header('Location: index.php#reserve');
    exit;
}

// Insert reservation within transaction
$db = getDB();
try {
    $db->exec('BEGIN IMMEDIATE');

    // Check capacity
    $stmt = $db->prepare('SELECT COUNT(*) as cnt FROM reservations WHERE slot_date = ? AND slot_time = ?');
    $stmt->execute([$slot_date, $slot_time]);
    $count = (int)$stmt->fetch()['cnt'];

    if ($count >= SLOT_CAPACITY) {
        $db->exec('ROLLBACK');
        setFlash('errors', ['申し訳ありません。選択された日時枠は満席になりました。別の枠をお選びください。']);
        setFlash('form_data', $flashFormData);
        header('Location: index.php#reserve');
        exit;
    }

    // Insert
    $stmt = $db->prepare('
        INSERT INTO reservations (name, email, x_account, rubber_trial, notes, slot_date, slot_time)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ');
    $stmt->execute([$name, $email, $x_account ?: null, $rubber_trial, $notes, $slot_date, $slot_time]);

    $db->exec('COMMIT');
} catch (PDOException $e) {
    $db->exec('ROLLBACK');

    // Duplicate check (UNIQUE constraint violation)
    if (strpos($e->getMessage(), 'UNIQUE constraint failed') !== false) {
        setFlash('errors', ['同じメールアドレスで同じ日時枠の予約が既に存在します。']);
    } else {
        setFlash('errors', ['予約処理中にエラーが発生しました。しばらくしてからもう一度お試しください。']);
    }
    setFlash('form_data', $flashFormData);
    header('Location: index.php#reserve');
    exit;
}

// Send confirmation email (non-blocking: if mail fails, reservation still succeeds)
sendConfirmationEmail($name, $email, $slot_date, $slot_time, $rubber_trial, $notes);

// Store for thanks page
$dayInfo = findEventDay($slot_date);
$_SESSION['last_reservation'] = [
    'name'       => $name,
    'email'      => $email,
    'slot_date'  => $slot_date,
    'slot_time'  => $slot_time,
    'rubber_trial' => $rubber_trial,
    'notes'      => $notes,
    'day_label'  => $dayInfo['label'] ?? $slot_date,
    'slot_label' => slotLabel($slot_time),
];

// Regenerate CSRF token after successful submission
unset($_SESSION['csrf_token']);

// PRG redirect
header('Location: thanks.php', true, 303);
exit;
