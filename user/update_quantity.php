<?php
// データベース接続設定
include '../../db_open.php';
session_start();

// POSTデータを受け取る
$shopId = isset($_POST['shop_id']) ? (int)$_POST['shop_id'] : 0;
$newQuantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
$userId = $_SESSION['id'];  // セッションからユーザーIDを取得

// 変数が正しいかチェック
if ($shopId > 0 && $newQuantity > 0) {
    try {
        // カート内の商品数量を更新するSQL文
        $sql = "UPDATE cart SET quantity = :quantity WHERE shop_id = :shop_id AND user_id = :user_id";
        $stmt = $dbh->prepare($sql);
        
        // プレースホルダに値をバインド
        $stmt->bindParam(':quantity', $newQuantity, PDO::PARAM_INT);
        $stmt->bindParam(':shop_id', $shopId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

        // SQLを実行
        if ($stmt->execute()) {
            // 更新成功
            echo 'success';
        } else {
            // 更新失敗
            echo 'failure: 更新に失敗しました。';
        }
    } catch (PDOException $e) {
        // エラーハンドリング
        echo 'error: ' . $e->getMessage();
    }
} else {
    // 不正なデータの場合
    echo 'invalid_input: shop_id または quantity が不正です';
}

// データベース接続を閉じる
$dbh = null;
?>
