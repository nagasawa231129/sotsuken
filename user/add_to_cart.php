<?php
session_start();
include "../../db_open.php"; // PDO接続のファイルをインクルード

// ログインしていないユーザーはカートに追加できない
if (!isset($_SESSION['id'])) {
    echo "ログインしてください。";
    exit;
}

// フォームから送信されたデータを受け取る
$userId = $_POST['user_id'];
$shopId = $_POST['shop_id'];
$quantity = $_POST['quantity'];

// カートに既にその商品が存在するか確認
$sql_check = "SELECT * FROM cart WHERE user_id = :user_id AND shop_id = :shop_id AND trade_situation = 0"; // trade_situation = 0 は「カートに入っている」状態
$stmt_check = $dbh->prepare($sql_check);
$stmt_check->bindParam(':user_id', $userId);
$stmt_check->bindParam(':shop_id', $shopId);
$stmt_check->execute();

// 商品がカートにすでに入っている場合は数量を更新
if ($stmt_check->rowCount() > 0) {
    $cart = $stmt_check->fetch(PDO::FETCH_ASSOC);
    $newQuantity = $cart['quantity'] + $quantity;  // 既存の数量に新しい数量を加算

    // カートに追加された商品が「カートに入っている」場合は数量だけ更新
    $sql_update = "UPDATE cart SET quantity = :quantity WHERE cart_id = :cart_id";
    $stmt_update = $dbh->prepare($sql_update);
    $stmt_update->bindParam(':quantity', $newQuantity);
    $stmt_update->bindParam(':cart_id', $cart['cart_id']);
    $stmt_update->execute();
    echo "カートの商品数量を更新しました。";
} else {
    // 商品がカートにない場合、新規に追加
    $sql_insert = "INSERT INTO cart (user_id, shop_id, quantity, trade_situation, order_date) 
                   VALUES (:user_id, :shop_id, :quantity, 0, NOW())"; // trade_situation = 0 は「カートに入っている」状態
    $stmt_insert = $dbh->prepare($sql_insert);
    $stmt_insert->bindParam(':user_id', $userId);
    $stmt_insert->bindParam(':shop_id', $shopId);
    $stmt_insert->bindParam(':quantity', $quantity);
    $stmt_insert->execute();
    echo "商品をカートに追加しました。";
}

// カートページにリダイレクト
header("Location: cart.php");
exit;
?>
