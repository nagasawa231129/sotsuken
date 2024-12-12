<?php
include '../../db_open.php';
session_start();
$address = "";

$user_id = $_SESSION['id'] ?? null;

if ($user_id == null) {
    echo "<p>ログインしていません</p>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_address'])) {
    $newAddress = $_POST['new_address'];  // 新しい住所を取得

    // 住所が空でないことを確認
    if (empty($newAddress)) {
        echo "<p>住所が入力されていません</p>";
        exit;  // 住所が入力されていない場合は処理を終了
    }

    // ユーザーの現在の住所を取得
    $getAddressSql = "SELECT address, address2, address3 FROM user WHERE user_id = :user_id";
    $stmt = $dbh->prepare($getAddressSql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $userAddress = $stmt->fetch(PDO::FETCH_ASSOC);

    // 新しい住所を適切なカラムに挿入する処理
    if ($userAddress['address'] == null || $userAddress['address'] == '') {
        // address が空ならば address に新しい住所を挿入
        $updateAddressSql = "UPDATE user SET address = :new_address WHERE user_id = :user_id";
    } elseif ($userAddress['address2'] == null || $userAddress['address2'] == '') {
        // address2 が空ならば address2 に新しい住所を挿入
        $updateAddressSql = "UPDATE user SET address2 = :new_address WHERE user_id = :user_id";
    } elseif ($userAddress['address3'] == null || $userAddress['address3'] == '') {
        // address3 が空ならば address3 に新しい住所を挿入
        $updateAddressSql = "UPDATE user SET address3 = :new_address WHERE user_id = :user_id";
    } else {
        // すべての住所欄が埋まっている場合
        echo "<p>住所欄はすべて埋まっています</p>";
        exit;  // 処理を終了
    }

    // SQL文を準備
    $stmt = $dbh->prepare($updateAddressSql);

    // パラメータをバインド
    $stmt->bindParam(':new_address', $newAddress, PDO::PARAM_STR);  // 新しい住所
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);  // ユーザーID

    // SQL文を実行
    if ($stmt->execute()) {
        echo "<p>住所が追加されました</p>";
        echo "<a href='register.php'>戻る</a>";  // 住所変更後に戻るリンクを表示
    } else {
        // 実行失敗時のエラーメッセージ
        $errorInfo = $stmt->errorInfo();
        echo "<p>住所の変更に失敗しました: " . $errorInfo[2] . "</p>";
    }
}
?>
