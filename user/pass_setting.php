<?php
include "../../db_open.php";
include "../header.php";
include "../head.php";
echo "<link rel='stylesheet' href='header.css'>";
echo "<link rel='stylesheet' href='pass_setting.css'>";

// ログインユーザーIDを取得
if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];
} else {
    $userId = null;
    echo "ログインしていないため、アクセスできません。";
    exit;
}

// 現在のパスワードが送信された場合
if (isset($_POST['current_password'], $_POST['new_password'], $_POST['confirm_password'])) {
    // フォームから送信された情報を取得
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // ユーザーIDはセッションから取得し、DBからユーザー情報を取得
    $stmt = $dbh->prepare("SELECT pass FROM user WHERE user_id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    // データベースから取得したパスワード（ハッシュ）
    $stored_password = $user['pass']; // ここでデータベースから取得したパスワードハッシュを設定

    // 現在のパスワードが正しいかを確認
    if (password_verify($current_password, $stored_password)) {
        // 新しいパスワードの検証
        if (strlen($new_password) >= 8 && preg_match('/[A-Z]/', $new_password)) {
            // 新しいパスワードと確認用パスワードが一致するか確認
            if ($new_password === $confirm_password) {
                // パスワードをハッシュ化してデータベースに保存
                $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

                // パスワードの更新処理
                $stmt = $dbh->prepare("UPDATE user SET pass = ? WHERE user_id = ?");
                $stmt->execute([$hashed_new_password, $userId]);

                echo "パスワードが変更されました。";
            } else {
                echo "新しいパスワードと確認用パスワードが一致しません。";
            }
        } else {
            echo "新しいパスワードは8文字以上で、大文字を1文字以上含める必要があります。";
        }
    } else {
        echo "現在のパスワードが正しくありません。";
    }
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>パスワード変更</title>
</head>
<body>
    <h1>パスワード変更</h1>
    <form method="post" action="pass_setting.php">
        <label for="current_password">現在のパスワード:</label>
        <input type="password" name="current_password" id="current_password" required><br>

        <label for="new_password">新しいパスワード:</label>
        <input type="password" name="new_password" id="new_password" required><br>

        <label for="confirm_password">新しいパスワード（確認）:</label>
        <input type="password" name="confirm_password" id="confirm_password" required><br>

        <button type="submit">パスワードを変更</button>
    </form>
    <a href="account.php">アカウントページに戻る</a>
</body>
</html>
