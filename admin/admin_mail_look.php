<?php
// Gmail IMAPサーバーの設定
$hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
$username = 'liangzhngz@gmail.com';
$password = 'dlmyptfgiwyaobfx'; 

// メールボックスに接続
$inbox = imap_open($hostname, $username, $password) or die('Cannot connect to Gmail: ' . imap_last_error());

// 未読メールを取得
$emails = imap_search($inbox, 'UNSEEN');

if ($emails) {
    // メールを新しい順にソート
    rsort($emails);

    // 最新10件を表示
    $count = 0;
    foreach ($emails as $email_number) {
        if ($count >= 10) break;  // 10件のみ表示
        
        // メールの概要を取得
        $overview = imap_fetch_overview($inbox, $email_number, 0)[0];

        // 件名と送信者をUTF-8に変換（文字化け対策）
        $subject = imap_utf8($overview->subject);
        $from = imap_utf8($overview->from);

        // メールの構造を取得
        $structure = imap_fetchstructure($inbox, $email_number);

        // メールの本文を取得（複数部分に対応）
        $message = '';
        if (isset($structure->parts) && count($structure->parts)) {
            foreach ($structure->parts as $partNum => $part) {
                $part_data = imap_fetchbody($inbox, $email_number, $partNum + 1);
                
                // Base64エンコードされている場合
                if ($part->encoding == 3) {
                    $part_data = base64_decode($part_data);
                } 
                // Quoted-Printableエンコードされている場合
                elseif ($part->encoding == 4) {
                    $part_data = quoted_printable_decode($part_data);
                }

                // 文字エンコーディングをUTF-8に変換
                $part_data = mb_convert_encoding($part_data, "UTF-8", "auto");

                $message .= $part_data;
            }
        }

        // 結果の表示
        echo "<h2>件名: " . htmlspecialchars($subject) . "</h2>";
        echo "<p>送信者: " . htmlspecialchars($from) . "</p>";
        echo "<p>日付: " . htmlspecialchars($overview->date) . "</p>";
        echo "<p>本文: " . nl2br(htmlspecialchars($message)) . "</p>";
        echo "<hr>";

        $count++;
    }
} else {
    echo "未読メールはありません。";
}

// メールボックスを閉じる
imap_close($inbox);
?>
