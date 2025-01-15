<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // セッション開始
}

include "../head.php";
include "../../db_open.php"; // データベース接続
echo "<link rel='stylesheet' href='next_regist.css'>";

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
    $postcode = $_POST['postcode'];
    $address = $_POST['address'];


    if (!empty($pass) && !empty($repass) && !empty($sei) && !empty($kanasei) && !empty($mei) && !empty($kanamei) && !empty($display) && !empty($postcode) && !empty($address)) {
        if ($pass === $repass) {
            // パスワードをハッシュ化
            $hashed_pass = password_hash($pass, PASSWORD_DEFAULT); // bcrypt使用

            try {
                // データベースにユーザー情報を更新するSQL文
                $sql = "UPDATE user SET pass = :pass, display_name = :display ,sei = :sei, kanasei = :kanasei, mei = :mei, kanamei = :kanamei, postcode = :postcode, address = :address  WHERE mail = :mail";
                $stmt = $dbh->prepare($sql);

                $stmt->execute([
                    ':pass' => $hashed_pass,
                    ':display' => $display,
                    ':sei' => $sei,
                    ':kanasei' => $kanasei,
                    ':mei' => $mei,
                    ':kanamei' => $kanamei,
                    ':address' => $address,
                    ':postcode' => $postcode,
                    ':mail' => $_SESSION['mail'],
                ]);

                $message = "<div class='success'>新規登録が完了しました。</div>";

                $stmt1 = $dbh->prepare("SELECT * FROM user WHERE mail = :mail");
                $stmt1->execute([':mail' => $_SESSION['mail']]);
                $user = $stmt1->fetch(PDO::FETCH_ASSOC);


                // $stmt->bindParam(':displayName', $displayName);
                $$_SESSION['login'] = true;
                $_SESSION['id'] = $user['user_id'];
                $_SESSION['display_name'] = $user['display_name'];
                

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

<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function getAddressFromZipcode(zipcodeId, addressId) {
            const zipcode = document.getElementById(zipcodeId).value;

            if (zipcode.length !== 7) {
                alert("郵便番号は7桁で入力してください。");
                return;
            }

            // 郵便番号APIで住所を取得
            fetch(`https://api.zipaddress.net/?zipcode=${zipcode}`)
                .then(response => response.json())
                .then(data => {
                    if (data.code === 200) {
                        document.getElementById(addressId).value = data.data.fullAddress;
                    } else {
                        alert("住所が見つかりませんでした。");
                    }
                })
                .catch(() => alert("住所取得に失敗しました。"));
        }
    </script>
</head>

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

            <label>郵便番号:</label>
            <input type="text" id="postcode" name="postcode" maxlength="7" placeholder="郵便番号（例: 1234567）" required>
            <button type="button" onclick="getAddressFromZipcode('postcode', 'address')">検索</button>

            <label>住所:</label>
            <input type="text" id="address" name="address" placeholder="住所" required>

            <input type="submit" value="登録">
        </form>
    </div>
</body>

</html>