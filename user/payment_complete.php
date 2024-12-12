<?php
// 名前空間のインポートをファイルの先頭に移動
// use Picqer\Barcode\BarcodeGeneratorHTML;
include '../../db_open.php';  // db_open.phpをインクルードして、$dbhを利用できるようにする
// require 'vendor/autoload.php';



session_start();  // セッション開始（セッションを使用してユーザーIDを管理している場合）

if (isset($_SESSION['id'])) {
    $user_id = $_SESSION['id'];
} else {
    echo "ログインできていません";
}

// データベース接続が成功しているか確認（デバッグ用）
if ($dbh) {
    // カートのアイテムを取得するSQL
    $selectSql = "SELECT `cart_id`, `user_id`, `shop_id`, `quantity`, `trade_situation`, `order_date` FROM `cart`";
    $stmt = $dbh->prepare($selectSql);

    if ($stmt->execute()) {
        $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC); // fetchAllで配列を取得
        // $generator = new BarcodeGeneratorHTML();  // インスタンス化

        // バーコードを生成（例えば、"123456789" のバーコード）
        // $barcode = $generator->getBarcode('123456789', BarcodeGeneratorHTML::TYPE_CODE_128);

        // カートが空でないか確認
        if (empty($cartItems)) {
            echo "カートにアイテムがありません。";
            exit;
        }

        // メール用のメッセージを構築
        $message = "お客様がカートの商品を購入しました。購入詳細は以下の通りです。\n\n";

        $updateBuySql = "UPDATE `shop` SET `buy` = 1 WHERE `shop_id` = :shop_id";
        $updateBuyStmt = $dbh->prepare($updateBuySql);

        // カート内容をメールに追加
        foreach ($cartItems as $item) {
            if (isset($item['shop_id'], $item['quantity'], $item['order_date'])) {
                $message .= "商品ID: " . $item['shop_id'] . "\n";
                $message .= "数量: " . $item['quantity'] . "\n";
                $message .= "注文日: " . $item['order_date'] . "\n\n";
                $updateBuyStmt->bindParam(':shop_id', $item['shop_id'], PDO::PARAM_INT);
                if (!$updateBuyStmt->execute()) {
                    echo "エラー: shopテーブルのbuyカラム更新に失敗しました。";
                }
            }
        }
        $barcode;

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

        $subject = "ご購入ありがとうございます";
        $headers = "From: sotsuken@st.yoshida-g.ac.jp\r\n";

        // メール送信
        if (mb_send_mail($to, $subject, $message, $headers)) {
            echo "ご購入ありがとうございます。確認のメールが送信されました。";
            echo "<a href='toppage.php'>メインメニューへ戻る</a>";
        } else {
            echo "メール送信に失敗しました。";
        }

        // カート削除処理
        $deleteSql = "DELETE FROM cart WHERE user_id = :user_id"; // ユーザーごとのカートを削除
        $deleteStmt = $dbh->prepare($deleteSql);
        $deleteStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        if (!$deleteStmt->execute()) {
            echo "エラー: カートの削除に失敗しました。";
        }

        // cart_detailへの挿入前に、同じ cart_id がすでに存在しないか確認
        foreach ($cartItems as $item) {
            // cart_detailに同じcart_idがすでに存在するかチェック
            $checkSql = "SELECT COUNT(*) FROM `cart_detail` WHERE `cart_id` = :cart_id";
            $checkStmt = $dbh->prepare($checkSql);
            $checkStmt->bindParam(':cart_id', $item['cart_id'], PDO::PARAM_INT);
            $checkStmt->execute();
            $count = $checkStmt->fetchColumn();

            if ($count > 0) {
                // 存在する場合はUPDATEでtrade_situationを2に変更
                $updateSql = "UPDATE `cart_detail`
                              SET `trade_situation` = 2
                              WHERE `cart_id` = :cart_id
                              AND `user_id` = :user_id
                              AND `shop_id` = :shop_id";
                $updateStmt = $dbh->prepare($updateSql);
                $updateStmt->bindParam(':cart_id', $item['cart_id'], PDO::PARAM_INT);
                $updateStmt->bindParam(':user_id', $item['user_id'], PDO::PARAM_INT);
                $updateStmt->bindParam(':shop_id', $item['shop_id'], PDO::PARAM_INT);

                if (!$updateStmt->execute()) {
                    echo "エラー: cart_detailの更新に失敗しました。";
                }
            } else {
                $selectedAddress = $_POST['selected_address'];
                // 存在しない場合は新規にINSERT
                $insertSql = "INSERT INTO `cart_detail` (`cart_id`, `user_id`, `shop_id`, `quantity`, `trade_situation`, `order_date`,`send_address`)
                              VALUES (:cart_id, :user_id, :shop_id, :quantity, 2, CURRENT_TIMESTAMP,:send_address)";
                $insertStmt = $dbh->prepare($insertSql);
                $insertStmt->bindParam(':cart_id', $item['cart_id'], PDO::PARAM_INT);
                $insertStmt->bindParam(':user_id', $item['user_id'], PDO::PARAM_INT);
                $insertStmt->bindParam(':shop_id', $item['shop_id'], PDO::PARAM_INT);
                $insertStmt->bindParam(':quantity', $item['quantity'], PDO::PARAM_INT);
                $insertStmt->bindParam(':send_address',$selectedAddress, PDO::PARAM_STR);

                if (!$insertStmt->execute()) {
                    echo "エラー: cart_detailへの挿入に失敗しました。";
                }
            }
        }
    } else {
        echo "エラー: カートアイテムの取得に失敗しました。";
    }
} else {
    echo "DB接続エラー";
}

// データベース接続を閉じる
$dbh = null;
?>
