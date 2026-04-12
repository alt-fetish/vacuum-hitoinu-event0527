<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

/**
 * Encode a UTF-8 string for use in email headers (RFC 2047 Base64).
 * Falls back to a pure-PHP implementation if mbstring is unavailable.
 */
function encodeMimeHeader(string $str): string
{
    if (function_exists('mb_encode_mimeheader')) {
        return mb_encode_mimeheader($str, 'UTF-8', 'B');
    }
    return '=?UTF-8?B?' . base64_encode($str) . '?=';
}

function sendConfirmationEmail(string $name, string $email, int $slotTime): bool
{
    $slotLabelStr = slotLabel($slotTime);
    $formattedPrice = number_format(EVENT_PRICE);
    $eventDate = EVENT_DATE;
    $eventVenue = EVENT_VENUE;

    $subject = '【5.27体験会】ご予約確認 - ' . $slotLabelStr . 'の枠';

    $body = <<<EOT
{$name} 様

5.27 バキューム&人犬体験会へのご予約ありがとうございます。
以下の内容で予約を承りました。

━━━━━━━━━━━━━━━━━━━━━━━
■ 予約内容
━━━━━━━━━━━━━━━━━━━━━━━
体験：バキュームベッド＆人犬体験（セット）
日時：{$eventDate} {$slotLabelStr}
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

    $fromName = encodeMimeHeader(MAIL_FROM_NAME);
    $headers = implode("\r\n", [
        'From: ' . $fromName . ' <' . MAIL_FROM . '>',
        'Reply-To: ' . MAIL_FROM,
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: base64',
        'MIME-Version: 1.0',
    ]);

    $encodedSubject = encodeMimeHeader($subject);
    $encodedBody = base64_encode($body);

    return @mail($email, $encodedSubject, $encodedBody, $headers);
}
