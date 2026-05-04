<?php
session_start();
require_once __DIR__ . '/includes/functions.php';

$reservation = $_SESSION['last_reservation'] ?? null;
unset($_SESSION['last_reservation']);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>予約完了 | <?= h(EVENT_TITLE) ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="container">
    <div class="thanks-card">
        <?php if ($reservation): ?>
            <h1>予約が完了しました</h1>
            <p>ご予約ありがとうございます。</p>

            <div class="thanks-detail">
                <dl>
                    <dt>お名前</dt>
                    <dd><?= h($reservation['name']) ?></dd>
                    <dt>体験</dt>
                    <dd>バキュームベッド＆人犬体験</dd>
                    <dt>日時</dt>
                    <dd><?= h($reservation['day_label']) ?> <?= h($reservation['slot_label']) ?></dd>
                    <dt>会場</dt>
                    <dd><?= h(EVENT_VENUE) ?></dd>
                    <dt>参加費</dt>
                    <dd>&yen;<?= number_format(EVENT_PRICE) ?></dd>
                </dl>
            </div>

            <p class="hint" style="color:var(--text-muted); font-size:0.85rem;">
                確認メールを <?= h($reservation['email']) ?> に送信しました。<br>
                届かない場合は迷惑メールフォルダをご確認ください。
            </p>
        <?php else: ?>
            <h1>予約情報</h1>
            <p>予約情報が見つかりません。<br>既に予約は完了している可能性があります。</p>
        <?php endif; ?>

        <div class="mt-24">
            <a href="index.php" class="btn btn-secondary">トップページに戻る</a>
        </div>
    </div>
</div>

<footer class="footer">
    <p><?= h(EVENT_TITLE) ?> | <?= h(getEventDateLong()) ?></p>
</footer>

</body>
</html>
