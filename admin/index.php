<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

$loggedIn = !empty($_SESSION['admin_logged_in']);
$loginError = getFlash('login_error');

if ($loggedIn) {
    $db = getDB();

    // Total count
    $totalCount = (int)$db->query('SELECT COUNT(*) FROM reservations')->fetchColumn();

    // Per-slot counts
    $slotCounts = getSlotCounts();

    // All reservations
    $stmt = $db->query('SELECT * FROM reservations ORDER BY slot_time ASC, created_at ASC');
    $reservations = $stmt->fetchAll();
}

$csrfToken = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理画面 | <?= h(EVENT_TITLE) ?></title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="container">

<?php if (!$loggedIn): ?>
    <!-- Login Form -->
    <div class="login-form">
        <h1 class="section-title">管理者ログイン</h1>

        <?php if ($loginError): ?>
            <div class="msg msg-error"><?= h($loginError) ?></div>
        <?php endif; ?>

        <div class="form-card">
            <form action="login.php" method="POST">
                <?= csrfField() ?>
                <div class="field">
                    <label for="username">ユーザー名</label>
                    <input type="text" id="username" name="username" required autocomplete="username">
                </div>
                <div class="field">
                    <label for="password">パスワード</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password">
                </div>
                <button type="submit" class="btn btn-primary">ログイン</button>
            </form>
        </div>
        <p class="text-center mt-16"><a href="../index.php">トップページに戻る</a></p>
    </div>

<?php else: ?>
    <!-- Dashboard -->
    <div class="admin-header">
        <h1><?= h(EVENT_TITLE) ?> 管理画面</h1>
        <a href="logout.php" class="btn btn-secondary" style="padding:8px 16px; font-size:0.85rem;">ログアウト</a>
    </div>

    <!-- Stats -->
    <div class="admin-stats">
        <div class="stat-card">
            <div class="stat-num"><?= $totalCount ?></div>
            <div class="stat-label">総予約数</div>
        </div>
        <?php foreach (SLOT_HOURS as $hour): ?>
            <div class="stat-card">
                <div class="stat-num"><?= $slotCounts[$hour] ?>/<?= SLOT_CAPACITY ?></div>
                <div class="stat-label"><?= $hour ?>:00</div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Actions -->
    <div class="admin-actions">
        <a href="export.php" class="btn btn-secondary" style="padding:8px 16px; font-size:0.85rem;">CSV ダウンロード</a>
        <a href="../index.php" class="btn btn-secondary" style="padding:8px 16px; font-size:0.85rem;">トップページ</a>
    </div>

    <!-- Reservation Table -->
    <?php if (empty($reservations)): ?>
        <p style="color:var(--text-muted);">予約はまだありません。</p>
    <?php else: ?>
        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>時間枠</th>
                        <th>名前</th>
                        <th>メール</th>
                        <th>X ID</th>
                        <th>申込日時</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $r): ?>
                        <tr>
                            <td><?= (int)$r['id'] ?></td>
                            <td><?= h(slotLabel((int)$r['slot_time'])) ?></td>
                            <td><?= h($r['name']) ?></td>
                            <td><?= h($r['email']) ?></td>
                            <td><?= $r['x_account'] ? '@' . h($r['x_account']) : '-' ?></td>
                            <td><?= h($r['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

<?php endif; ?>

</div>

<footer class="footer">
    <p><?= h(EVENT_TITLE) ?> 管理画面</p>
</footer>

</body>
</html>
