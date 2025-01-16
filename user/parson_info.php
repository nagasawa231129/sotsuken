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
    $new_display = $_POST['display'];
    $new_sei = $_POST['sei'];
    $new_kanasei = $_POST['kanasei'];
    $new_mei = $_POST['mei'];
    $new_kanamei = $_POST['kanamei'];
    $new_gender = $_POST['gender'];
    $new_postcode = strval($_POST['zipcode']);
    $new_address = $_POST['address1'];
    $new_phone = str_replace('-', '', $_POST['phone']);

    // 基本情報の更新
    $stmt = $dbh->prepare("UPDATE user SET display_name = ?, sei = ?, kanasei = ?, mei = ?, kanamei = ?, gender_id = ?, postcode = ?, address = ?, phone = ? WHERE user_id = ?");
    $stmt->execute([$new_display, $new_sei, $new_kanasei, $new_mei, $new_kanamei, $new_gender, $new_postcode, $new_address, $new_phone, $userId]);

    // 住所2および3の更新処理
    for ($i = 2; $i <= 3; $i++) {
        $postcode = $_POST["zipcode{$i}"] ?? '';
        $address = $_POST["address{$i}"] ?? '';
        if (!empty($postcode) && !empty($address)) {
            $stmt = $dbh->prepare("UPDATE user SET postcode{$i} = ?, address{$i} = ? WHERE user_id = ?");
            $stmt->execute([$postcode, $address, $userId]);
        }
    }

    echo "情報が更新されました。";
    header('Location: register.php');
    exit();}


