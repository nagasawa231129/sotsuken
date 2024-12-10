<?php
include "../../db_open.php"; // データベース接続ファイルをインクルード
// update_thumbnail.php

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image']) && isset($_POST['shop_id'])) {
    // 画像ファイルを取得
    $image = $_FILES['image']['tmp_name'];
    $shopId = $_POST['shop_id'];

    // 画像がアップロードされているか確認
    if (is_uploaded_file($image)) {
        // 画像を読み込み、Base64エンコード
        $imageData = file_get_contents($image);
        $encodedImage = base64_encode($imageData);

        // 成功した場合、Base64エンコードされた画像データを返す
        echo "data:image/jpeg;base64," . $encodedImage;
    } else {
        echo "error"; // 画像の読み込みに失敗した場合
    }
}
