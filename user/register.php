<?php
include '../../db_open.php';  // db_open.phpをインクルードして、$dbhを利用できるようにする

$sumPrice = 0;

// データベース接続が成功しているか確認（デバッグ用）
if ($dbh) {
    // カートからデータを取得
    $sql = "SELECT * FROM cart";
    $result = $dbh->query($sql);

    // クエリが失敗した場合
    if ($result === false) {
        $errorInfo = $dbh->errorInfo();  // PDO::errorInfo()で詳細エラーを取得
        echo 'クエリ失敗: ' . $errorInfo[2];  // エラーメッセージを表示
    } else {
        echo "<h1>内容をお確かめください</h1>";

        // カートのデータを処理
        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            $cart_id = $row['cart_id'];
            $user_id = $row['user_id'];
            $shop_id = $row['shop_id'];
            $quantity = $row['quantity'];
            $trade_situation = $row['trade_situation'];
            $order_date = $row['order_date'];

            // shopテーブルから商品情報を取得
            $sqlGoods = "SELECT goods, price FROM shop WHERE shop_id = :shop_id";
            $stmt = $dbh->prepare($sqlGoods);
            $stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
            $stmt->execute();

            // 商品情報が正しく取得できたか確認
            $goodsRow = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($goodsRow) {
                // 商品情報が取得できた場合
                $goods = $goodsRow['goods'];
                $price = $goodsRow['price'];

                // 合計金額の計算
                $sumPrice += ($price * $quantity);

                // 商品情報を表示
                echo "<div class='cart-item'>";
                echo "<p>商品名: <span class='info-text'>" . htmlspecialchars($goods, ENT_QUOTES, 'UTF-8') . "</span><br>";
                echo "価格: <span class='info-text'>" . htmlspecialchars($price, ENT_QUOTES, 'UTF-8') . "円</span><br>";
                echo "数量: <span id='quantity_$shop_id'>" . $quantity . "</span> 個<br>";
                echo "合計: <span id='totalAmount_$shop_id'>" . ($price * $quantity) . "円</span><br>";
                echo "</div>";
            } else {
                echo "<p>shop_id: $shop_id に該当する商品はありません</p>";
            }
        }
    }
} else {
    echo 'データベース接続に失敗しました。';  // 接続失敗時のエラーメッセージ
}

// カート削除関数
function deleteCart($dbh) {
    $sql = "DELETE FROM cart";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
}

// カート詳細追加関数
function detailCart($dbh, $goods, $user_id, $shop_id, $quantity, $trade_situation, $order_date) {
    $sql = "INSERT INTO `cart_detail`(`cart_id`, `user_id`, `shop_id`, `quantity`, `trade_situation`, `order_date`) 
            VALUES (:goods, :user_id, :shop_id, :quantity, :trade_situation, :order_date)";

    $stmt = $dbh->prepare($sql);

    // パラメータをバインド
    $stmt->bindParam(':goods', $goods, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
    $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
    $stmt->bindParam(':trade_situation', $trade_situation, PDO::PARAM_STR);
    $stmt->bindParam(':order_date', $order_date, PDO::PARAM_STR);

    // 実行
    $stmt->execute();
}

// データベース接続を閉じる
$dbh = null;
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="register_style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>決済完了</title>
</head>
<body>
    <p>合計金額: <span id="totalSum" class="info-text"><?php echo htmlspecialchars($sumPrice, ENT_QUOTES, 'UTF-8'); ?>円</span></p>
    
    <div class="container">
        <h1>決済完了</h1>

        <!-- 決済完了ボタン（初期状態で表示） -->
        <form action="payment_complete.php" method="post"  id="paymentForm">
            <button id="paymentCompleteButton">決済完了</button>
        </form>
    </div>
</body>
</html>
