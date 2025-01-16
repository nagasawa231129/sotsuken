<?php
session_start();
include "../../db_open.php"; // DB接続設定

// ユーザーがログインしているか確認
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'ログインしてください。']);
    exit;
}

$userId = $_SESSION['id'];

// LINE連携解除
$stmt = $dbh->prepare("UPDATE user SET line_user_id = NULL WHERE user_id = ?");
$result = $stmt->execute([$userId]);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'データベースの更新に失敗しました。']);
}
?>
