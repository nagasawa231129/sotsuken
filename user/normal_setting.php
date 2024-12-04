<?php
// session_start();
include "../../db_open.php";  // DB接続設定
include "../header.php";
echo "<link rel='stylesheet' href='normal_setting.css'>";
echo "<link rel='stylesheet' href='header.css'>";
// ユーザーIDをセッションから取得
if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];
} else {
    echo "ログインしていません。";
    exit;
}

// ユーザー情報をデータベースから取得
$stmt = $dbh->prepare("SELECT * FROM user LEFT OUTER JOIN gender ON gender.gender_id = user.gender_id WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// 現在の情報を変更する場合
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_sei = $_POST['sei'];
    $new_kanasei = $_POST['kanasei'];
    $new_mei = $_POST['mei'];
    $new_kanamei = $_POST['kanamei'];
    $new_gender = $_POST['gender'];
    $new_postcode = strval($_POST['zipcode']);
    $new_address = $_POST['address'];
    $new_phone = str_replace('-', '', $_POST['phone']);  // 入力から「-」を削除

    // データベースの更新
    $stmt = $dbh->prepare("UPDATE user SET sei = ?, kanasei = ?, mei = ?, kanamei = ?, gender_id = ?, postcode = ?, address = ?, phone = ? WHERE user_id = ?");
    $stmt->execute([$new_sei, $new_kanasei, $new_mei, $new_kanamei, $new_gender, $new_postcode, $new_address, $new_phone, $userId]);

    echo "情報が更新されました。";
}

function formatPhoneNumber($phoneNumber) {
    // 電話番号が11桁であれば、適切にハイフンを挿入
    if (strlen($phoneNumber) == 11) {
        return substr($phoneNumber, 0, 3) . '-' . substr($phoneNumber, 3, 4) . '-' . substr($phoneNumber, 7);
    }
    return $phoneNumber;  // それ以外の形式はそのまま表示
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>アカウント情報変更</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // 郵便番号が変更されたときに住所を自動入力する
        function getAddressFromZipcode() {
            var zipcode = document.getElementById("zipcode").value; // 郵便番号の値を取得

            if (zipcode.length == 7) {
                // 郵便番号が7桁の場合、APIを使って住所を取得
                var apiUrl = `https://zipcloud.ibsnet.co.jp/api/search?zipcode=${zipcode}`;

                $.getJSON(apiUrl, function(data) {
                    if (data.results) {
                        // 住所が見つかった場合
                        var address = data.results[0].address1 + data.results[0].address2 + data.results[0].address3;
                        document.getElementById("address").value = address; // 住所を入力欄に自動入力
                    } else {
                        alert("住所が見つかりませんでした。");
                    }
                }).fail(function() {
                    alert("APIのリクエストに失敗しました。");
                });
            } else {
                alert("郵便番号は7桁で入力してください。");
            }
        }
    </script>
</head>

<body>
    <h1>アカウント情報変更</h1>

    <form method="post" action="normal_setting.php">
        <table>
            <tr>
                <td>お名前</td>
                <td><input type="text" name="sei" value="<?php echo htmlspecialchars($user['sei']); ?>" required>
                    <input type="text" name="mei" value="<?php echo htmlspecialchars($user['mei']); ?>" required>
                </td>
            </tr>
            <tr>
                <td>お名前（カナ）</td>
                <td><input type="text" name="kanasei" value="<?php echo htmlspecialchars($user['kanasei']); ?>" required>
                    <input type="text" name="kanamei" value="<?php echo htmlspecialchars($user['kanamei']); ?>" required>
                </td>
            </tr>
            <tr>
                <td>性別</td>
                <td>
                    <label>
                        <input type="radio" name="gender" value="1" <?php if ($user['gender_id'] == 1) echo 'checked'; ?>> 男性
                    </label>
                    <label>
                        <input type="radio" name="gender" value="2" <?php if ($user['gender_id'] == 2) echo 'checked'; ?>> 女性
                    </label>
                </td>
            </tr>

            <table>
                <tr>
                    <td>郵便番号</td>
                    <td>
                        <input type="text" id="zipcode" name="zipcode" maxlength="7" placeholder="郵便番号（例: 1234567）" value="<?php echo htmlspecialchars($user['postcode']); ?>" required>
                        <button type="button" onclick="getAddressFromZipcode()">検索</button>
                    </td>
                </tr>
                <tr>
                    <td>住所</td>
                    <td><input type="text" id="address" name="address" placeholder="住所" value="<?php echo htmlspecialchars($user['address']); ?>" required></td>
                </tr>
            </table>
            <tr>
                <td>電話番号</td>
                <td><input type="text" name="phone" value="<?php echo htmlspecialchars(formatPhoneNumber($user['phone'])); ?>" required></td>
            </tr>
        </table>

        <button type="submit">情報を更新</button>
    </form>

    <a href="account.php">アカウント情報に戻る</a>
</body>

</html>