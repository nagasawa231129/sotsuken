<?php
// データベース接続
include './../../db_open.php';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $shop_id = $_POST['shop_id'];
        $image_id = $_POST['image_id'];

        // 画像を削除するSQL
        $sql = 'DELETE FROM image WHERE shop_id = :shop_id AND image_id = :image_id';
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
        $stmt->bindParam(':image_id', $image_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo '画像が削除されました。';
        } else {
            echo '画像の削除に失敗しました。';
        }
    }
} catch (PDOException $e) {
    echo 'データベース接続エラー: ' . $e->getMessage();
}
?>