<?php
// Gmail IMAPサーバーの設定
$hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
$username = '231129@st.yoshida-g.ac.jp';  // Gmailアドレス
$password = 'Ryo130626';  // Gmailパスワードまたはアプリパスワード

// メールボックスに接続
$inbox = imap_open($hostname, $username, $password) or die('Cannot connect to Gmail: ' . imap_last_error());

// 最新の10件のメールを取得
$emails = imap_search($inbox, 'ALL');

if ($emails) {
    // メールを新しい順にソート
    rsort($emails);

    // 最新10件を表示
    $count = 0;
    foreach ($emails as $email_number) {
        if ($count >= 10) break;  // 10件のみ表示
        
        // メールの概要を取得
        $overview = imap_fetch_overview($inbox, $email_number, 0)[0];
        $message = imap_fetchbody($inbox, $email_number, 1);  // メールの本文を取得

        echo "<h2>件名: " . htmlspecialchars($overview->subject) . "</h2>";
        echo "<p>送信者: " . htmlspecialchars($overview->from) . "</p>";
        echo "<p>日付: " . htmlspecialchars($overview->date) . "</p>";
        echo "<p>本文: " . nl2br(htmlspecialchars($message)) . "</p>";
        echo "<hr>";

        $count++;
    }
} else {
    echo "メールが見つかりません。";
}

// メールボックスを閉じる
imap_close($inbox);
?>
