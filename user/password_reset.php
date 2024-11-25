<?php
include "../../db_open.php";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // フォームからのデータ取得
    $to = $_POST["Mail"] ?? '';

    if (!empty($to)) {
        try {
            // ユーザーを検索
            $stmt = $dbh->prepare("SELECT * FROM user WHERE mail = :Mail");
            $stmt->execute(['Mail' => $to]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // トークンと有効期限の生成
                $token = bin2hex(random_bytes(32));
                $expires_at = date("Y-m-d H:i:s", strtotime('+1 hour'));

                // トークンと有効期限をデータベースに保存
                $stmt = $dbh->prepare("UPDATE user SET reset_token = :token, reset_expires = :expires WHERE user_id = :id");
                $stmt->execute(['token' => $token, 'expires' => $expires_at, 'id' => $user['user_id']]);

                // リセットリンクの生成
                $reset_link = "https://y231129.daa.jp/sotsuken/sotsuken/user/password_update.php?token=" . $token . "&user_id=" . $user['user_id'];

                // メール送信の設定
                $to = $_POST["Mail"] ?? '';
                $subject = "パスワードリセットのご案内";
                $message = "パスワードリセットのリクエストを受け付けました。以下のリンクをクリックしてパスワードを再設定してください。\n\n" . $reset_link . "\n\nリンクの有効期限は1時間です。";
                $headers = "From: sotsuken@st.yoshida-g.ac.jp";

                if (mb_send_mail($to, $subject, $message, $headers)) {
                    $message = "<p class='success'>パスワードリセット用のメールを送信しました。</p>";
                } else {
                    $message = "<p class='error'>メール送信に失敗しました。</p>";
                }
            } else {
                $message = "<p class='error'>情報が一致しません。</p>";
            }
        } catch (PDOException $e) {
            $message = "<p class='error'>エラー: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        $message = "<p class='error'>すべてのフィールドを入力してください</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <link rel="stylesheet" href="login.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="password_reset.css">
    <title>パスワードリセット</title>
</head>

<body>
    <div class="container">
        <h2>パスワードリセット</h2>
        <?php echo $message; ?>
        <form method="POST">
            <label for="Mail">メールアドレス：</label>
            <input type="email" id="Mail" name="Mail" required>

            <input type="submit" value="パスワードリセット">
        </form>
        <a href="login.php">ログイン画面に戻る</a>
    </div>
</body>
</html>
