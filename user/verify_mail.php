<?php
session_start();
include "../../db_open.php";  // DB接続設定

// ユーザーIDをセッションから取得
if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];
} else {
    echo "ログインしていません。";
    exit;
}

// POSTリクエストが送られた場合（OTPの入力があった場合）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputOtp = $_POST['otp'];

    // 現在ログイン中のメールアドレスからOTPを取得
    $stmt = $dbh->prepare("SELECT otp, otp_timestamp FROM user WHERE user_id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    var_dump($user); // デバッグ用

    // OTPの検証
    if ($user && (int)$user['otp'] === (int)$inputOtp) { // ここで明示的に整数型で比較
        $currentTimestamp = date("Y-m-d H:i:s");
    
        // OTPの有効期限を確認
        if ($currentTimestamp <= $user['otp_timestamp']) {
            echo "認証成功！メールアドレスが変更されました。";
    
            // 新しいメールアドレスをセッションから取得
            if (isset($_SESSION['new_mail'])) {
                $newMail = $_SESSION['new_mail'];
    
                // 新しいメールアドレスを更新
                $stmt = $dbh->prepare("UPDATE user SET mail = ? WHERE user_id = ?");
                $stmt->execute([$newMail, $userId]);
    
                // セッションの`new_mail`をクリア（セキュリティのため）
                unset($_SESSION['new_mail']);
            }
    
            // OTPの無効化（セキュリティのため）
            $stmt = $dbh->prepare("UPDATE user SET otp = NULL, otp_timestamp = NULL WHERE user_id = ?");
            $stmt->execute([$userId]);
    
            header("Location: account.php"); // アカウントページにリダイレクト
            exit;
        } else {
            echo "認証コードの有効期限が切れています。";
        }
    } else {
        echo "認証コードが正しくありません。";
    }
    
}
?>


<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>認証コード入力</title>
</head>

<body>
    <h1>認証コード入力</h1>

    <form method="post" action="verify_mail.php">
        <table>
            <tr>
                <td>認証コード</td>
                <td><input type="text" name="otp" placeholder="認証コードを入力" required></td>
            </tr>
        </table>

        <button type="submit">認証する</button>
    </form>

    <a href="mail_setting.php">戻る</a>
</body>

</html>
