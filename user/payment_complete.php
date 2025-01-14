<?php

// echo "Current Directory: " . getcwd() . "<br>";
// echo "Trying to require: " . realpath(__DIR__ . '/../vendor/autoload.php') . "<br>";
// require __DIR__ . '/../vendor/autoload.php';

// require '../../vendor/autoload.php';


// データベース接続設定
include '../../db_open.php';
session_start();  // セッション開始

// ユーザーIDの確認
if (isset($_SESSION['id'])) {
    $user_id = $_SESSION['id'];
} else {
    echo "ログインできていません";
    exit;
}
$address = $_POST['selected_address']; // 送付先住所の取得

if ($dbh) {
    try {
        // カートのアイテムを取得する
        $selectSql = "SELECT `cart_id`, `user_id`, `shop_id`, `quantity`, `trade_situation`, `order_date` FROM `cart` WHERE `user_id` = :user_id";
        $stmt = $dbh->prepare($selectSql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // カートアイテムを全て取得
            $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($cartItems)) {
                echo "カートにアイテムがありません。";
                exit;
            }

            // カートアイテムを処理する
            foreach ($cartItems as $item) {
                // shop_id を使って brand_id を取得
                $brandSql = "SELECT brand_id FROM shop WHERE shop_id = :shop_id";
                $stmtBrand = $dbh->prepare($brandSql);
                $stmtBrand->bindParam(':shop_id', $item['shop_id'], PDO::PARAM_INT);

                if (!$stmtBrand->execute()) {
                    throw new Exception("ブランド情報の取得に失敗しました: shop_id = " . $item['shop_id']);
                }

                // brand_id を取得
                $brandRow = $stmtBrand->fetch(PDO::FETCH_ASSOC);
                if ($brandRow) {
                    $brand_id = $brandRow['brand_id'];

                    // cart_detail に挿入
                    $InsertSql = "INSERT INTO `cart_detail` (`cart_id`, `user_id`, `shop_id`, `brand_id`, `quantity`, `trade_situation`, `order_date`, `send_address`) 
                                  VALUES (:cart_id, :user_id, :shop_id, :brand_id, :quantity, 1, :order_date, :send_address)";
                    $stmtInsert = $dbh->prepare($InsertSql);

                    // 変数をバインド
                    $stmtInsert->bindParam(':cart_id', $item['cart_id'], PDO::PARAM_INT);
                    $stmtInsert->bindParam(':user_id', $item['user_id'], PDO::PARAM_INT);
                    $stmtInsert->bindParam(':shop_id', $item['shop_id'], PDO::PARAM_INT);
                    $stmtInsert->bindParam(':brand_id', $brand_id, PDO::PARAM_INT);
                    $stmtInsert->bindParam(':quantity', $item['quantity'], PDO::PARAM_INT);
                    $stmtInsert->bindParam(':order_date', $item['order_date'], PDO::PARAM_STR);
                    $stmtInsert->bindParam(':send_address', $address, PDO::PARAM_STR);  // 送付先住所

                    // 挿入を実行
                    if (!$stmtInsert->execute()) {
                        throw new Exception("カート詳細の挿入に失敗しました: cart_id = " . $item['cart_id']);
                    }
                } else {
                    throw new Exception("ブランド情報が見つかりません: shop_id = " . $item['shop_id']);
                }
            }

            // カートの削除
            $deleteCartSql = "DELETE FROM cart WHERE user_id = :user_id";
            $stmtDelete = $dbh->prepare($deleteCartSql);
            $stmtDelete->bindParam(':user_id', $user_id, PDO::PARAM_INT);

            if (!$stmtDelete->execute()) {
                throw new Exception("カートの削除に失敗しました: user_id = " . $user_id);
            }

            $message = "お客様がカートの商品を購入しました。購入詳細は以下の通りです。\n\n";
            foreach ($cartItems as $item) {
                $message .= "商品ID: " . $item['shop_id'] . "\n";
                $message .= "数量: " . $item['quantity'] . "\n";
                $message .= "注文日: " . $item['order_date'] . "\n\n";
                // echo "<img src='C:\Users\student\Pictures\barcode-illustration-isolated_23-2150584086.avif'>";
            }
            // $generator = new BarcodeGeneratorHTML();
            // echo $generator->getBarcode('123456789', $generator::TYPE_CODE_128);
    
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
    
            // メール送信
            $subject = "ご購入ありがとうございます";
            $boundary = md5(uniqid()); // ユニークなバウンダリ文字列
            $headers = "From: sotsuken@st.yoshida-g.ac.jp\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";
    
            // メール本文
            $emailBody = "--$boundary\r\n";
            $emailBody .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
            $emailBody .= $message . "\r\n\r\n";
    
            if (mail($to, $subject, $emailBody, $headers)) {
                echo "ご購入ありがとうございます。確認のメールが送信されました。";
                echo "<a href='toppage.php'>戻る";
            } else {
                echo "メール送信に失敗しました。";
            }
        } else {
            throw new Exception("カートアイテムの取得に失敗しました。");
        }
    } catch (Exception $e) {
        echo "エラー: " . $e->getMessage();
    }
} else {
    echo "DB接続エラー";
}

// データベース接続を閉じる
$dbh = null;

?>
