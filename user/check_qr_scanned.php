<?php
// check_qr_scanned.php
include "../../db_open.php";
header('Content-Type: application/json; charset=utf-8');

// QRコードがスキャンされた状態を確認
if (isset($_GET['cart_group']) && isset($_GET['user_id'])) {
    $cart_group = $_GET['cart_group'];
    $user_id = $_GET['user_id'];

    try {
        // cart_detailテーブルからscannedフィールドを取得
        $sql = "SELECT scanned FROM cart_detail WHERE cart_group = :cart_group AND user_id = :user_id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':cart_group', $cart_group);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $scanned = $row['scanned'];

            // QRコードがスキャンされた時点で、scannedを1に更新し、trade_situationを2に更新
            if ($scanned == 1) {
                // trade_situation を 2 に更新（未決済 -> 完了）
                $updateSql = "UPDATE cart_detail SET trade_situation = 2 WHERE cart_group = :cart_group AND user_id = :user_id AND trade_situation = 1";
                $updateStmt = $dbh->prepare($updateSql);
                $updateStmt->bindParam(':cart_group', $cart_group);
                $updateStmt->bindParam(':user_id', $user_id);
                $updateStmt->execute();
                
                echo json_encode(['scanned' => true]);
            } else {
                echo json_encode(['scanned' => false]);
            }
        } else {
            echo json_encode(['scanned' => false]);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'データベースエラー: ' . $e->getMessage()]);
    }
}
?>
