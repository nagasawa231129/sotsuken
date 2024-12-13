<?php
include "../../db_open.php"; // データベース接続ファイル

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['thumbnail']) && isset($_POST['shop_id'])) {
    $shopId = $_POST['shop_id'];
    $image = $_FILES['thumbnail']['tmp_name'];

    if (is_uploaded_file($image)) {
        // 画像データを取得
        $imageData = file_get_contents($image);

        // データベースに保存
        $stmt = $pdo->prepare("UPDATE shop SET thumbnail = ? WHERE shop_id = ?");
        $stmt->execute([$imageData, $shopId]);

        // 成功した場合、Base64エンコードされた画像データを返す
        echo "data:image/jpeg;base64," . base64_encode($imageData);
    } else {
        http_response_code(400); // エラー応答
        echo "error";
    }
}
?>
