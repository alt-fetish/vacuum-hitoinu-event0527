<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

$loggedIn = !empty($_SESSION['admin_logged_in']);
$loginError = getFlash('login_error');
$adminMsg = getFlash('admin_msg');

if ($loggedIn) {
    $db = getDB();

    // Total count
    $totalCount = (int)$db->query('SELECT COUNT(*) FROM reservations')->fetchColumn();

    // Per-(date, slot) counts
    $slotCounts = getSlotCounts();

    // 日ごとの予約数合計
    $dayTotals = [];
    foreach (EVENT_DAYS as $day) {
        $sum = 0;
        foreach ($day['hours'] as $hour) {
            $sum += $slotCounts[$day['date']][$hour] ?? 0;
        }
        $dayTotals[$day['date']] = $sum;
    }

    // All reservations (新しい順 = 日付・時刻昇順)
    $stmt = $db->query('SELECT * FROM reservations ORDER BY slot_date ASC, slot_time ASC, created_at ASC');
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

    <!-- 全体統計 -->
    <div class="admin-stats">
        <div class="stat-card">
            <div class="stat-num"><?= $totalCount ?></div>
            <div class="stat-label">総予約数</div>
        </div>
        <?php foreach (EVENT_DAYS as $day): ?>
            <div class="stat-card">
                <div class="stat-num"><?= $dayTotals[$day['date']] ?></div>
                <div class="stat-label"><?= h($day['short']) ?> 計</div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- 日別 × 時間枠 統計 -->
    <?php foreach (EVENT_DAYS as $day): ?>
        <div class="day-block">
            <div class="day-block-header">
                <span class="day-block-date"><?= h($day['short']) ?></span>
                <span class="day-block-label"><?= h($day['label']) ?></span>
            </div>
            <div class="admin-stats" style="margin-bottom:0;">
                <?php foreach ($day['hours'] as $hour): ?>
                    <?php $cnt = $slotCounts[$day['date']][$hour] ?? 0; ?>
                    <div class="stat-card">
                        <div class="stat-num"><?= $cnt ?>/<?= SLOT_CAPACITY ?></div>
                        <div class="stat-label"><?= $hour ?>:00</div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if ($adminMsg): ?>
        <div class="msg msg-success"><?= h($adminMsg) ?></div>
    <?php endif; ?>

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
                        <th>日付</th>
                        <th>時間枠</th>
                        <th>名前</th>
                        <th>メール</th>
                        <th>X ID</th>
                        <th>申込日時</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $r): ?>
                        <?php
                        $rDayInfo = findEventDay($r['slot_date']);
                        $rDayShort = $rDayInfo['short'] ?? $r['slot_date'];
                        ?>
                        <tr>
                            <td><?= (int)$r['id'] ?></td>
                            <td><?= h($rDayShort) ?></td>
                            <td><?= h(slotLabel((int)$r['slot_time'])) ?></td>
                            <td><?= h($r['name']) ?></td>
                            <td><?= h($r['email']) ?></td>
                            <td><?= $r['x_account'] ? '@' . h($r['x_account']) : '-' ?></td>
                            <td><?= h($r['created_at']) ?></td>
                            <td>
                                <form action="delete.php" method="POST" onsubmit="return confirm('予約 #<?= (int)$r['id'] ?> (<?= h($r['name']) ?>) を削除します。よろしいですか？');" style="margin:0;">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                                    <button type="submit" class="btn btn-danger" style="padding:4px 10px; font-size:0.8rem;">削除</button>
                                </form>
                            </td>
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
