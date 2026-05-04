<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

if (empty($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

$db = getDB();
$stmt = $db->query('SELECT * FROM reservations ORDER BY slot_date ASC, slot_time ASC, created_at ASC');
$reservations = $stmt->fetchAll();

$filename = 'reservations_' . date('Ymd_His') . '.csv';

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// BOM for Excel UTF-8 recognition
echo "\xEF\xBB\xBF";

$out = fopen('php://output', 'w');
fputcsv($out, ['ID', '日付', '時間枠', '名前', 'メール', 'X ID', '申込日時']);

foreach ($reservations as $r) {
    $dayInfo = findEventDay($r['slot_date']);
    $dayLabel = $dayInfo['label'] ?? $r['slot_date'];
    fputcsv($out, [
        $r['id'],
        $dayLabel,
        slotLabel((int)$r['slot_time']),
        $r['name'],
        $r['email'],
        $r['x_account'] ? '@' . $r['x_account'] : '',
        $r['created_at'],
    ]);
}

fclose($out);
exit;
