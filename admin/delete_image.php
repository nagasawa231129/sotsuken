<?php
// delete_image.php

// セッション開始（必要に応じて）
session_start();

// 必要なファイルのインクルード
include "../../db_open.php"; // データベース接続ファイルをインクルード

// POSTリクエストで image_id が送られてきていることを確認
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['image_id'])) {
    $imageId = intval($_POST['image_id']); // 削除する画像のID

    // データベース接続
    try {
        // 指定された image_id に対して画像を削除するSQL文
        $sql = "DELETE FROM image WHERE image_id = :image_id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':image_id', $imageId, PDO::PARAM_INT);
        $stmt->execute();

        // 画像削除が成功したら、ページリロードのためのJSを出力
        echo "<script>
                alert('画像が正常に削除されました。$imageId ');
                window.location.href = window.location.href; // 現在のページをリロード
              </script>";
    } catch (PDOException $e) {
        echo "データベースエラーが発生しました: " . $e->getMessage();
    }
}
?>
