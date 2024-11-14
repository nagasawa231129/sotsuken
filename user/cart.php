<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>決済用バーコード生成</title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>
</head>
<body>
    <h1>決済する</h1>
    <button id="payButton">決済する</button>
    <!-- <svg>要素を用意しておきます -->
    <svg id="barcodeContainer" style="margin-top: 20px;"></svg>

    <script>
        document.getElementById('payButton').addEventListener('click', function() {
            const paymentData = 'PAY1234567890'; // バーコードにするデータ
            const barcodeContainer = document.getElementById('barcodeContainer');
            barcodeContainer.innerHTML = ''; // 前のバーコードを消去

            // JsBarcodeを使用してバーコードを生成
            JsBarcode(barcodeContainer, paymentData, {
                format: 'CODE128',
                displayValue: true,
                width: 2,
                height: 60,
                margin: 10
            });
        });
    </script>
</body>
</html>
