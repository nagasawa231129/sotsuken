<?php
include '../../db_open.php';  // db_open.phpをインクルードして、$connを利用できるようにする

// データベース接続が成功しているか確認（デバッグ用）
if ($conn) {
    // 接続が成功した場合、データベースからshop.idを取得
    $sql = "SELECT shop_id FROM cart WHERE shop_id = 2";
    $result = $conn->query($sql);
    
    // クエリが失敗した場合
    if ($result === false) {
        $errorInfo = $conn->errorInfo();  // PDO::errorInfo()で詳細エラーを取得
        $shopId = 'クエリ失敗: ' . $errorInfo[2];  // エラーメッセージを表示
    } else {
        // クエリが成功した場合、1行目のデータを取得
        $row = $result->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $shopId = $row['shop_id'];  // 取得したshop.idを設定
        } else {
            $shopId = 'データなし';  // 結果が空の場合
        }

        if($shopId != 'データなし'){
            $sqlGoods = "SELECT goods,price FROM shop WHERE shop_id = :shop_id";
            $stmt = $conn->prepare($sqlGoods);
            $stmt->bindParam(':shop_id', $shopId, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $goodsRow = $stmt->fetch(PDO::FETCH_ASSOC);
                $goods = $goodsRow['goods'];
                $price = $goodsRow['price'];
            } else {
                $goods = '該当する商品はありません';
                $goods = '商品情報の取得に失敗しました';
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
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>決済用バーコード生成</title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>
</head>
<body>
    
    <div class="container">
        <h1>決済用バーコード生成</h1>
        <button id="payButton">バーコードを表示</button>
        <p class="barcode-info">
        商品名: <span class="info-text"><?php echo htmlspecialchars($goods, ENT_QUOTES, 'UTF-8'); ?></span><br>
        価格: <span class="info-text"><?php echo htmlspecialchars($price, ENT_QUOTES, 'UTF-8'); ?>円</span>
        </p>

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
