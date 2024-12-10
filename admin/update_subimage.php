<?php
// update_subimage.php

// セッション開始（必要に応じて）
session_start();

// 必要なファイルのインクルード
include "../../db_open.php"; // データベース接続ファイルをインクルード

// POSTリクエストかつ必要なパラメータがセットされているかを確認
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['subimage']) && isset($_POST['shop_id']) && isset($_POST['image_id'])) {
    // 投稿された画像とshop_id, image_idを取得
    $shopId = intval($_POST['shop_id']);
    $imageId = intval($_POST['image_id']); // 更新する画像のID
    $subimage = $_FILES['subimage'];

    // 画像が正常にアップロードされたかを確認
    if ($subimage['error'] === UPLOAD_ERR_OK) {
        // 画像をBLOBとしてデータベースに保存
        $imgData = file_get_contents($subimage['tmp_name']);
        
        // 画像ファイルが正常か、適切なフォーマットかを確認（オプション）
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $fileMimeType = mime_content_type($subimage['tmp_name']);
        if (!in_array($fileMimeType, $allowedMimeTypes)) {
            echo "対応していない画像形式です。JPEGまたはPNG形式の画像をアップロードしてください。";
            exit;
        }

        // データベース接続
        try {
            // 指定されたimage_idに対してサブサムネイル画像を更新するSQL文
            $sql = "UPDATE image SET img = :img WHERE shop_id = :shop_id AND image_id = :image_id";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':shop_id', $shopId, PDO::PARAM_INT);
            $stmt->bindParam(':image_id', $imageId, PDO::PARAM_INT);
            $stmt->bindParam(':img', $imgData, PDO::PARAM_LOB);
            $stmt->execute();

            // 更新成功のメッセージ
            echo "サブサムネイルが正常に更新されました。";
        } catch (PDOException $e) {
            // エラー発生時に詳細なエラーメッセージを表示
            echo "データベースエラーが発生しました: " . $e->getMessage();
        }
    } else {
        // アップロードエラーが発生した場合のエラーメッセージ
        echo "画像のアップロードに失敗しました。エラーコード: " . $subimage['error'];
    }
} else {
    // 必要なパラメータが足りない場合
    echo "画像、shop_id、またはimage_idが送信されていません。";
}
?>
