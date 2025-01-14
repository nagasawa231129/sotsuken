<?php
include "../../db_open.php"; // DB接続ファイルの読み込み
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$message = ''; // エラーメッセージを初期化

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 入力された値を取得
    $mail = $_POST["Mail"] ?? '';
    $entered_pass = $_POST["Pass"] ?? '';

    if (!empty($mail) && !empty($entered_pass)) {
        try {
            // データベースからユーザーを検索
            $stmt = $dbh->prepare("SELECT * FROM admin WHERE mail = :mail");
            $stmt->bindParam(':mail', $mail, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // 保存されたハッシュを取得
                $stored_hash = $user['pass'];

                // パスワード検証
                if (password_verify($entered_pass, $stored_hash)) {
                    // ログイン成功処理
                    $_SESSION['admin_login'] = true;
                    $_SESSION['admin_id'] = $user['admin_id'];
                    $_SESSION['admin_name'] = $user['mei'];

                    // echo $_SESSION['admin_id'] ;
                    // ログイン成功後にadmin_toppage.phpにリダイレクト
                    header('Location: admin_toppage.php');
                    exit(); // リダイレクト後、スクリプト終了
                } else {
                    // パスワードが一致しない場合
                    $message = "<p class='error'>メールアドレスまたはパスワードが間違っています。</p>";
                }
            // } else {
                
                // メールアドレスが存在しない場合
                $message = "<p class='error'>そのメールアドレスのユーザーは存在しません。</p>";
            }
        } catch (PDOException $e) {
            // DBエラー時の処理
            $message = "<p class='error'>データベースエラー: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        $message = "<p class='error'>すべてのフィールドを入力してください。</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <link rel="stylesheet" href="admin_login.css">
    <title>ログイン</title>
</head>
<body>
<div class="container">
    <h2 class="title">ログイン</h2>
    <form method="POST">
        <?= $message ?>
        <label for="Mail">メールアドレス：</label>
        <input type="text" id="Mail" name="Mail" required>
        
        <label for="Pass">パスワード：</label>
        <input type="password" id="Pass" name="Pass" required>
        
        <input type="submit" value="ログイン">
    </form>
    <a href="admin_regist.php">新規登録はこちら</a>
    <a href="admin_password_reset.php">パスワードを忘れた方はこちら</a>
</div>
</body>
</html>
