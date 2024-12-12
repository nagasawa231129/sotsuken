<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit(); // 処理を終了
}
include "../../db_open.php";

// 商品IDとユーザーIDがPOSTで送信されているか確認
if (isset($_POST['shop_id']) && isset($_POST['user_id'])) {
    $shopId = $_POST['shop_id'];
    $userId = $_POST['user_id'];
    $quantity = 1; // ここで1個と仮定

    // カートに同じ商品がすでに存在するかチェック
    $sql_check = "SELECT * FROM cart WHERE shop_id = :shop_id AND user_id = :user_id";
    $stmt_check = $dbh->prepare($sql_check);
    $stmt_check->bindParam(':shop_id', $shopId, PDO::PARAM_INT);
    $stmt_check->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt_check->execute();
    
    if ($stmt_check->rowCount() > 0) {
        // 商品がすでにカートにある場合、数量を増加させる
        $sql_update = "UPDATE cart SET quantity = quantity + 1 WHERE shop_id = :shop_id AND user_id = :user_id";
        $stmt_update = $dbh->prepare($sql_update);
        $stmt_update->bindParam(':shop_id', $shopId, PDO::PARAM_INT);
        $stmt_update->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt_update->execute();
    } else {
        // 商品がカートにない場合、新たに追加
        $sql_insert = "INSERT INTO cart (shop_id, user_id, quantity,order_date) VALUES (:shop_id, :user_id, :quantity,CURRENT_TIMESTAMP)";
        $stmt_insert = $dbh->prepare($sql_insert);
        $stmt_insert->bindParam(':shop_id', $shopId, PDO::PARAM_INT);
        $stmt_insert->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt_insert->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt_insert->execute();
    }
    
    // カートに追加後、リダイレクトする
    header('Location: cart.php');
    exit;
}
?>
