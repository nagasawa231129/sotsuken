<?php
include "../head.php";
include "../../db_open.php"; // データベース接続
echo "<link rel='stylesheet' href='regist.css'>";
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // セッション開始
}
$message = ""; // メッセージを格納する変数

// フォーム送信時の処理
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pass = $_POST['pass'];
    $repass = $_POST['repass'];
    $sei = $_POST['sei'];
    $kanasei = $_POST['kanasei'];
    $mei = $_POST['mei'];
    $kanamei = $_POST['kanamei'];

    
    // 必須項目チェック
    if (!empty($pass) && !empty($repass) && !empty($sei) && !empty($kanasei) && !empty($mei) && !empty($kanamei)) {
        // パスワードが一致するかの確認
        if ($pass === $repass) {
            // パスワードをハッシュ化
            $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

            try {
                // データベースにユーザー情報を更新するSQL文
                $sql = "UPDATE user SET pass = :pass, sei = :sei, kanasei = :kanasei, mei = :mei, kanamei = :kanamei WHERE mail = :mail";
                $stmt = $dbh->prepare($sql);

                // SQL文の実行
                $stmt->execute([
                    ':pass' => $hashed_pass,
                    ':sei' => $sei,
                    ':kanasei' => $kanasei,
                    ':mei' => $mei,
                    ':kanamei' => $kanamei,
                    ':mail' => $_SESSION['mail'] // セッションからメールアドレスを取得

                ]);

                $message = "<script>alert('新規登録が完了しました。');</script>";
                header('Location: toppage.php'); // OTP確認ページへリダイレクト
                    exit();
            } catch (PDOException $e) {
                // エラーメッセージ
                $message = "<p class='error'>エラーが発生しました。再度お試しください。</p>";
                error_log($e->getMessage()); // エラーログの記録
            }
        } else {
            $message = "<p class='error'>パスワードが一致しません。</p>";
        }
    } else {
        $message = "<p class='error'>すべての項目を入力してください。</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<body>
    <div class="container">
        <h1>新規登録</h1>
        <?php echo $message; ?> <!-- メッセージの表示 -->
        <form action="next_regist.php" method="POST" onsubmit="validateForm(event)">
            <!-- パスワード入力 -->
            <label for="pass">パスワード:</label>
            <input type="password" id="pass" name="pass" maxlength="16" minlength="8" required pattern="^[A-Za-z0-9]+$" placeholder="英数字8字以上16字以内">

            <!-- パスワード再入力 -->
            <label for="repass">パスワード再入力:</label>
            <input type="password" id="repass" name="repass" maxlength="16" minlength="8" required pattern="^[A-Za-z0-9]+$" placeholder="英数字8字以上16字以内">

            <!-- 姓 -->
            <label for="sei">姓:</label>
            <input type="text" id="sei" name="sei" required>

            <!-- カナ姓 -->
            <label for="kanasei">カナ姓:</label>
            <input type="text" id="kanasei" name="kanasei" pattern="^[ァ-ンヴー]+$" required>

            <!-- 名 -->
            <label for="mei">名:</label>
            <input type="text" id="mei" name="mei" required>

            <!-- カナ名 -->
            <label for="kanamei">カナ名:</label>
            <input type="text" id="kanamei" name="kanamei" pattern="^[ァ-ンヴー]+$" required>
            <input type="submit" value="新規登録">
        </form>
    </div>
</body>
</html>
