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
    <link rel="stylesheet" href="login.css">
</head>

<?php
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo <<<___EOF___
  <div class="container">
    <h2 class="title">ログイン</h2> <!-- タイトルがフォームの上に表示される -->
    <form method="POST">
        <label for="Mail">メールアドレス：</label>
        <input type="text" id="Mail" name="Mail" required>
        
        <label for="Pass">パスワード：</label>
        <input type="password" id="Pass" name="Pass" required>
        
        <input type="submit" value="ログイン">
    </form>
    <a href="regist.php">新規登録はこちら</a>
    <a href="password_reset.php">パスワードを忘れた方はこちら</a>
</div>
___EOF___;
echo "<a href='twitter_login.php'>Twitterでログイン</a>";
echo "<a href='line_login.php'>Lineでログイン</a>";
} else {

    $mail = $_POST["Mail"];
    $entered_pass = $_POST["Pass"];

    // $mail = isset($_POST["Mail"]) ? $_POST["Mail"] : null;
    // $entered_pass = isset($_POST["Pass"]) ? $_POST["Pass"] : null;


    if (!empty($mail) && !empty($entered_pass)) {
        try {
            // メールアドレスに基づいてユーザーを検索
            $stmt = $dbh->prepare("SELECT * FROM user WHERE mail = :mail");
            $stmt->bindParam(':mail', $mail);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $stored_hash = $user['pass'];
                // パスワードの検証
                if (password_verify($entered_pass, $stored_hash)) {
                    // ログイン成功
                    $_SESSION['login'] = true;
                    $_SESSION['id'] = $user['user_id'];
                    $_SESSION['name'] = $user['mei'];
                    header('Location: toppage.php');
                    exit();
                } else {
                    // パスワードが間違っている場合
                    $message = "<p class='error'>メールアドレスまたはパスワードが間違っています。</p>";
                }
            } else {
                // メールアドレスが見つからない場合
                $message = "<p class='error'>そのメールアドレスのユーザーは存在しません。</p>";
            }
        } catch (PDOException $e) {
            // DBエラー
            $message = "<p class='error'>エラー: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        // フィールドが空の場合
        $message = "<p class='error'>すべてのフィールドを入力してください。</p>";
    }

    // フォームを再表示して、エラーメッセージを表示
    echo <<<___EOF___
    <div class="container">
    <h2 class="title">ログイン</h2> <!-- タイトルがフォームの上に表示される -->
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
</div>
___EOF___;
}
?>
</html>