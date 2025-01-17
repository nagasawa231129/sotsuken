<?php
// result.php
require_once "../../phpqrcode/qrlib.php"; // QRライブラリ

// URLパラメータを受け取る
$cartGroup = isset($_GET['cart_group']) ? $_GET['cart_group'] : null;
$userId = isset($_GET['user_id']) ? $_GET['user_id'] : null;

if ($cartGroup && $userId) {
    // QRコードのデータを作成
    $qrData = "cart_group={$cartGroup}&user_id={$userId}";
    $url = 'create_qr.php?data=' . urlencode('https://y231129.daa.jp/sotsuken/sotsuken/user/pay_comp.php?' . $qrData);
} else {
    echo "必要なパラメータが不足しています。";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QRコード</title>
</head>
<body>
    <h2>支払い読み取りQRコード</h2>
    <img src="<?php echo $url ?>" />
    <br>
    <a href="order.php#unpaid">戻る</a>

    <script>
        // 定期的にQRコードの読み取りを確認
        setInterval(function() {
            // Ajaxリクエストで読み取り状態を確認
            fetch("check_qr_scanned.php?cart_group=<?php echo $cartGroup; ?>&user_id=<?php echo $userId; ?>")
                .then(response => response.json())
                .then(data => {
                    if (data.scanned) {
                        // QRコードがスマホで読み取られた場合、order.phpに遷移
                        window.location.href = "order.php#unpaid";
                    }
                })
                .catch(error => {
                    console.error("エラーが発生しました:", error);
                });
        }, 1000); // 1秒ごとに確認
    </script>
</body>
</html>
