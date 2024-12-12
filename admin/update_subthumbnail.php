<?php
include "../../db_open.php"; // データベース接続ファイルをインクルード

// 送信されたデータを取得
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imageId = $_POST['image_id']; // 画像ID
    $imageData = file_get_contents($_FILES['image']['tmp_name']); // アップロードされた画像データ

    // 画像データが存在する場合
    if ($imageData && $imageId) {
        // 画像をBase64エンコードして保存する
        $encodedImage = base64_encode($imageData);

        // 画像IDに対応するレコードを更新
        $sql = "UPDATE image SET img = :img WHERE image_id = :image_id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':img', $encodedImage, PDO::PARAM_STR);
        $stmt->bindParam(':image_id', $imageId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // 成功した場合、更新された画像のBase64エンコードデータを返す
            echo "data:image/jpeg;base64," . $encodedImage; // 画像データをBase64エンコードして返す
        } else {
            echo "画像の更新に失敗しました。";
        }
    } else {
        echo "画像のアップロードに失敗しました。";
    }
}
?>