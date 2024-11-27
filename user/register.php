<?php
include '../../db_open.php';  // db_open.phpをインクルードして、$dbhを利用できるようにする

$sumPrice = 0;

// データベース接続が成功しているか確認（デバッグ用）
if ($dbh) {
    $sql = "SELECT * FROM cart ";
    $result = $dbh->query($sql);
    
    // クエリが失敗した場合
    if ($result === false) {
        $errorInfo = $dbh->errorInfo();  // PDO::errorInfo()で詳細エラーを取得
        $shopId = 'クエリ失敗: ' . $errorInfo[2];  // エラーメッセージを表示
    } else {
        echo "<h1>内容をお確かめください</h1>";
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
            $stmt->bindParam(':shop_id', $shopId, PDO::PARAM_INT);
            $stmt->execute();
            if($stmt->rowCount() > 0){
                $goodsRow = $stmt->fetch(PDO::FETCH_ASSOC);
                $goods = $goodsRow['goods'];
                $price = $goodsRow['price'];
                
                $sumPrice = $sumPrice + ($price * $quantity);

                // 商品情報を表示
                echo "<div class='cart-item'>";
                echo "<p>商品名: <span class='info-text'>" . htmlspecialchars($goods, ENT_QUOTES, 'UTF-8') . "</span><br>";
                echo "価格: <span class='info-text'>" . htmlspecialchars($price, ENT_QUOTES, 'UTF-8') . "円</span><br>";
                echo "数量: <span id='quantity_$shopId'>" . $quantity . "</span> 個<br>";
                 echo "合計: <span id='totalAmount_$shopId'>" . ($price * $quantity) . "円</span><br>";
                echo "</div>";
            } else {
                echo "<p>shop_id: $shopId に該当する商品はありません</p>";
            }
        }
    }
} else {
    $shopId = '接続失敗';  // 接続が失敗した場合
}

    function deleteCart($dbh){
        $sql = "DELETE FROM cart";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(PDO::PARAM_INT);
        $stmt->execute();
    }

    function detailCart($dbh, $goods, $user_id, $shop_id, $quantity, $trade_situation, $order_date){
        $sql = "INSERT INTO `cart_detail`(`cart_id`, `user_id`, `shop_id`, `quantity`, `trade_situation`, `order_date`) 
            VALUES (:goods, :user_id, :shop_id, :quantity, :trade_situation, :order_date)";

        $stmt = $dbh->prepare($sql);
            
        // パラメータをバインド
        $stmt->bindParam(':goods', $goods, PDO::PARAM_INT);
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
    <title>決済用バーコード生成</title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>
</head>
<p>合計金額: <span id="totalSum" class="info-text"><?php echo htmlspecialchars($sumPrice, ENT_QUOTES, 'UTF-8'); ?>円</span></p>
<body>
    
    <div class="container">
        <h1>決済用バーコード生成</h1>
        <button id="payButton">バーコードを表示</button>

        <svg id="barcodeContainer"></svg>
        
        <button onclick="deleteCart()" style="display:none;" >決済完了</button>
        
    </div>

    <script>
       document.getElementById('payButton').addEventListener('click', function() {
    const paymentData = '<?php echo $shopId; ?>-123456789'; // バーコードにするデータ
    const barcodeContainer = document.getElementById('barcodeContainer');
    barcodeContainer.innerHTML = ''; // 前のバーコードを消去

    // JsBarcodeを使用してバーコードを生成
    JsBarcode(barcodeContainer, paymentData, {
        format: 'CODE128',
        displayValue: true,  // バーコードの値も表示
        width: 3,           // バーコードの線の幅
        height: 100,         // バーコードの高さ
        margin: 20          // バーコードの周りの余白
    });

    // バーコード表示後に決済完了ボタンを表示
    document.getElementById('paymentCompleteButton').style.display = 'block';
});

// document.getElementById('paymentCompleteButton').addEventListener('click', function() {
//     const xhr = new XMLHttpRequest();
//     xhr.open('POST', 'delete_cart.php', true);
//     xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

//     // リクエストが完了したときの処理
//     xhr.onload = function() {
//         if (xhr.status === 200 && xhr.responseText === 'success') {
//             // 削除が成功した場合、決済完了ページへ遷移
//             window.location.href = 'payment_complete.php';
//         } else {
//             alert('カートの削除に失敗しました。');
//         }
//     };

//     // エラー処理
//     xhr.onerror = function() {
//         alert('削除リクエストの送信に失敗しました。');
//     };

//     xhr.send();  // リクエストを送信
// });

    </script>
</body>
</html>
