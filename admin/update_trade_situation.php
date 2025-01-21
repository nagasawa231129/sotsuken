<?php
// データベース接続設定
include './../../db_open.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cartIds = $_POST['cart_id'];

    foreach ($cartIds as $cartId) {
        // 注文の取引状況を「入金待ち」に更新
        $stmt = $dbh->prepare('UPDATE cart_detail SET trade_situation = 1 , scanned = 0 WHERE cart_id = ?');
        $stmt->execute([$cartId]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $userMail = $_POST['user_mail'][0]; // 配列として送信されたメールアドレスを取得
    
        // メールの送信先
        $to = $userMail;
      
      
        // 件名
        $subject = "卒研TOWN 荷物発送について";
    
        // メール本文
        $message = "平素より卒研TOWNをご利用いただき、誠にありがとうございます。\n\n 入金の確認が取れませんでした。\n\n再度入金をお願いいたします。";
    
        // ヘッダー情報
        $headers = "From: no-reply@yourdomain.com\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
        // メール送信
        if (mail($to, $subject, $message, $headers)) {
            echo "メールが送信されました。";
        } else {
            echo "メールの送信に失敗しました。";
        }
    }
}
    ?>
