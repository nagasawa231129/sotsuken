<?php
// Gmail IMAPサーバーの設定
$hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
$username = 'liangzhngz@gmail.com'; 
$password = 'dlmyptfgiwyaobfx'; 

// メールボックスに接続
$inbox = imap_open($hostname, $username, $password) or die('Cannot connect to Gmail: ' . imap_last_error());

// 未読メールを取得
$emails = imap_search($inbox, 'UNSEEN');

$unreadCount = 0;  // 未読メール数の初期化

if ($emails) {
    // 未読メール数をカウント
    $unreadCount = count($emails);
}

// メールボックスを閉じる
imap_close($inbox);

// 未読メール数を返す
return $unreadCount;
?>
