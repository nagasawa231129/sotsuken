<?php
include '../../db_open.php';  // db_open.phpをインクルードして、$connを利用できるようにする

$sumPrice = 0;

// データベース接続が成功しているか確認（デバッグ用）
if ($conn) {
    // 接続が成功した場合、データベースからshop.idを取得
    $sql = "SELECT * FROM cart ";
    $result = $conn->query($sql);
    
    // クエリが失敗した場合
    if ($result === false) {
        $errorInfo = $conn->errorInfo();  // PDO::errorInfo()で詳細エラーを取得
        $shopId = 'クエリ失敗: ' . $errorInfo[2];  // エラーメッセージを表示
    } else {
        echo "<h1>内容をお確かめください</h1>";
        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            $shopId = $row['shop_id'];
            $quantity = $row['quantity'];
            // shopテーブルから商品情報を取得
            $sqlGoods = "SELECT goods, price FROM shop WHERE shop_id = :shop_id";
            $stmt = $conn->prepare($sqlGoods);
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

// データベース接続を閉じる
$conn = null;
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
        <!-- <p class="barcode-info">
        商品名: <span class="info-text"><?php echo htmlspecialchars($goods, ENT_QUOTES, 'UTF-8'); ?></span><br>
        価格: <span class="info-text"><?php echo htmlspecialchars($price, ENT_QUOTES, 'UTF-8'); ?>円</span>
        </p> -->

        <!-- shop.idを表示するためのdiv -->
        <!-- <p class="barcode-info">取得したshop.id: <span class="info-text"><?php echo htmlspecialchars($shopId, ENT_QUOTES, 'UTF-8'); ?></span></p> -->

        <!-- バーコードを表示するためのSVG要素 -->
        <svg id="barcodeContainer"></svg>
        
        <button id="paymentCompleteButton">決済完了</button>
        
    </div>

    <script>
        document.getElementById('payButton').addEventListener('click', function() {
            // PHPから取得したshop.idの値をJavaScriptに渡す
            const paymentData = '123456789-' + '<?php echo $shopId; ?>'; // バーコードにするデータ
            const barcodeContainer = document.getElementById('barcodeContainer');
            barcodeContainer.innerHTML = ''; // 前のバーコードを消去

            // JsBarcodeを使用してバーコードを生成
            JsBarcode(barcodeContainer, paymentData, {
                format: 'CODE128',
                displayValue: true,  // バーコードの値も表示
                width: 2,           // バーコードの線の幅
                height: 60,         // バーコードの高さ
                margin: 10          // バーコードの周りの余白
            });

            document.getElementById('paymentCompleteButton').style.display = 'block';
        });

            document.getElementById('paymentCompleteButton').addEventListener('click',function(){
                window.location.href='payment_complete.php'
            });
        
    </script>
    
</body>
</html>
