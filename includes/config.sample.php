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

// --- Event constants ---
define('EVENT_TITLE', 'バキューム&人犬体験会');
define('EVENT_DATE', '2025年5月27日（土）');
define('EVENT_DATE_SHORT', '5.27');
define('EVENT_VENUE', '芳賀書店ビル');
define('EVENT_PRICE', 6000);
define('SLOT_CAPACITY', 4);
define('SLOT_HOURS', [10, 11, 12, 14, 15, 16, 17, 18]);
define('SLOT_DURATION', 50); // minutes
