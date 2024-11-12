<!DOCTYPE html>
<html lang="ja">



<link rel="stylesheet" href="admin_login.css">
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_email = $_POST['admin_email'] ?? '';
    $admin_password = $_POST['admin_password'] ?? '';

    include './../db_open.php'; // データベース接続

    try {
        // SQLクエリの準備（プリペアドステートメント）
        $query = "SELECT * FROM admin WHERE mail = :email AND pass = :password";
        $stmt = $dbh->prepare($query);

        // パラメータをバインド
        $stmt->bindParam(':email', $admin_email);
        $stmt->bindParam(':password', $admin_password);

        // クエリの実行
        $stmt->execute();

        // 結果を取得
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC); 

            // セッションにユーザー情報を保存
            $_SESSION['admin_id'] = $user['admin_id']; 
            $_SESSION['admin_sei'] = $user['sei'];
            $_SESSION['admin_mei'] = $user['mei'];
            $_SESSION['admin_pass'] = $user['pass']; 
           

            echo "<script>alert('ログイン成功'); window.location.href = 'admin_toppage.php';</script>";
        } else {
            echo "<script>alert('ログイン失敗: メールアドレスまたはパスワードが間違っています'); window.location.href = 'admin_login.php';</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('エラー: " . $e->getMessage() . "'); window.location.href = 'admin_login.php';</script>";
    }
}
?>
<body>
    <div class="login-container">
        <h2>管理者ログイン</h2>
        <form action="" method="post">
            <label for="email">Email</label>
            <input type="email" id="admin_email" name="admin_email"  required placeholder="yoshidajobi@example.com" maxlength="50">

            <label for="password">Password</label>
            <input type="password" id="admin_password" name="admin_password" placeholder="半角英数字8文字以上16文字以下で入力" minlength="8" maxlength="16" required > 
            <br>
            <input type="submit" value="ログイン">
        </form>
        <p>
            <a href="admin_add.php">新規登録</a>
            <br>
            <a href="admin_pass_forget.php">パスワード忘れました</a>
            <br>
        </p>
    </div>
</body>

</html>

