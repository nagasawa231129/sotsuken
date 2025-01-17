<?php
// pay_comp.php
include "../../db_open.php";
header('Content-Type: application/json; charset=utf-8');

// パラメータを受け取る
if (isset($_GET['cart_group']) && isset($_GET['user_id'])) {
    $cart_group = $_GET['cart_group'];
    $user_id = $_GET['user_id'];

    try {
        // cart_detailテーブルのscannedフィールドを1に更新
        $updateSql = "UPDATE cart_detail SET scanned = 1 WHERE cart_group = :cart_group AND user_id = :user_id";
        $updateStmt = $dbh->prepare($updateSql);
        $updateStmt->bindParam(':cart_group', $cart_group);
        $updateStmt->bindParam(':user_id', $user_id);
        $updateStmt->execute();

        echo json_encode(['status' => 'success', 'message' => 'QRコードがスキャンされ、状態が更新されました']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'データベースエラー: ' . $e->getMessage()]);
    }
}
?>
