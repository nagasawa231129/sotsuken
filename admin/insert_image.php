<?php
// データベース接続設定
include './../../db_open.php';

// 画像アップロード処理
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['subthumbnail'])) {
    $shop_id = $_POST['shop_id'];
    $files = $_FILES['subthumbnail']; // 画像ファイルの情報

    $uploadSuccess = true; // アップロード成功かどうかのフラグ

    // ファイルごとにアップロード処理
    foreach ($files['tmp_name'] as $key => $tmpName) {
        if (is_uploaded_file($tmpName)) {
            // 画像データを取得
            $imgData = file_get_contents($tmpName);

            // データベースに保存
            $stmt = $dbh->prepare("INSERT INTO image (shop_id, img) VALUES (:shop_id, :img)");
            $stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
            $stmt->bindParam(':img', $imgData, PDO::PARAM_LOB);

            if (!$stmt->execute()) {
                $uploadSuccess = false; // エラーがあった場合フラグをfalseに
                break;
            }
        } else {
            $uploadSuccess = false; // アップロード失敗
            break;
        }
    }

    // JSONでレスポンスを返す
    if ($uploadSuccess) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}
?>
