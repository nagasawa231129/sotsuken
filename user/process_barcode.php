<?php
include "../../db_open.php";
header('Content-Type: application/json; charset=utf-8');

if (isset($_GET['barcode'])) {
    $barcodeData = $_GET['barcode'];

    // URLデコードしてから処理を行う
    $decodedBarcodeData = urldecode($barcodeData); // URLデコード
    error_log("Decoded barcode data: " . $decodedBarcodeData);  // デコードされたデータをログに出力
    var_dump($decodedBarcodeData); // データ確認用

    // バーコードデータがアンダースコア（_）で区切られているか確認
    if (strpos($decodedBarcodeData, '_') !== false) {
        list($order_date, $user_id) = explode('_', $decodedBarcodeData);
        error_log("Order Date: " . $order_date . " | User ID: " . $user_id);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'バーコード形式が不正です']);
        exit;
    }

    try {
        // SQLでデータベースを更新
        $sql = "UPDATE cart_detail 
                SET trade_situation = 2 
                WHERE order_date = :order_date AND user_id = :user_id AND trade_situation = 1";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':order_date', $order_date);
        $stmt->bindParam(':user_id', $user_id);

        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                // 購入が完了した場合
                echo json_encode(['status' => 'success', 'message' => '購入が完了しました']);
            } else {
                // 更新対象が見つからない場合
                echo json_encode(['status' => 'error', 'message' => '更新対象が見つかりません']);
            }
        } else {
            $errorInfo = $stmt->errorInfo();
            echo json_encode(['status' => 'error', 'message' => 'SQLエラー: ' . $errorInfo[2]]);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'データベースエラー: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'バーコードデータが送信されていません']);
}
?>
