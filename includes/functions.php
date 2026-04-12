<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

// --- Output escaping ---
function h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

// --- CSRF ---
function generateCsrfToken(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrfToken(?string $token): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . h(generateCsrfToken()) . '">';
}

// --- Slot helpers ---
function slotLabel(int $hour): string
{
    $end_min = SLOT_DURATION;
    return sprintf('%d:00〜%d:%02d', $hour, $hour, $end_min);
}

function slotPeriod(int $hour): string
{
    return $hour < 13 ? '午前' : '午後';
}

function getSlotCounts(): array
{
    $db = getDB();
    $stmt = $db->query('SELECT slot_time, COUNT(*) as cnt FROM reservations GROUP BY slot_time');
    $counts = [];
    foreach (SLOT_HOURS as $h) {
        $counts[$h] = 0;
    }
    while ($row = $stmt->fetch()) {
        $counts[(int)$row['slot_time']] = (int)$row['cnt'];
    }
    return $counts;
}

function getSlotAvailability(): array
{
    $counts = getSlotCounts();
    $slots = [];
    foreach (SLOT_HOURS as $hour) {
        $booked = $counts[$hour];
        $remaining = SLOT_CAPACITY - $booked;
        $slots[$hour] = [
            'hour'      => $hour,
            'label'     => slotLabel($hour),
            'period'    => slotPeriod($hour),
            'booked'    => $booked,
            'remaining' => $remaining,
            'available' => $remaining > 0,
        ];
    }
    return $slots;
}

function isSlotAvailable(int $hour): bool
{
    $db = getDB();
    $stmt = $db->prepare('SELECT COUNT(*) as cnt FROM reservations WHERE slot_time = ?');
    $stmt->execute([$hour]);
    $row = $stmt->fetch();
    return (int)$row['cnt'] < SLOT_CAPACITY;
}

// --- Validation ---
function validateReservation(array $data): array
{
    $errors = [];

    // Name
    $name = trim($data['name'] ?? '');
    if ($name === '') {
        $errors[] = 'お名前を入力してください。';
    } elseif (strlen($name) > 300) { // ~100 UTF-8 chars
        $errors[] = 'お名前は100文字以内で入力してください。';
    }

    // Email
    $email = trim($data['email'] ?? '');
    if ($email === '') {
        $errors[] = 'メールアドレスを入力してください。';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'メールアドレスの形式が正しくありません。';
    }

    // X account (optional)
    $x_account = trim($data['x_account'] ?? '');
    if ($x_account !== '') {
        $x_account = ltrim($x_account, '@');
        if (strlen($x_account) > 50) {
            $errors[] = 'X(Twitter) IDは50文字以内で入力してください。';
        }
    }

    // Slot time
    $slot_time = (int)($data['slot_time'] ?? 0);
    if (!in_array($slot_time, SLOT_HOURS, true)) {
        $errors[] = '有効な時間枠を選択してください。';
    }

    return [
        'errors'    => $errors,
        'name'      => $name,
        'email'     => $email,
        'x_account' => $x_account,
        'slot_time' => $slot_time,
    ];
}

// --- Flash messages ---
function setFlash(string $key, $value): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash'][$key] = $value;
}

function getFlash(string $key, $default = null)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $value = $_SESSION['flash'][$key] ?? $default;
    unset($_SESSION['flash'][$key]);
    return $value;
}
