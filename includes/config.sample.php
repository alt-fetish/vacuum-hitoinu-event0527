<?php
// このファイルは Git 管理に含めます。
// 本番 (さくら) 側で includes/config.php にコピーして値を書き換えてください。
// デプロイ時は config.php は上書きされません (.yml で exclude 済み)。

// --- Database ---
define('DB_PATH', __DIR__ . '/../data/reservations.sqlite3');

// --- Admin credentials ---
// 事前にローカルで: php -r "echo password_hash('好きなパスワード', PASSWORD_DEFAULT);"
// を実行し、出力文字列を ADMIN_PASS_HASH に貼り付ける
define('ADMIN_USER', 'admin');
define('ADMIN_PASS_HASH', '$2y$10$REPLACE_WITH_REAL_HASH');

// --- Mail ---
define('MAIL_FROM', 'noreply@example.com');
define('MAIL_FROM_NAME', 'バキューム&人犬体験会');

// --- SMTP (さくらインターネット) ---
// さくらのコントロールパネルで作成したメールアカウントを使う
define('SMTP_HOST', 'xxxx.sakura.ne.jp');       // 初期ドメイン
define('SMTP_PORT', 587);                        // 587 = STARTTLS
define('SMTP_USER', 'noreply@example.com');      // メールアドレス全体
define('SMTP_PASS', 'SMTP_PASSWORD_HERE');       // さくらで設定したパスワード
define('SMTP_SECURE', 'tls');                    // 'tls' (STARTTLS) or 'ssl' (465ポート)

// --- Event constants ---
define('EVENT_TITLE', 'バキューム&人犬体験会');
define('EVENT_DATE', '2026年6月27日（土）・28日（日）');
define('EVENT_DATE_SHORT', '6.27 ／ 6.28');
define('EVENT_VENUE', '芳賀書店ビル');
define('EVENT_PRICE', 6000);
define('SLOT_CAPACITY', 4);

// 開催日ごとの時間枠（日付別の枠管理）
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

// 全日の時間枠を合算したリスト（DBのCHECK制約・互換用）
define('SLOT_HOURS', [10, 11, 12, 14, 15, 16, 17, 18]);
define('SLOT_DURATION', 50); // minutes
