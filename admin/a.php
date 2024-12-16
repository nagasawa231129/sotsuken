<?php
include "../../db_open.php"; // データベース接続ファイルをインクルード

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // アップロードされたファイルが存在し、エラーがないか確認
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['image']['tmp_name'];
        $imageName = $_FILES['image']['name'];
        $imageType = $_FILES['image']['type'];

        // 許可する画像タイプを指定（JPEG, PNG, GIF）
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (in_array($imageType, $allowedTypes)) {
            // 画像ファイルのバイナリデータを読み込む
            $imageData = file_get_contents($imageTmpPath);
            
            // 商品IDをPOSTで受け取る
            $shopId = $_POST['shop_id'];

            // データベースを更新する
            $sql = "UPDATE shop SET thumbnail = :thumbnail WHERE shop_id = :shop_id";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':thumbnail', $imageData, PDO::PARAM_LOB); // 画像データをBLOB型として保存
            $stmt->bindParam(':shop_id', $shopId); // 更新する商品ID
            
            if ($stmt->execute()) {
                // 画像が更新された後、base64エンコードされた画像を返す
                echo "success"; // 成功メッセージ、これをJavaScript側で受け取って処理
            } else {
                echo "fail"; // 更新に失敗した場合
            }
        } else {
            echo "許可されていないファイルタイプです。";
        }
    } else {
        echo "画像が選択されていません。";
    }
}
?>
