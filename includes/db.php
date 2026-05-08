<?php
require_once __DIR__ . '/config.php';

function getDB(): PDO
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $dir = dirname(DB_PATH);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA journal_mode=WAL');
    $pdo->exec('PRAGMA busy_timeout=5000');

    // 新規DB用: マルチデイ対応スキーマ
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS reservations (
            id         INTEGER PRIMARY KEY AUTOINCREMENT,
            name       TEXT    NOT NULL,
            email      TEXT    NOT NULL,
            x_account  TEXT    DEFAULT NULL,
            rubber_trial INTEGER NOT NULL DEFAULT 0,
            notes      TEXT    NOT NULL DEFAULT '',
            slot_date  TEXT    NOT NULL,
            slot_time  INTEGER NOT NULL CHECK (slot_time IN (10,11,12,14,15,16,17,18)),
            created_at TEXT    NOT NULL DEFAULT (datetime('now','localtime')),
            UNIQUE(email, slot_date, slot_time)
        )
    ");

    // 旧DB（slot_date無し）からのマイグレーション
    $cols = $pdo->query("PRAGMA table_info(reservations)")->fetchAll();
    $hasSlotDate = false;
    foreach ($cols as $c) {
        if ($c['name'] === 'slot_date') {
            $hasSlotDate = true;
            break;
        }
    }
    if (!$hasSlotDate) {
        $pdo->exec('BEGIN');
        try {
            $pdo->exec('ALTER TABLE reservations RENAME TO reservations_legacy');
            $pdo->exec("
                CREATE TABLE reservations (
                    id         INTEGER PRIMARY KEY AUTOINCREMENT,
                    name       TEXT    NOT NULL,
                    email      TEXT    NOT NULL,
                    x_account  TEXT    DEFAULT NULL,
                    rubber_trial INTEGER NOT NULL DEFAULT 0,
                    notes      TEXT    NOT NULL DEFAULT '',
                    slot_date  TEXT    NOT NULL,
                    slot_time  INTEGER NOT NULL CHECK (slot_time IN (10,11,12,14,15,16,17,18)),
                    created_at TEXT    NOT NULL DEFAULT (datetime('now','localtime')),
                    UNIQUE(email, slot_date, slot_time)
                )
            ");
            $pdo->exec("
                INSERT INTO reservations (id, name, email, x_account, rubber_trial, notes, slot_date, slot_time, created_at)
                SELECT id, name, email, x_account, 0, '', '2026-06-27', slot_time, created_at
                FROM reservations_legacy
            ");
            $pdo->exec('DROP TABLE reservations_legacy');
            $pdo->exec('COMMIT');
        } catch (Throwable $e) {
            $pdo->exec('ROLLBACK');
            throw $e;
        }
    }

    $cols = $pdo->query("PRAGMA table_info(reservations)")->fetchAll();
    $columnNames = array_column($cols, 'name');
    if (!in_array('rubber_trial', $columnNames, true)) {
        $pdo->exec('ALTER TABLE reservations ADD COLUMN rubber_trial INTEGER NOT NULL DEFAULT 0');
    }
    if (!in_array('notes', $columnNames, true)) {
        $pdo->exec("ALTER TABLE reservations ADD COLUMN notes TEXT NOT NULL DEFAULT ''");
    }

    return $pdo;
}
