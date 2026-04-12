<?php
session_start();
require_once __DIR__ . '/includes/functions.php';

$slots = getSlotAvailability();
$errors = getFlash('errors');
$formData = getFlash('form_data', []);
$csrfToken = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h(EVENT_TITLE) ?> | <?= h(EVENT_DATE) ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<!-- Hero -->
<div class="hero">
    <div class="hero-date"><?= h(EVENT_DATE_SHORT) ?></div>
    <h1 class="hero-title"><?= h(EVENT_TITLE) ?></h1>
    <p class="hero-venue"><?= h(EVENT_VENUE) ?></p>
</div>

<div class="container">

    <!-- Event Info -->
    <div class="section">
        <h2 class="section-title">イベント概要</h2>
        <p>バキュームベッドと人犬、2つの体験がセットで楽しめるスペシャルイベント。<br>
        全身を包み込むバキュームベッドの圧迫感と、人犬プレイの非日常をぜひ体感してください。</p>

        <div class="info-grid mt-16">
            <div class="info-card">
                <dl>
                    <dt>開催日</dt>
                    <dd><?= h(EVENT_DATE) ?></dd>
                </dl>
            </div>
            <div class="info-card">
                <dl>
                    <dt>会場</dt>
                    <dd><?= h(EVENT_VENUE) ?></dd>
                </dl>
            </div>
            <div class="info-card">
                <dl>
                    <dt>参加費</dt>
                    <dd>&yen;<?= number_format(EVENT_PRICE) ?></dd>
                </dl>
            </div>
            <div class="info-card">
                <dl>
                    <dt>体験時間</dt>
                    <dd>1枠 約<?= SLOT_DURATION ?>分</dd>
                </dl>
            </div>
        </div>
    </div>

    <!-- Slot Availability -->
    <div class="section">
        <h2 class="section-title">空き状況</h2>

        <p class="slot-group-title">午前の部</p>
        <div class="slot-list">
            <?php foreach ($slots as $s): ?>
                <?php if ($s['period'] !== '午前') continue; ?>
                <div class="slot-card <?= $s['available'] ? '' : 'is-full' ?>"
                     <?php if ($s['available']): ?>onclick="selectSlot(<?= $s['hour'] ?>)" style="cursor:pointer"<?php endif; ?>>
                    <div>
                        <span class="slot-time"><?= h($s['label']) ?></span>
                        <span class="slot-remaining">（残り <?= $s['remaining'] ?>/<?= SLOT_CAPACITY ?>）</span>
                    </div>
                    <span class="pill <?= $s['available'] ? 'pill-ok' : 'pill-full' ?>">
                        <?= $s['available'] ? '受付中' : '満席' ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>

        <p class="slot-group-title">午後の部</p>
        <div class="slot-list">
            <?php foreach ($slots as $s): ?>
                <?php if ($s['period'] !== '午後') continue; ?>
                <div class="slot-card <?= $s['available'] ? '' : 'is-full' ?>"
                     <?php if ($s['available']): ?>onclick="selectSlot(<?= $s['hour'] ?>)" style="cursor:pointer"<?php endif; ?>>
                    <div>
                        <span class="slot-time"><?= h($s['label']) ?></span>
                        <span class="slot-remaining">（残り <?= $s['remaining'] ?>/<?= SLOT_CAPACITY ?>）</span>
                    </div>
                    <span class="pill <?= $s['available'] ? 'pill-ok' : 'pill-full' ?>">
                        <?= $s['available'] ? '受付中' : '満席' ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Reservation Form -->
    <div class="section" id="reserve">
        <h2 class="section-title">予約フォーム</h2>

        <?php if ($errors): ?>
            <div class="msg msg-error">
                <ul style="margin:0; padding-left:1.2em;">
                    <?php foreach ($errors as $e): ?>
                        <li><?= h($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="form-card">
            <form action="reserve.php" method="POST">
                <?= csrfField() ?>

                <div class="field">
                    <label for="slot_time">希望時間枠 <span style="color:var(--red)">*</span></label>
                    <p class="slot-group-title mb-8">午前の部</p>
                    <div class="slot-radio-group">
                        <?php foreach ($slots as $s): ?>
                            <?php if ($s['period'] !== '午前') continue; ?>
                            <label id="slot-radio-<?= $s['hour'] ?>" class="<?= $s['available'] ? '' : 'is-full' ?>">
                                <input type="radio" name="slot_time" value="<?= $s['hour'] ?>"
                                    <?= ($formData['slot_time'] ?? '') == $s['hour'] ? 'checked' : '' ?>
                                    <?= $s['available'] ? '' : 'disabled' ?>>
                                <span><?= h($s['label']) ?> （残<?= $s['remaining'] ?>）</span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <p class="slot-group-title mb-8">午後の部</p>
                    <div class="slot-radio-group">
                        <?php foreach ($slots as $s): ?>
                            <?php if ($s['period'] !== '午後') continue; ?>
                            <label id="slot-radio-<?= $s['hour'] ?>" class="<?= $s['available'] ? '' : 'is-full' ?>">
                                <input type="radio" name="slot_time" value="<?= $s['hour'] ?>"
                                    <?= ($formData['slot_time'] ?? '') == $s['hour'] ? 'checked' : '' ?>
                                    <?= $s['available'] ? '' : 'disabled' ?>>
                                <span><?= h($s['label']) ?> （残<?= $s['remaining'] ?>）</span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="field">
                    <label for="name">お名前 <span style="color:var(--red)">*</span></label>
                    <input type="text" id="name" name="name" required maxlength="100"
                           value="<?= h($formData['name'] ?? '') ?>"
                           placeholder="例：田中太郎">
                </div>

                <div class="field">
                    <label for="email">メールアドレス <span style="color:var(--red)">*</span></label>
                    <input type="email" id="email" name="email" required
                           value="<?= h($formData['email'] ?? '') ?>"
                           placeholder="例：example@mail.com">
                    <p class="hint">確認メールを送信します</p>
                </div>

                <div class="field">
                    <label for="x_account">X (Twitter) ID</label>
                    <input type="text" id="x_account" name="x_account" maxlength="50"
                           value="<?= h($formData['x_account'] ?? '') ?>"
                           placeholder="@username">
                    <p class="hint">任意</p>
                </div>

                <button type="submit" class="btn btn-primary">予約する</button>
            </form>
        </div>
    </div>

</div>

<footer class="footer">
    <p><?= h(EVENT_TITLE) ?> | <?= h(EVENT_DATE) ?></p>
    <p class="mt-16"><a href="admin/">管理画面</a></p>
</footer>

<script>
function selectSlot(hour) {
    var label = document.getElementById('slot-radio-' + hour);
    if (!label) return;
    var radio = label.querySelector('input[type="radio"]');
    if (radio && !radio.disabled) {
        radio.checked = true;
        label.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}
</script>
</body>
</html>
