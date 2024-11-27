<?php
include '../../db_open.php';  // db_open.phpをインクルードして、$dbhを利用できるようにする

// データベース接続が成功しているか確認（デバッグ用）
if ($dbh) {
    // カートの削除処理
    $deleteSql = "DELETE FROM cart";
    $stmt = $dbh->prepare($deleteSql);
    if ($stmt->execute()) {
        echo "success";  // 削除成功のメッセージ
    } else {
        echo "error";    // 削除失敗のメッセージ
    }
} else {
    echo "db_error";    // DB接続エラー
}

// データベース接続を閉じる
$dbh = null;
?>
