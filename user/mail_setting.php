<?php
include "../../db_open.php";  // DB接続設定
include "../header.php";
echo "<link rel='stylesheet' href='header.css'>";
echo "<link rel='stylesheet' href='mail_setting.css'>";
// ユーザーIDをセッションから取得
if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];
} else {
    echo "ログインしていません。";
    exit;
}

// ユーザー情報をデータベースから取得
$stmt = $dbh->prepare("SELECT * FROM user WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// ランダムなワンタイムパスワードを生成する関数
function generateOTP($length = 6) {
    $otp = '';
    for ($i = 0; $i < $length; $i++) {
        $otp .= random_int(0, 9);
    }
    return $otp;
}

// POSTリクエストが送られた場合
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_mail = $_POST['mail'];

    $stmt = $dbh->prepare('SELECT COUNT(*) FROM user WHERE mail = ?');
    $stmt->execute([$new_mail]);
    if ($stmt->fetchColumn() > 0) {
        echo "このメールアドレスは既に使用されています。";
        exit;
    }
    
    // メールアドレスのバリデーション
    if (filter_var($new_mail, FILTER_VALIDATE_EMAIL)) {
        // ワンタイムパスワード生成
        $otp = generateOTP();
        $otp_timestamp = date("Y-m-d H:i:s", strtotime("+1 hour")); // 1時間後のタイムスタンプ

        // 現在のメールアドレスにOTPを更新
        $current_mail = $user['mail']; // 現在ログイン中のユーザーのメールアドレス
        $stmt = $dbh->prepare('UPDATE user SET otp = :otp, otp_timestamp = :otp_timestamp WHERE mail = :mail');
        $stmt->execute([
            'otp' => $otp,
            'otp_timestamp' => $otp_timestamp,
            'mail' => $current_mail
        ]);

        // 新しいメールアドレスに認証メールを送信
        $to = $new_mail;
        $subject = "メールアドレス認証コード";
        $message = "以下の認証コードを入力してください:\n\n" . $otp;
        $headers = "From: sotsuken@st.yoshida-g.ac.jp";

        if (mail($to, $subject, $message, $headers)) {
            echo "認証メールを送信しました。メールを確認してください。";
            // 認証ページにリダイレクト
            $_SESSION["new_mail"] = $to;
            header("Location: verify_mail.php");
            exit;
        } else {
            echo "メール送信に失敗しました。";
        }
    } else {
        echo "無効なメールアドレスです。";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>メールアドレス変更</title>
</head>

<body>
    <h1><?php echo $translations['Change MailAddress'] ?></h1>

    <form method="post" action="mail_setting.php">
        <table>
            <tr>
                <td><?php echo $translations['New MailAddress'] ?>
                <input type="email" name="mail" placeholder="<?php echo $translations['New MailAddress'] ?>" required></td>
            </tr>
        </table>

        <button type="submit"><?php echo $translations['Send verification email'] ?></button>
    </form>

    <a href="account.php"><?php echo $translations['Return to the Membership Information page'] ?></a>
</body>

</html>
