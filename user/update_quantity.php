<?php
// データベース接続設定
include '../../db_open.php';

// POSTデータを受け取る
$shopId = isset($_POST['shop_id']) ? (int)$_POST['shop_id'] : 0;
$newQuantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

// 変数が正しいかチェック
if ($shopId > 0 && $newQuantity > 0) {
    // 個数を更新するSQL
    try {
        $sql = "UPDATE cart SET quantity = :quantity WHERE shop_id = :shop_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':quantity', $newQuantity, PDO::PARAM_INT);
        $stmt->bindParam(':shop_id', $shopId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // 更新成功
            echo 'success';
        } else {
            // 更新失敗
            echo 'failure';
        }
    } catch (PDOException $e) {
        // エラーハンドリング
        echo 'error: ' . $e->getMessage();
    }
} else {
    // 不正なデータの場合
    echo 'invalid_input';
}

// データベース接続を閉じる
$conn = null;
?>
