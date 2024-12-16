<?php
// データベース接続
include './../../db_open.php';

// update_thumbnail.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shopId = $_POST['shop_id'];
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $thumbnailData = file_get_contents($_FILES['thumbnail']['tmp_name']);

        // サムネイル画像を更新
        $sql = "UPDATE shop SET thumbnail = :thumbnail WHERE shop_id = :shop_id";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([
            ':thumbnail' => $thumbnailData,
            ':shop_id' => $shopId
        ]);

        echo "サムネイルが正常に更新されました。";
    } else {
        echo "サムネイルのアップロードに失敗しました。";
    }
}

?>

