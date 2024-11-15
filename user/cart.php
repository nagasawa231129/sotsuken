<?php
include '../../db_open.php';  // db_open.phpをインクルードして、$connを利用できるようにする

// データベース接続が成功しているか確認（デバッグ用）
if ($conn) {
    // 接続が成功した場合、データベースからshop.idを取得
    $sql = "SELECT shop_id FROM cart LIMIT 1";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>決済用バーコード生成</title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 90%;
            max-width: 400px;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        #payButton {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            margin-bottom: 20px;
            transition: background-color 0.3s ease;
        }

        #payButton:hover {
            background-color: #218838;
        }

        #barcodeContainer {
            width: 100%;
            height: 100px;
            margin-top: 20px;
        }

        .barcode-info {
            font-size: 16px;
            color: #555;
            margin-top: 10px;
        }

        .info-text {
            color: #007bff;
            font-weight: bold;
        }

        #paymentCompleteButton{
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 20px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>決済用バーコード生成</h1>
        <button id="payButton">バーコードを表示</button>
        
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
        
    </script>
    
</body>
</html>
