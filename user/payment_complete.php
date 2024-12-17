<?php

require __DIR__ . '/../../vendor/autoload.php';

use Picqer\Barcode\BarcodeGeneratorPNG;

session_start();  // セッション開始

// ユーザーIDの確認
if (isset($_SESSION['id'])) {
    $user_id = $_SESSION['id'];
} else {
    echo "ログインできていません";
    exit;
}

// データベース接続
include '../../db_open.php';

if ($dbh) {
    // カートのアイテムを取得する
    $selectSql = "SELECT `cart_id`, `user_id`, `shop_id`, `quantity`, `trade_situation`, `order_date` FROM `cart` WHERE `user_id` = :user_id";
    $stmt = $dbh->prepare($selectSql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($cartItems)) {
            echo "カートにアイテムがありません。";
            exit;
        }

        // メールメッセージの準備
        $message = "お客様がカートの商品を購入しました。購入詳細は以下の通りです。\n\n";

        foreach ($cartItems as $item) {
            $message .= "商品ID: " . $item['shop_id'] . "\n";
            $message .= "数量: " . $item['quantity'] . "\n";
            $message .= "注文日: " . $item['order_date'] . "\n\n";
        }

        // メールアドレスを取得
        $mailSql = "SELECT mail FROM user WHERE user_id = :user_id";
        $mailStmt = $dbh->prepare($mailSql);
        $mailStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $mailStmt->execute();

        $mailRow = $mailStmt->fetch(PDO::FETCH_ASSOC);
        if ($mailRow && isset($mailRow['mail'])) {
            $to = $mailRow['mail'];
        } else {
            echo "メールアドレスのユーザーが見つかりませんでした";
            exit;
        }

        // バーコードを生成
        $generator = new BarcodeGeneratorPNG();
        $barcodeData = "OrderID:" . $cartItems[0]['cart_id']; // カートIDをバーコードに使用
        $barcode = $generator->getBarcode($barcodeData, $generator::TYPE_CODE_128);

        // バーコードを一時ファイルとして保存
        $barcodeFile = tempnam(sys_get_temp_dir(), 'barcode') . '.png';
        file_put_contents($barcodeFile, $barcode);

        // メール送信の準備
        $subject = "ご購入ありがとうございます";
        $boundary = md5(uniqid()); // ユニークなバウンダリ文字列
        $headers = "From: sotsuken@st.yoshida-g.ac.jp\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

        // メール本文
        $emailBody = "--$boundary\r\n";
        $emailBody .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
        $emailBody .= $message . "\r\n\r\n";

        // バーコード画像を添付
        $emailBody .= "--$boundary\r\n";
        $emailBody .= "Content-Type: image/png; name=\"barcode.png\"\r\n";
        $emailBody .= "Content-Transfer-Encoding: base64\r\n";
        $emailBody .= "Content-Disposition: attachment; filename=\"barcode.png\"\r\n\r\n";
        $emailBody .= chunk_split(base64_encode(file_get_contents($barcodeFile))) . "\r\n";
        $emailBody .= "--$boundary--";

        // メール送信
        if (mail($to, $subject, $emailBody, $headers)) {
            echo "ご購入ありがとうございます。確認のメールが送信されました。";
        } else {
            echo "メール送信に失敗しました。";
        }

        // 一時ファイルを削除
        unlink($barcodeFile);
    } else {
        echo "エラー: カートアイテムの取得に失敗しました。";
    }
} else {
    echo "DB接続エラー";
}

// データベース接続を閉じる
$dbh = null;
?>
