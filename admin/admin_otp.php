<?php
include "../../db_open.php";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$message = "";

// OTP再送信処理
if (isset($_POST['resend_otp'])) {
    $mail = $_SESSION['mail'] ?? ''; // セッションからメールアドレスを取得
    if ($mail) {
        $otp = rand(100000, 999999); // 新しい6桁のOTPを生成

        // OTPをpre_userテーブルに更新
        $stmt = $dbh->prepare('UPDATE pre_admin SET otp = :otp WHERE mail = :mail');
        $stmt->execute(['otp' => $otp, 'mail' => $mail]);

        // OTPをメールで送信
        $subject = "新規登録のための認証コード";
        $messageBody = "有効期限は1時間です。\n以下のコードを入力して認証してください。\n\n" . $otp;
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
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verify_otp'])) {  // 修正された名前
    $input_otp = (int)$_POST['otp'] ?? '';
    $mail = $_SESSION['mail'] ?? '';

    // pre_userテーブルからOTPとその発行時刻を取得
    $stmt = $dbh->prepare('SELECT otp, otp_timestamp FROM pre_admin WHERE mail = :mail');
    $stmt->execute(['mail' => $mail]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $stored_otp = (int)$result['otp'];
        $otp_timestamp = strtotime($result['otp_timestamp']); // タイムスタンプをPHPの時間に変換

        // OTPの有効期限を1時間に設定
        if (time() - $otp_timestamp > 3600) {
            $message = "<p class='error'>ワンタイムパスワードが期限切れです。再送信を行ってください。</p>";
        } elseif ($input_otp === $stored_otp) {
            // OTPが一致した場合、pre_userテーブルからメールを取得し、userテーブルに登録
            $stmt = $dbh->prepare('SELECT mail FROM pre_admin WHERE mail = :mail');
            $stmt->execute(['mail' => $mail]);
            $pre_user_mail = $stmt->fetchColumn();

            if ($pre_user_mail) {
                // pre_userから取得したメールをuserテーブルに登録
                $stmt = $dbh->prepare('INSERT INTO admin (mail, otp) VALUES (:mail, :otp)');
                $stmt->execute(['mail' => $pre_user_mail, 'otp' => $input_otp]);

                // pre_userテーブルから該当するレコードを削除
                $stmt = $dbh->prepare('DELETE FROM pre_admin WHERE mail = :mail');
                $stmt->execute(['mail' => $mail]);

                // ユーザー情報をセッションに保存
                $stmt = $dbh->prepare("SELECT * FROM admin WHERE mail = :mail");
                $stmt->execute(['mail' => $pre_user_mail]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    $_SESSION['login'] = true;
                    $_SESSION['id'] = $user['user_Id']; // ユーザーIDをセッションに保存
                    header('Location: admin_next_regist.php'); // 次のページへリダイレクト
                    exit();
                } else {
                    $message = "<p class='error'>ユーザーが見つかりません。</p>";
                }
            } else {
                $message = "<p class='error'>pre_userテーブルに該当するメールが見つかりません。</p>";
            }
        } else {
            $message = "<p class='error'>ワンタイムパスワードが無効です。再度お試しください。</p>";
        }
    } else {
        $message = "<p class='error'>指定されたメールアドレスに対するワンタイムパスワードが見つかりません。</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ワンタイムパスワード確認</title>
    <link rel="stylesheet" href="admin_otp.css">
</head>
<body>
    <div class="container">
        <h1>ワンタイムパスワード確認</h1>
        <?php echo $message; ?>
        <form action="admin_otp.php" method="POST">
            <label for="otp">ワンタイムパスワード:</label>
            <input type="text" id="otp" name="otp" required>
            <input type="submit" name="verify_otp" value="確認">  <!-- 修正された名前 -->
        </form>

        <!-- OTP再送信ボタン -->
        <form action="admin_otp.php" method="POST">
            <input type="submit" name="resend_otp" value="ワンタイムパスワードを再送信">
        </form>

        <a href="admin_login.php">すでにアカウントをお持ちの場合はこちらをクリックしてください</a>
    </div>
</body>
</html>
