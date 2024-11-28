<?php
include '../../db_open.php';  // db_open.phpをインクルードして、$dbhを利用できるようにする

// データベース接続が成功しているか確認（デバッグ用）
if ($dbh) {
    $selectSql = "SELECT `cart_id`, `user_id`, `shop_id`, `quantity`, `trade_situation`, `order_date` FROM `cart`";
    $stmt = $dbh->prepare($selectSql);

    if($stmt->execute()){
       $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

       //カートの削除処理
       $deleteSql = "DELETE FROM cart";
       $stmt = $dbh->prepare($deleteSql);

       if($stmt->execute()){
        echo "ご購入ありがとうございます";

        foreach ($cartItems as $row) {
            $cartId = $row['cart_id'];
            $shopId = $row['shop_id'];
            $userId = $row['user_id'];
            $quantity = $row['quantity'];
            $tradeSituation = $row['trade_situation'];
            $orderDate = $row['order_date'];

            // INSERT文の作成
            $insertSql = "INSERT INTO `cart_detail`(`cart_id`, `user_id`, `shop_id`, `quantity`, `trade_situation`, `order_date`) 
                        VALUES (:cart_id, :user_id, :shop_id, :quantity, :trade_situation, :order_date)";
            $insertStmt = $dbh->prepare($insertSql);

            // パラメータのバインド
            $insertStmt->bindParam(':cart_id', $cartId);
            $insertStmt->bindParam(':user_id', $userId);
            $insertStmt->bindParam(':shop_id', $shopId);
            $insertStmt->bindParam(':quantity', $quantity);
            $insertStmt->bindParam(':trade_situation', $tradeSituation);
            $insertStmt->bindParam(':order_date', $orderDate);

            // データを挿入
            $insertStmt->execute();
        }
       }
    }
} else {
    echo "db_error";    // DB接続エラー
}

// データベース接続を閉じる
$dbh = null;
?>