function formatPhoneNumber($phoneNumber)
{
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
    let maxAddresses = 3; // 最大追加可能住所数
    let currentAddressCount = 1; // 初期の住所数（デフォルトで住所1がある前提）

    function addAddressField() {
        if (currentAddressCount >= maxAddresses) {
            alert("追加できる住所は最大" + maxAddresses + "件までです。");
            return;
        }

        currentAddressCount++; // 追加される住所の番号を更新
        const nextIndex = currentAddressCount; // 次の住所番号

        // 郵便番号と住所のHTML構築
        const addressHtml = `
            <tr id="address-row-${nextIndex}">
                <td>郵便番号${nextIndex}</td>
                <td>
                    <input type="text" id="zipcode${nextIndex}" name="zipcode${nextIndex}" maxlength="7" placeholder="郵便番号（例: 1234567）">
                    <button type="button" onclick="getAddressFromZipcode('zipcode${nextIndex}', 'address${nextIndex}')">検索</button>
                </td>
            </tr>
            <tr id="address-detail-row-${nextIndex}">
                <td>住所${nextIndex}</td>
                <td><input type="text" id="address${nextIndex}" name="address${nextIndex}" placeholder="住所${nextIndex}"></td>
            </tr>
            <tr id="delete-row-${nextIndex}">
                <td colspan="2" style="text-align: right;">
                    <button type="button" class="delete-button" onclick="removeAddressField(${nextIndex})">削除</button>
                </td>
            </tr>
        `;

        // フォームテーブルに追加
        const table = document.querySelector('form table');
        table.insertAdjacentHTML('beforeend', addressHtml);

        // 最大件数に達したらボタンを無効化
        if (currentAddressCount === maxAddresses) {
            document.getElementById('add-address-button').disabled = true;
        }
    }

    function removeAddressField(index) {
        // 住所行、詳細行、削除ボタン行を削除
        const addressRow = document.getElementById(`address-row-${index}`);
        const addressDetailRow = document.getElementById(`address-detail-row-${index}`);
        const deleteRow = document.getElementById(`delete-row-${index}`);

        if (addressRow) addressRow.remove();
        if (addressDetailRow) addressDetailRow.remove();
        if (deleteRow) deleteRow.remove();

        currentAddressCount--; // 現在の住所数を減らす

        // ボタンを再度有効化（最大件数未満になった場合）
        if (currentAddressCount < maxAddresses) {
            document.getElementById('add-address-button').disabled = false;
        }
    }

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
    <h1><?php echo $translations['Change Account Information'] ?></h1>

    <form method="post" action="parson_info.php">
        <table>
            <tr>
                <td><?php echo $translations['Nickname'] ?></td>
                <td><input type="text" name="display" value="<?php echo htmlspecialchars($user['display_name']); ?>" required></td>
            </tr>
            <tr>
                <td><?php echo $translations['Name'] ?></td>
                <td><input type="text" name="sei" value="<?php echo htmlspecialchars($user['sei']); ?>" required>
                    <input type="text" name="mei" value="<?php echo htmlspecialchars($user['mei']); ?>" required>
                </td>
            </tr>
            <tr>
                <td><?php echo $translations['Name Kana'] ?></td>
                <td><input type="text" name="kanasei" value="<?php echo htmlspecialchars($user['kanasei']); ?>" required>
                    <input type="text" name="kanamei" value="<?php echo htmlspecialchars($user['kanamei']); ?>" required>
                </td>
            </tr>
            <tr>
                <td><?php echo $translations['Gender'] ?></td>
                <td>
                    <label>
                        <input type="radio" name="gender" value="1" <?php if ($user['gender_id'] == 1) echo 'checked'; ?>> <?php echo $translations['Man'] ?>
                    </label>
                    <label>
                        <input type="radio" name="gender" value="2" <?php if ($user['gender_id'] == 2) echo 'checked'; ?>> <?php echo $translations['Woman'] ?>
                    </label>
                </td>
            </tr>

            <!-- 郵便番号1と住所1が存在する場合のみ表示 -->
            <tr>
                <td><?php echo $translations['Post Code'] ?></td>
                <td>
                    <input type="text" id="zipcode1" name="zipcode" maxlength="7" placeholder="郵便番号（例: 1234567）" value="<?php echo htmlspecialchars($user['postcode']); ?>" required>
                    <button type="button" onclick="getAddressFromZipcode('zipcode1', 'address1')"><?php echo $translations['Search'] ?></button>
                </td>
            </tr>
            <tr>
                <td><?php echo $translations['Address'] ?></td>
                <td><input type="text" id="address1" name="address1" placeholder="住所" value="<?php echo htmlspecialchars($user['address']); ?>" required></td>
            </tr>

            <!-- 郵便番号2と住所2が存在する場合のみ表示 -->
            <?php if (!empty($user['postcode2']) && $user['postcode2'] != '0'): ?>
                <tr>
                    <td><?php echo $translations['Post Code'] ?></td>
                    <td>
                        <input type="text" id="zipcode2" name="zipcode2" maxlength="7" placeholder="郵便番号（例: 1234567）" value="<?php echo htmlspecialchars($user['postcode2']); ?>">
                        <button type="button" onclick="getAddressFromZipcode('zipcode2', 'address2')"><?php echo $translations['Search'] ?></button>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $translations['Address'] ?></td>
                    <td><input type="text" id="address2" name="address2" placeholder="住所2" value="<?php echo htmlspecialchars($user['address2']); ?>"></td>
                </tr>
            <?php endif; ?>

            <!-- 郵便番号3と住所3が存在する場合のみ表示 -->
            <?php if (!empty($user['postcode3']) && $user['postcode3'] != '0'): ?>
                <tr>
                    <td><?php echo $translations['Post Code'] ?></td>
                    <td>
                        <input type="text" id="zipcode3" name="zipcode3" maxlength="7" placeholder="郵便番号（例: 1234567）" value="<?php echo htmlspecialchars($user['postcode3']); ?>">
                        <button type="button" onclick="getAddressFromZipcode('zipcode3', 'address3')"><?php echo $translations['Search'] ?></button>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $translations['Address'] ?></td>
                    <td><input type="text" id="address3" name="address3" placeholder="住所3" value="<?php echo htmlspecialchars($user['address3']); ?>"></td>
                </tr>
            <?php endif; ?>

            <!-- 追加された住所フィールドが表示される場所 -->
            <div id="address-fields"></div>



            <tr>
                <td><?php echo $translations['Phone Number'] ?></td>
                <td><input type="text" name="phone" value="<?php echo htmlspecialchars(formatPhoneNumber($user['phone'])); ?>" required></td>
            </tr>
        </table>
        <tr>
    <td colspan="2">
        <button type="button" id="add-address-button" onclick="addAddressField()"><?php echo $translations['Add Address'] ?></button>
    </td>
</tr>
        <button type="submit"><?php echo $translations['Update Information'] ?></button>
    </form>

    <a href="account.php"><?php echo $translations['Return to the Membership Information page'] ?></a>
</body>

</html>