<?php
// DB接続の設定（例）
include "../../db_open.php"; // データベース接続ファイルをインクルード

// POSTされたimage_idを取得
if (isset($_POST['image_id'])) {
    $image_id = $_POST['image_id'];
    echo "受け取ったimage_id: $image_id"; // デバッグ用に表示

    // image_idを使って画像をDBから取得
    $sql = "SELECT * FROM image WHERE image_id = :image_id";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':image_id', $image_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $img_data = $stmt->fetch(PDO::FETCH_ASSOC);
        $encodedImg = base64_encode($img_data['img']); // 画像をBase64エンコード
        echo "<img src='data:image/jpeg;base64,$encodedImg' alt='表示画像' />";
    } else {
        echo "画像が見つかりません。";
    }
} else {
    echo "画像IDが送信されていません。";
}
?>
