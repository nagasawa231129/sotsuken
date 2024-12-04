<?php
include '../../db_open.php';  // db_open.phpをインクルードして、$dbhを利用できるようにする

session_start();  // セッション開始（セッションを使用してユーザーIDを管理している場合）

if(isset($_SESSION['id'])){
    $user_id = $_SESSION['id'];
}else{
    echo "ログインできていません";
}


// データベース接続が成功しているか確認（デバッグ用）
if ($dbh) {
    // カートのアイテムを取得するSQL
    $selectSql = "SELECT `cart_id`, `user_id`, `shop_id`, `quantity`, `trade_situation`, `order_date` FROM `cart`";
    $stmt = $dbh->prepare($selectSql);

    if ($stmt->execute()) {
        $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC); // fetchAllで配列を取得

        // カートが空でないか確認
        if (empty($cartItems)) {
            echo "カートにアイテムがありません。";
            exit;
        }

        // メール用のメッセージを構築
        $message = "お客様がカートの商品を購入しました。購入詳細は以下の通りです。\n\n";

        // カート内容をメールに追加
        foreach ($cartItems as $item) {
            if (isset($item['shop_id'], $item['quantity'], $item['order_date'])) {
                $message .= "商品ID: " . $item['shop_id'] . "\n";
                $message .= "数量: " . $item['quantity'] . "\n";
                $message .= "注文日: " . $item['order_date'] . "\n\n";
            }
        }

       $mailSql = "SELECT mail FROM user WHERE user_id = {$user_id}";
       $mailStmt = $dbh->prepare($mailSql);
       $mailStmt->execute();
        //メールアドレスを取得
       $mailRow = $mailStmt->fetch(PDO::FETCH_ASSOC);

        if($mailRow && isset($mailRow['mail'])){
            $to = $mailRow['mail'];
        }else{
            echo "メールアドレスのユーザーが見つかりませんでした";
        }
        $subject = "ご購入ありがとうございます";
        $headers = "From: sotsuken@st.yoshida-g.ac.jp\r\n";

        // メール送信
        if (mb_send_mail($to, $subject, $message, $headers)) {
            echo "ご購入ありがとうございます。確認のメールが送信されました。";
            echo"<a href='toppage.php'>メインメニューへ戻る</a>";
        } else {
            echo "メール送信に失敗しました。";
        }

        // カート削除処理
        $deleteSql = "DELETE FROM cart";
        $deleteStmt = $dbh->prepare($deleteSql);

        // まずは cart_detail への挿入前に、同じ cart_id がすでに存在しないか確認
        foreach ($cartItems as $item) {
            // 同じ cart_id がすでに cart_detail に存在するか確認
            $checkSql = "SELECT COUNT(*) FROM `cart_detail` WHERE `cart_id` = :cart_id";
            $checkStmt = $dbh->prepare($checkSql);
            
            // 変数に値を代入
            $cart_id = $item['cart_id'];
            $checkStmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
            
            $checkStmt->execute();
            $count = $checkStmt->fetchColumn();
        
            // 存在しない場合にのみ挿入を行う
            if ($count == 0) {
                $insertSql = "INSERT INTO `cart_detail`(`cart_id`, `user_id`, `shop_id`, `quantity`, `trade_situation`, `order_date`) 
                              VALUES (:cart_id, :user_id, :shop_id, :quantity, :trade_situation, CURRENT_TIMESTAMP)";
                $insertStmt = $dbh->prepare($insertSql);
        
                // 変数に値を代入してからbindParamで渡す
                $user_id = $item['user_id'];
                $shop_id = $item['shop_id'];
                $quantity = $item['quantity'];
                $trade_situation = 1;  // 0 (未決済) 状態で挿入
        
                $insertStmt->bindParam(':cart_id', $cart_id);
                $insertStmt->bindParam(':user_id', $user_id);
                $insertStmt->bindParam(':shop_id', $shop_id);
                $insertStmt->bindParam(':quantity', $quantity);
                $insertStmt->bindParam(':trade_situation', $trade_situation);
        
                if (!$insertStmt->execute()) {
                    throw new Exception("エラー: cart_detail への挿入に失敗しました。");
                }
            } else {
                echo "アイテムがすでに購入履歴に登録されています。";
            }
        }
        

        // カート削除処理
        if ($deleteStmt->execute()) {
            // echo "カートが削除されました。";
        } else {
            echo "エラー: カートの削除に失敗しました。";
        }
    } else {
        echo "エラー: カートアイテムの取得に失敗しました。";
    }
} else {
    echo "db_error";    // DB接続エラー
}

// データベース接続を閉じる
$dbh = null;
?>