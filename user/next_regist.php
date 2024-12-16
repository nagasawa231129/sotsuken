<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // セッション開始
}

include "../head.php";
include "../../db_open.php"; // データベース接続
echo "<link rel='stylesheet' href='regist.css'>";

$message = ""; // メッセージを格納する変数

// ログイン状態の確認
if (!isset($_SESSION['mail'])) {
    header('Location: login.php');
    exit();
}

// フォーム送信時の処理
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pass = $_POST['pass'];
    $repass = $_POST['repass'];
    $sei = $_POST['sei'];
    $kanasei = $_POST['kanasei'];
    $mei = $_POST['mei'];
    $kanamei = $_POST['kanamei'];
    $display = $_POST['display'];

    if (!empty($pass) && !empty($repass) && !empty($sei) && !empty($kanasei) && !empty($mei) && !empty($kanamei) && !empty($display)) {
        if ($pass === $repass) {
            // パスワードをハッシュ化
            $hashed_pass = password_hash($pass, PASSWORD_DEFAULT); // bcrypt使用

            try {
                // データベースにユーザー情報を更新するSQL文
                $sql = "UPDATE user SET pass = :pass, display_name = :display ,sei = :sei, kanasei = :kanasei, mei = :mei, kanamei = :kanamei WHERE mail = :mail";
                $stmt = $dbh->prepare($sql);

                $stmt->execute([
                    ':pass' => $hashed_pass,
                    ':display' => $display,
                    ':sei' => $sei,
                    ':kanasei' => $kanasei,
                    ':mei' => $mei,
                    ':kanamei' => $kanamei,
                    ':mail' => $_SESSION['mail'],
                ]);

                $message = "<div class='success'>新規登録が完了しました。</div>";
                header('Location: toppage.php');
                exit();
            } catch (PDOException $e) {
                $message = "<div class='error'>エラーが発生しました。再度お試しください。</div>";
                error_log($e->getMessage());
            }
        } else {
            $message = "<div class='error'>パスワードが一致しません。</div>";
        }
    } else {
        $message = "<div class='error'>すべての項目を入力してください。</div>";
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

            <label for="pass">パスワード:</label>
            <input type="password" id="pass" name="pass" maxlength="16" minlength="8"
                required pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,16}$"
                placeholder="英数字を含む8字以上16字以内">

            <label for="repass">パスワード再入力:</label>
            <input type="password" id="repass" name="repass" maxlength="16" minlength="8"
                required pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,16}$"
                placeholder="パスワードの再確認">
           
                     <!-- 姓 -->
            <label for="display">ニックネーム:</label>
            <input type="text" id="display" name="display" required>


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

            <input type="submit" value="登録">
        </form>
    </div>
</body>

</html>