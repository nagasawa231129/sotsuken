<?php
include "../../db_open.php";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}$message = ''; // エラーメッセージを初期化
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
        <label for="Mail">メールアドレス：</label>
        <input type="text" id="Mail" name="Mail" required>
        
        <label for="Pass">パスワード：</label>
        <input type="password" id="Pass" name="Pass" required>
        
        <input type="submit" value="ログイン">
    </form>
    <a href="register.php">新規登録はこちら</a>
    <a href="password_reset.php">パスワードを忘れた方はこちら</a>
</div>

___EOF___;
} else {
    $mail = $_POST["Mail"];
    $pass = $_POST["Pass"];
    if (!empty($mail) && !empty($pass)) {
        try {
            // メールアドレスに基づいてユーザーを検索
            $stmt = $dbh->prepare("SELECT * FROM stuser WHERE Mail = :Mail");
            $stmt->bindParam(':Mail', $mail);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user && password_verify($pass, $user['Pass'])) {
                // ログイン成功
                $_SESSION['login'] = true;
                $_SESSION['id'] = $user['StUser_Id']; // ユーザーIDをセッションに保存
                header('Location: top.php'); // トップページにリダイレクト
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
    <h2 class="title">ログイン</h2> <!-- タイトルがフォームの上に表示される -->
    <form method="POST">
    $message
        <label for="Mail">メールアドレス：</label>
        <input type="text" id="Mail" name="Mail" required>
        
        <label for="Pass">パスワード：</label>
        <input type="password" id="Pass" name="Pass" required>
        
        <input type="submit" value="ログイン">
    </form>
    <a href="register.php">新規登録はこちら</a>
        <a href="password_reset.php">パスワードを忘れた方はこちら</a>
</div>

___EOF___;
}
?>