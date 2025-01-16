<?php
// データベース接続設定
include '../../db_open.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 'shopId' が存在する場合のみ処理を実行
    if (isset($_POST['shop_id'])) {
        $shopId = $_POST['shop_id'];
    } else {
        // 'shopId' が存在しない場合、エラーメッセージを表示
        die("エラー: 商品IDが指定されていません。");
    }
}
$currentQuantity = intval($_POST['current_quantity']);
$action = $_POST['action'];
$newQuantity = $currentQuantity;
if($action == 'increase'){
    $newQuantity = $currentQuantity + 1;
}else if($action == 'decrease'){
    if($currentQuantity == 1){
        $deleteSql = "DELETE FROM cart WHERE user_id = :user_id AND shop_id = :shop_id";
        $stmt = $dbh->prepare($deleteSql);
        $stmt->bindParam(':user_id', $_SESSION['id'], PDO::PARAM_INT);
        $stmt->bindParam(':shop_id', $shopId, PDO::PARAM_INT);
        if ($stmt->execute()) {
            header("Location: cart.php"); // カートページにリダイレクト
            exit();
        } else {
            echo "削除に失敗しました。";
        }
        }
        $newQuantity = $currentQuantity - 1;
}
if($dbh){
    $sql = "UPDATE cart SET quantity = :quantity WHERE user_id = :user_id AND shop_id = :shop_id";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':quantity', $newQuantity, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $_SESSION['id'], PDO::PARAM_INT);
    $stmt->bindParam(':shop_id', $shopId, PDO::PARAM_INT);
    if ($stmt->execute()) {
        header("Location: cart.php"); // カートページにリダイレクト
        exit();
    } else {
        echo "更新に失敗しました。";
    }
} else {
    echo "データベース接続に失敗しました。";
}
?>