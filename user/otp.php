<?php
include "../../db_open.php";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$message = "";

// OTP 再送信処理
if (isset($_POST['resend_otp'])) {
    $mail = $_SESSION['mail'] ?? ''; // セッションからメールアドレスを取得
    if ($mail) {
        $otp = rand(100000, 999999); // 新しい6桁のOTPを生成

        // OTPをpre_userテーブルに更新
        $stmt = $dbh->prepare('UPDATE pre_user SET otp = :otp WHERE mail = :mail');
        $stmt->execute(['otp' => $otp, 'mail' => $mail]);

        // OTPをメールで送信
        $subject = "新規登録のための認証コード";
        $messageBody = "有効期限は1時間です。
        以下のコードを入力して認証してください。\n\n" . $otp;
        $headers = "From: sotsuken@st.yoshida-g.ac.jp";

        if (mb_send_mail($mail, $subject, $messageBody, $headers)) {
            $message = "<p class='success'>ワンタイムパスワードを再送信しました。メールを確認してください。</p>";
        } else {
            $message = "<p class='error'>メール送信に失敗しました。</p>";
        }
    } else {
        $message = "<p class='error'>メールアドレスが見つかりません。</p>";
    }
}

// OTP確認処理
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['veirfy_otp'])) {
    $input_otp = (int)$_POST['otp'] ?? '';
    $mail = $_SESSION['mail'] ?? '';

    // pre_userテーブルからOTPを取得
    $stmt = $dbh->prepare('SELECT otp FROM pre_user WHERE mail = :mail');
    $stmt->execute(['mail' => $mail]);
    $stored_otp = $stmt->fetchColumn();

    $stored_otp = (int)$stored_otp;

    if ($input_otp === $stored_otp) {
    //     // OTPが正しい場合、pre_userテーブルからメールを取得し、userテーブルに登録
        $stmt = $dbh->prepare('SELECT mail FROM pre_user WHERE mail = :mail');
        $stmt->execute(['mail' => $mail]);
        $pre_user_mail = $stmt->fetchColumn();

        if ($pre_user_mail) {
            // pre_userから取得したメールをuserテーブルに登録
            $stmt = $dbh->prepare('INSERT INTO user (mail, otp) VALUES (:mail, :otp)');
            $stmt->execute(['mail' => $pre_user_mail, 'otp' => $input_otp]);

            // pre_userテーブルから該当するレコードを削除
            $stmt = $dbh->prepare('DELETE FROM pre_user WHERE mail = :mail');
            $stmt->execute(['mail' => $mail]);

            // ユーザー情報をセッションに保存
            $stmt = $dbh->prepare("SELECT * FROM user WHERE mail = :mail");
            $stmt->execute(['mail' => $pre_user_mail]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $_SESSION['login'] = true;
                $_SESSION['id'] = $user['user_Id']; // ユーザーIDをセッションに保存
                header('Location: next_regist.php'); // 次のページへリダイレクト
                exit();
            } else {
                $message = "<p class='error'>ユーザーが見つかりません。</p>";
            }
        } else {
            $message = "<p class='error'>pre_userテーブルに該当するメールが見つかりません。</p>";
        }
    } else {
        echo "OTPが一致しません。<br>";
        $message = "<p class='error'>OTPが無効です。再度お試しください。</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ワンタイムパスワード確認</title>
    <link rel="stylesheet" href="otp.css">
</head>
<body>
    <div class="container">
        <h1>ワンタイムパスワード確認</h1>
        <?php echo $message; ?>
        <form action="otp.php" method="POST">
            <label for="otp">OTP:</label>
            <input type="text" id="otp" name="otp" required>
            <input type="submit" name="veirfy_otp" value="確認">
        </form>

        <!-- OTP再送信ボタン -->
        <form action="otp.php" method="POST">
            <input type="submit" name="resend_otp" value="OTPを再送信">
        </form>

        <a href="login.php">すでにアカウントをお持ちの場合はこちらをクリックしてください</a>
    </div>
</body>
</html>
