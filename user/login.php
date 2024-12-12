<?php
include "../../db_open.php";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$message = ''; // エラーメッセージを初期化
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
<header>
<!-- ヘッダー部分を追加 -->
<div class="search-container">
    <!-- 「卒研TOWN」を左側に移動 -->
    <div class="search-bar">
        <a class="site-name" href="/sotsuken/sotsuken/user/toppage.php">卒研TOWN</a>
    </div>
</div>
</header>
<?php
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo <<<HTML
    <div class="container">
        <h2 class="title">ログイン</h2>
        <form method="POST">
            <label for="Mail">メールアドレス：</label>
            <input type="text" id="Mail" name="Mail" required>
            
            <label for="Pass">パスワード：</label>
            <input type="password" id="Pass" name="Pass" required>
            
            <input type="submit" value="ログイン">
        </form>
        <a href="regist.php">新規登録はこちら</a>
        <a href="password_reset.php">パスワードを忘れた方はこちら</a>
        <a href="twitter_login.php" class="sns-login twitter">Twitterでログイン</a>
        <a href="line_login.php" class="sns-login line">Lineでログイン</a>
        <a href="google_login.php" class="sns-login google">Googleでログイン</a>
    </div>
HTML;
} else {
    $mail = $_POST["Mail"];
    $entered_pass = $_POST["Pass"];

    if (!empty($mail) && !empty($entered_pass)) {
        try {
            $stmt = $dbh->prepare("SELECT * FROM user WHERE mail = :mail");
            $stmt->bindParam(':mail', $mail);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $stored_hash = $user['pass'];
                if (password_verify($entered_pass, $stored_hash)) {
                    $_SESSION['login'] = true;
                    $_SESSION['id'] = $user['user_id'];
                    $_SESSION['display_name'] = $user['display_name'];
                    header('Location: toppage.php');
                    exit();
                } else {
                    $message = "<p class='error'>メールアドレスまたはパスワードが間違っています。</p>";
                }
            } else {
                $message = "<p class='error'>そのメールアドレスのユーザーは存在しません。</p>";
            }
        } catch (PDOException $e) {
            $message = "<p class='error'>エラー: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        $message = "<p class='error'>すべてのフィールドを入力してください。</p>";
    }

    echo <<<HTML
    <div class="container">
        <h2 class="title">ログイン</h2>
        <form method="POST">
            $message
            <label for="Mail">メールアドレス：</label>
            <input type="text" id="Mail" name="Mail" required>
            
            <label for="Pass">パスワード：</label>
            <input type="password" id="Pass" name="Pass" required>
            
            <input type="submit" value="ログイン">
        </form>
        <a href="regist.php">新規登録はこちら</a>
        <a href="password_reset.php">パスワードを忘れた方はこちら</a>
        <a href="twitter_login.php" class="sns-login twitter">Twitterでログイン</a>
        <a href="line_login.php" class="sns-login line">Lineでログイン</a>
        <a href="google_login.php" class="sns-login google">Googleでログイン</a>
    </div>
HTML;
}
?>
</body>
</html>
