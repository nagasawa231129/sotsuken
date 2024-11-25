<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$message = '';

// GET パラメータからユーザーIDを取得
$userId = $_GET['user_id'] ?? null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 新しいパスワードを取得
    $newPassword = $_POST["newPassword"] ?? '';
    $renewPassword = $_POST["renewPassword"] ?? '';

    // パスワードが一致するか確認
    if (!empty($newPassword) && !empty($renewPassword) && $newPassword === $renewPassword && $userId) {
        // パスワードをハッシュ化
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // データベースに保存
        include "../../db_open.php"; // DB接続
        try {
            $stmt = $dbh->prepare("UPDATE user SET pass = :pass WHERE user_id = :Id");
            $stmt->execute(['pass' => $hashedPassword, 'Id' => $userId]);

            echo "<script>
            alert('パスワードが更新されました。');
            window.location.href = 'login.php';
            </script>";
        } catch (PDOException $e) {
            $message = "<p class='error'>エラー: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        $message = "<p class='error'>新しいパスワードが一致しません。再度入力してください。</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <link rel="stylesheet" href="login.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>パスワード更新</title>
    <script>
        function validatePasswords() {
            const newPassword = document.getElementById('newPassword').value;
            const renewPassword = document.getElementById('renewPassword').value;

            if (newPassword !== renewPassword) {
                alert("パスワードが一致しません。");
                return false;
            }
            return true;
        }
    </script>
</head>

<body>
    <div class="container">
        <h2>パスワード更新</h2>
        <?php echo $message; ?>
        <form method="POST" onsubmit="return validatePasswords();">
            <label for="newPassword">新しいパスワード:</label>
            <input type="password" id="newPassword" name="newPassword" minlength="8" maxlength="16" required 
                pattern="^(?=.*[A-Z]).{8,16}$" 
                title="パスワードは8文字以上16文字以内で、1文字以上の大文字を含めてください。">

            <label for="renewPassword">新しいパスワード（確認用）:</label>
            <input type="password" id="renewPassword" name="renewPassword" minlength="8" maxlength="16" required 
                pattern="^(?=.*[A-Z]).{8,16}$">
            <small>※パスワードは8文字以上16文字以内で、1文字以上の大文字を含めてください。</small>

            <input type="submit" value="パスワードを更新">
        </form>
        <a href="login.php">ログイン画面に戻る</a>
    </div>
</body>
</html>
