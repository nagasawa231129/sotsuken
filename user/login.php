<?php
include "../../db_open.php";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$message = ''; // エラーメッセージを初期化
?>

<head>
    <link rel="stylesheet" href="login.css">
</head>
<?php
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo <<<___EOF___
  <div class="container">
    <h2 class="title">ログイン</h2> <!-- タイトルがフォームの上に表示される -->
    <form method="POST">
        <label for="mail">メールアドレス：</label>
        <input type="email" id="mail" name="mail" required>
        
        <label for="pass">パスワード：</label>
        <input type="password" id="pass" name="pass" required>
        
        <input type="submit" value="ログイン">
    </form>
    <a href="regist.php">新規登録はこちら</a>
    <a href="password_reset.php">パスワードを忘れた方はこちら</a>
</div>

___EOF___;
} else {
    $mail = $_POST["mail"];
    $pass = $_POST["pass"];
    if (!empty($mail) && !empty($pass)) {
        try {
            // メールアドレスに基づいてユーザーを検索
            $stmt = $dbh->prepare("SELECT * FROM user WHERE mail = :mail");
            $stmt->bindParam(':mail', $mail);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user && password_verify($pass, $user['pass'])) {
                // ログイン成功
                $_SESSION['login'] = true;
                $_SESSION['id'] = $user['user_id']; // ユーザーIDをセッションに保存
                header('Location: toppage.php'); // トップページにリダイレクト
                exit();
            } else {
                // ログイン失敗
                $message = "<p class='error'>メールアドレスまたはパスワードが間違っています。</p>";
            }
        } catch (PDOException $e) {
            $message = "<p class='error'>エラー: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        $message = "<p class='error'>すべてのフィールドを入力してください</p>";
    }
    // フォームを再表示して、エラーメッセージを表示
    echo <<<___EOF___
    <div class="container">
    <h2 class="title">ログイン</h2>
    <form method="POST">
        <label for="mail">メールアドレス：</label>
        <input type="email" id="mail" name="mail" required>
        
        <label for="pass">パスワード：</label>
        <input type="password" id="pass" name="pass" required>
        
        <input type="submit" value="ログイン">
    </form>
    <a href="register.php">新規登録はこちら</a>
        <a href="password_reset.php">パスワードを忘れた方はこちら</a>
</div>

___EOF___;
}
?>