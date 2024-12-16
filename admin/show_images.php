<?php
include "./../../db_open.php"; // DB接続

// shop_idを取得
if (isset($_GET['shop_id']) && is_numeric($_GET['shop_id'])) {
    $shop_id = $_GET['shop_id'];

    // imageテーブルから指定されたshop_idに関連するすべての画像を取得
    $stmt = $dbh->prepare("SELECT img FROM image WHERE shop_id = :shop_id");
    $stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
    $stmt->execute();

    // 画像が複数枚ある場合、すべての画像をBase64エンコードして返す
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($images) {
        $encodedImages = [];
        foreach ($images as $image) {
            // 画像をBase64エンコード
            $encodedImg = base64_encode($image['img']);
            $encodedImages[] = "data:image/jpeg;base64,$encodedImg";
        }
        // JSON形式で返す
        echo json_encode($encodedImages);
    } else {
        echo json_encode([]);  // 画像がない場合は空の配列を返す
    }
} else {
    echo json_encode([]);  // 無効なshop_idの場合
}
?>
