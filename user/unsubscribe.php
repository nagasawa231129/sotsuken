<?php
session_start();
include "../../db_open.php";  // DB接続設定

// ユーザーIDをセッションから取得
if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];
} else {
    header("Location: login.php"); // ログインしていない場合はログインページにリダイレクト
    exit;
}

// 退会処理
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm'])) {
    // ユーザー情報を削除（ここではユーザーのデータを削除するSQLを記述）
    $stmt = $dbh->prepare("DELETE FROM user WHERE user_id = ?");
    $stmt->execute([$userId]);

    // セッションを破棄
    session_destroy();

    // 退会完了ページへリダイレクト
    header("Location: toppage.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>退会手続き</title>
    <link rel="stylesheet" href="unsubscribe.css">
</head>
<body>

    <div class="container">
        <h1>退会手続き</h1>
        <p>本当に退会しますか？ 退会後は、すべての会員情報が削除され、再度の復元はできません。</p>

        <div class="warning-message">
            退会すると、今後このアカウントでのログインができなくなります。よろしいですか？
        </div>

        <form method="POST">
            <div class="button-container">
                <button type="submit" name="confirm">退会する</button>
                <button type="button" class="cancel-button" onclick="window.location.href='account.php'">キャンセル</button>
            </div>
        </form>
    </div>

</body>
</html>
