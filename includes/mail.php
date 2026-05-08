<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

function createMailer(): PHPMailer
{
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = SMTP_SECURE === 'ssl'
        ? PHPMailer::ENCRYPTION_SMTPS
        : PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = SMTP_PORT;
    $mail->CharSet    = 'UTF-8';
    $mail->Encoding   = 'base64';
    $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
    $mail->addReplyTo(MAIL_FROM, MAIL_FROM_NAME);

    return $mail;
}

function sendConfirmationEmail(
    string $name,
    string $email,
    string $slotDate,
    int $slotTime,
    int $rubberTrial = 0,
    string $notes = ''
): bool
{
    $dayInfo = findEventDay($slotDate);
    $dayLabel = $dayInfo['label'] ?? $slotDate;
    $shortLabel = $dayInfo['short'] ?? $slotDate;
    $slotLabelStr = slotLabel($slotTime);
    $formattedPrice = number_format(EVENT_PRICE);
    $eventVenue = EVENT_VENUE;
    $rubberTrialLabel = $rubberTrial ? '希望する' : '希望しない';
    $notesText = $notes !== '' ? $notes : 'なし';

    $subject = '【バキューム&ヒトイヌ体験会】ご予約確認 - ' . $shortLabel . ' ' . $slotLabelStr . 'の枠';

    $body = <<<EOT
{$name} 様

バキューム&ヒトイヌ体験会へのご予約ありがとうございます。
以下の内容で予約を承りました。

━━━━━━━━━━━━━━━━━━━━━━━
■ 予約内容
━━━━━━━━━━━━━━━━━━━━━━━
体験：バキュームベッド･キューブ＆ヒトイヌ体験（セット）
ラバースーツ試着：{$rubberTrialLabel}
備考：{$notesText}
日時：{$dayLabel} {$slotLabelStr}
会場：{$eventVenue}
参加費：¥{$formattedPrice}

━━━━━━━━━━━━━━━━━━━━━━━

■ 注意事項
・当日は体調の良い状態でお越しください
・会場には開始時間の10分前からお入りいただけます
・参加費は当日現金でのお支払いとなります

ご不明な点がございましたら、X（Twitter）のDMまでお問い合わせください。
@shun_rubber
もしくは
オルタフェティッシュ市川哲也070-5087-9619
━━━━━━━━━━━━━━━━━━━━━━━
本メールは自動送信です。
EOT;

    $mail = null;
    try {
        $mail = createMailer();
        $mail->addAddress($email, $name);

        $mail->Subject = $subject;
        $mail->Body    = $body;

        return $mail->send();
    } catch (PHPMailerException $e) {
        error_log('[PHPMailer] ' . ($mail ? $mail->ErrorInfo : $e->getMessage()));
        return false;
    }
}

function sendAdminNotificationEmail(
    string $name,
    string $email,
    string $xAccount,
    string $slotDate,
    int $slotTime,
    int $rubberTrial = 0,
    string $notes = ''
): bool
{
    $adminEmails = [
        '112syunsaku112@gmail.com',
        'ichihara@alt-fetish.com',
    ];

    $dayInfo = findEventDay($slotDate);
    $dayLabel = $dayInfo['label'] ?? $slotDate;
    $shortLabel = $dayInfo['short'] ?? $slotDate;
    $slotLabelStr = slotLabel($slotTime);
    $eventVenue = EVENT_VENUE;
    $xAccountLabel = $xAccount !== '' ? '@' . $xAccount : 'なし';
    $rubberTrialLabel = $rubberTrial ? '希望する' : '希望しない';
    $notesText = $notes !== '' ? $notes : 'なし';

    $subject = '【予約通知】バキューム&ヒトイヌ体験会 - ' . $shortLabel . ' ' . $slotLabelStr . 'の枠';

    $body = <<<EOT
新しい予約が入りました。

━━━━━━━━━━━━━━━━━━━━━━━
■ 予約内容
━━━━━━━━━━━━━━━━━━━━━━━
お名前：{$name}
メール：{$email}
X ID：{$xAccountLabel}
体験：バキュームベッド･キューブ＆ヒトイヌ体験（セット）
ラバースーツ試着：{$rubberTrialLabel}
備考：{$notesText}
日時：{$dayLabel} {$slotLabelStr}
会場：{$eventVenue}
━━━━━━━━━━━━━━━━━━━━━━━

管理画面で詳細を確認してください。
EOT;

    $mail = null;
    try {
        $mail = createMailer();
        foreach ($adminEmails as $adminEmail) {
            $mail->addAddress($adminEmail);
        }
        $mail->Subject = $subject;
        $mail->Body    = $body;

        return $mail->send();
    } catch (PHPMailerException $e) {
        error_log('[PHPMailer admin] ' . ($mail ? $mail->ErrorInfo : $e->getMessage()));
        return false;
    }
}
