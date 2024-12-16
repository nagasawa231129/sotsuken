<?php
include "../../db_open.php"; // データベース接続
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // セッション開始
}
$message = ""; // メッセージを格納する変数

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // フォームからのメールアドレスの入力を取得
    $mail = $_POST['mail'] ?? ''; 
    $terms = isset($_POST['terms']) ? true : false; // 利用規約に同意したか確認

    // メールアドレスが空でないかチェック
    if (!empty($mail) && empty($message)) {
        try {
            // メールアドレスが既に仮登録されているか確認
            $stmt = $dbh->prepare('SELECT COUNT(*) FROM pre_admin WHERE mail = :mail');
            $stmt->execute(['mail' => $mail]);
            $emailCount = $stmt->fetchColumn();

            // メールアドレスが既に仮登録されている場合
            if ($emailCount > 0) {
                $message = "<p class='error'>このメールアドレスは既に仮登録されています。</p>";
            } else {
                // 仮登録テーブルにメールアドレスを挿入
                $stmt = $dbh->prepare('INSERT INTO pre_admin (mail) VALUES (:mail)');
                $stmt->execute(['mail' => $mail]);

                // OTP（ワンタイムパスワード）を生成
                $otp = rand(100000, 999999); // 6桁のランダムな数値
                $otp_timestamp = date("Y-m-d H:i:s", strtotime("+1 hour")); // 1時間後のタイムスタンプ
                $stmt = $dbh->prepare('UPDATE pre_user SET otp = :otp, otp_timestamp = :otp_timestamp  WHERE mail = :mail');
                $stmt->execute(['otp' => $otp, 'otp_timestamp' => $otp_timestamp, 'mail' => $mail]);

                // OTPをメールで送信
                $subject = "新規登録のための認証コード";
                $messageBody = "有効期限は1時間です。
                以下のコードを入力して認証してください。\n\n" . $otp;
                $headers = "From: sotsuken@st.yoshida-g.ac.jp";

                if (mb_send_mail($mail, $subject, $messageBody, $headers)) {
                    $_SESSION['mail'] = $mail; // OTP検証ページに送るためのメールアドレス
                    header('Location: admin_otp.php'); // OTP確認ページへリダイレクト
                    exit(); // リダイレクト後は処理を終了
                } else {
                    $message = "<p class='error'>メール送信に失敗しました。</p>";
                }
            }
        } catch (PDOException $e) {
            $message = "<p class='error'>エラー: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        $message = "<p class='error'>メールアドレスを入力してください。</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <link rel="stylesheet" href="admin_regist.css"> <!-- スタイルシート -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新規登録</title>
    <script>
        // 利用規約に同意しているかをチェック
        function validateForm(event) {
            const checkbox = document.getElementById('termsCheckbox');
            if (!checkbox.checked) {
                event.preventDefault(); // チェックボックスがチェックされていなければフォーム送信を防ぐ
                alert("利用規約に同意する必要があります。");
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>新規登録</h1>
        <?php echo $message; ?> <!-- メッセージの表示 -->
        <form action="admin_regist.php" method="POST" onsubmit="validateForm(event)">
            <!-- メールアドレス入力フィールド -->
            <label for="mail">メールアドレス:</label>
            <input type="email" id="mail" name="mail" maxlength="50" required pattern="^[\w.%+-]+@[A-Za-z0-9.-]+\.[A-Z]{2,}$" title="有効なメールアドレスを入力してください。">

            <!-- 利用規約同意チェックボックス -->
            <div class="terms-container">
                <input type="checkbox" id="termsCheckbox" name="terms" required>
                <label for="termsCheckbox">利用規約に同意する</label>
            </div>

            <input type="submit" value="新規登録">
        </form>

        <!-- 利用規約とログインページへのリンク -->
        <a href="terms.php">利用規約をご覧ください</a>
        <a href="admin_login.php">すでにアカウントをお持ちの場合はこちらをクリックしてください</a>
    </div>
</body>
</html>
