<?php
include "../../db_open.php"; // PDO接続のファイルをインクルード

// レスポンスを初期化
$response = ['success' => false];

// ログインしているか確認
if (isset($_POST['user_id']) && (isset($_POST['shop_id']) || isset($_POST['brand_id']))) {
    $userId = $_POST['user_id'];

    // shop_idがPOSTされていた場合
    if (isset($_POST['shop_id'])) {
        $shopId = $_POST['shop_id'];

        // すでにお気に入りに登録されているか確認
        $sql_check = "SELECT * FROM favorite WHERE user_id = :user_id AND shop_id = :shop_id";
        $stmt_check = $dbh->prepare($sql_check);
        $stmt_check->bindParam(':user_id', $userId);
        $stmt_check->bindParam(':shop_id', $shopId);
        $stmt_check->execute();

        if ($stmt_check->rowCount() > 0) {
            // お気に入りから削除
            $sql_delete = "DELETE FROM favorite WHERE user_id = :user_id AND shop_id = :shop_id";
            $stmt_delete = $dbh->prepare($sql_delete);
            $stmt_delete->bindParam(':user_id', $userId);
            $stmt_delete->bindParam(':shop_id', $shopId);
            $stmt_delete->execute();

            $response['success'] = true;
            $response['action'] = 'removed'; // 削除された
        } else {
            // お気に入りに追加
            $sql_insert = "INSERT INTO favorite (user_id, shop_id) VALUES (:user_id, :shop_id)";
            $stmt_insert = $dbh->prepare($sql_insert);
            $stmt_insert->bindParam(':user_id', $userId);
            $stmt_insert->bindParam(':shop_id', $shopId);
            $stmt_insert->execute();

            $response['success'] = true;
            $response['action'] = 'added'; // 追加された
        }
    }

    // brand_idがPOSTされていた場合
    if (isset($_POST['brand_id'])) {
        $brandId = $_POST['brand_id'];

        // すでにお気に入りに登録されているか確認
        $sql_check = "SELECT * FROM favorite WHERE user_id = :user_id AND brand_id = :brand_id";
        $stmt_check = $dbh->prepare($sql_check);
        $stmt_check->bindParam(':user_id', $userId);
        $stmt_check->bindParam(':brand_id', $brandId);
        $stmt_check->execute();

        if ($stmt_check->rowCount() > 0) {
            // お気に入りから削除
            $sql_delete = "DELETE FROM favorite WHERE user_id = :user_id AND brand_id = :brand_id";
            $stmt_delete = $dbh->prepare($sql_delete);
            $stmt_delete->bindParam(':user_id', $userId);
            $stmt_delete->bindParam(':brand_id', $brandId);
            $stmt_delete->execute();

            $response['success'] = true;
            $response['action'] = 'removed'; // 削除された
        } else {
            // お気に入りに追加
            $sql_insert = "INSERT INTO favorite (user_id, brand_id) VALUES (:user_id, :brand_id)";
            $stmt_insert = $dbh->prepare($sql_insert);
            $stmt_insert->bindParam(':user_id', $userId);
            $stmt_insert->bindParam(':brand_id', $brandId);
            $stmt_insert->execute();

            $response['success'] = true;
            $response['action'] = 'added'; // 追加された
        }
    }
}

// 結果をJSONで返す
echo json_encode($response);
?>
