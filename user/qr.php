<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QRコード / バーコード スキャン</title>
    <script src="https://cdn.jsdelivr.net/npm/html5-qrcode/minified/html5-qrcode.min.js"></script>
</head>
<body>
    <h1>QRコード / バーコードをスキャンしてください</h1>

    <!-- スキャン領域 -->
    <div id="reader" style="width: 100%; height: 300px;"></div> 

    <script>
        // HTML5Qrcodeインスタンス作成
        const html5QrCode = new Html5Qrcode("reader");

        // バーコード/QRコードスキャン開始
        html5QrCode.start(
            { facingMode: "environment" },  // 背面カメラを使用
            {
                fps: 10,  // スキャン速度
                qrbox: 250  // スキャン範囲
            },
            (decodedText, decodedResult) => {
                // スキャン成功時に呼ばれるコールバック
                console.log("読み取られたデータ: " + decodedText);

                // 画面遷移はここで制御する
                // alert("読み取られたデータ: " + decodedText);
                
                // 必要ならばサーバーに通知
                fetch("notify_scanned.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ scanned: true, data: decodedText })
                }).then(response => {
                    console.log("QRコード/バーコードが読み取られました");
                });

                // スキャン後にカメラを停止
                html5QrCode.stop();

                // 手動で画面遷移などしたい場合、ここで遷移を実行
                // 例: window.location.href = "order.php#unpaid"; (読み取った後に遷移)
            },
            (errorMessage) => {
                // エラー発生時のコールバック
                console.log("エラー発生: ", errorMessage);
            }
        ).catch(err => {
            console.log("カメラ起動に失敗: ", err);
        });
    </script>

</body>
</html>
