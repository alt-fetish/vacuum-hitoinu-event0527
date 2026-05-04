<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

// 本番サーバ側の config.php に EVENT_DAYS が未定義の場合のフォールバック。
// （includes/config.php は .gitignore でデプロイ対象外のため、新規定数はここで担保する）
if (!defined('EVENT_DAYS')) {
    define('EVENT_DAYS', [
        [
            'date'  => '2026-06-27',
            'label' => '6月27日（土）',
            'short' => '6.27',
            'hours' => [10, 11, 12, 14, 15, 16, 17, 18],
        ],
        [
            'date'  => '2026-06-28',
            'label' => '6月28日（日）',
            'short' => '6.28',
            'hours' => [15, 16, 17, 18],
        ],
    ]);
}
if (!defined('EVENT_DATE')) {
    define('EVENT_DATE', '2026年6月27日（土）・28日（日）');
}
if (!defined('EVENT_DATE_SHORT')) {
    define('EVENT_DATE_SHORT', '6.27 ／ 6.28');
}

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

// 指定日付のEVENT_DAYSエントリを返す。無ければnull。
function findEventDay(string $date): ?array
{
    foreach (EVENT_DAYS as $day) {
        if ($day['date'] === $date) {
            return $day;
        }
    }
    return null;
}

// 日付+時刻が有効な開催枠か
function isValidSlot(string $date, int $hour): bool
{
    $day = findEventDay($date);
    if (!$day) {
        return false;
    }
    return in_array($hour, $day['hours'], true);
}

// "2026-06-27|10" 形式を解析。失敗時はnull。
function parseSlotValue(?string $value): ?array
{
    if ($value === null) {
        return null;
    }
    if (!preg_match('/^(\d{4}-\d{2}-\d{2})\|(\d{1,2})$/', $value, $m)) {
        return null;
    }
    return ['date' => $m[1], 'hour' => (int)$m[2]];
}

// "2026-06-27|10" 形式を構築
function slotValue(string $date, int $hour): string
{
    return $date . '|' . $hour;
}

// 全日全枠の予約数を [date][hour] => count で返す
function getSlotCounts(): array
{
    $db = getDB();
    $stmt = $db->query('SELECT slot_date, slot_time, COUNT(*) as cnt FROM reservations GROUP BY slot_date, slot_time');

    $counts = [];
    foreach (EVENT_DAYS as $day) {
        foreach ($day['hours'] as $hour) {
            $counts[$day['date']][$hour] = 0;
        }
    }
    while ($row = $stmt->fetch()) {
        $counts[$row['slot_date']][(int)$row['slot_time']] = (int)$row['cnt'];
    }
    return $counts;
}

// 各日のslot情報（残数等）を含めた配列を返す
function getEventDaysWithAvailability(): array
{
    $counts = getSlotCounts();
    $days = [];
    foreach (EVENT_DAYS as $day) {
        $slots = [];
        foreach ($day['hours'] as $hour) {
            $booked = $counts[$day['date']][$hour] ?? 0;
            $remaining = SLOT_CAPACITY - $booked;
            $slots[] = [
                'date'      => $day['date'],
                'hour'      => $hour,
                'label'     => slotLabel($hour),
                'period'    => slotPeriod($hour),
                'value'     => slotValue($day['date'], $hour),
                'booked'    => $booked,
                'remaining' => $remaining,
                'available' => $remaining > 0,
            ];
        }
        $day['slots'] = $slots;
        $days[] = $day;
    }
    return $days;
}

function isSlotAvailable(string $date, int $hour): bool
{
    $db = getDB();
    $stmt = $db->prepare('SELECT COUNT(*) as cnt FROM reservations WHERE slot_date = ? AND slot_time = ?');
    $stmt->execute([$date, $hour]);
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

    // Slot (date|hour)
    $slot = parseSlotValue($data['slot'] ?? null);
    if (!$slot || !isValidSlot($slot['date'], $slot['hour'])) {
        $errors[] = '有効な日時枠を選択してください。';
        $slot_date = '';
        $slot_time = 0;
    } else {
        $slot_date = $slot['date'];
        $slot_time = $slot['hour'];
    }

    return [
        'errors'    => $errors,
        'name'      => $name,
        'email'     => $email,
        'x_account' => $x_account,
        'slot_date' => $slot_date,
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
