<?php
// データベース接続
include './../../db_open.php';

// エラーチェック用フラグ
$hasError = false;
$errorMessages = [];

// サムネイル画像のチェック
if (!isset($_FILES['thumbnail']['tmp_name'][0]) || $_FILES['thumbnail']['error'][0] !== UPLOAD_ERR_OK) {
    $hasError = true;
    $errorMessages[] = "サムネイル画像が正しくアップロードされていません。";
}

// サブサムネイル画像のチェック
if (empty($_FILES['subthumbnail']['tmp_name'][0])) {
    $hasError = true;
    $errorMessages[] = "サブ画像が選択されていません。";
}

// POSTデータのバリデーション
$requiredFields = ['brand', 'goods', 'price', 'size', 'color', 'category', 'subcategory', 'gender', 'goods_info'];
foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        $hasError = true;
        $errorMessages[] = "$field フィールドが未入力です。";
    }
}

if ($hasError) {
    echo implode("<br>", $errorMessages);
    exit;
}

// 送信されたデータを取得
$brands = $_POST['brand'];
$goods = $_POST['goods'];
$prices = $_POST['price'];
$sizes = $_POST['size'];
$colors = $_POST['color'];
$categories = $_POST['category'];
$subcategories = $_POST['subcategory'];
$genders = $_POST['gender'];
$goods_info = $_POST['goods_info'];

// サムネイル画像を取得
$thumbnailData = file_get_contents($_FILES['thumbnail']['tmp_name'][0]);

// トランザクション開始
$dbh->beginTransaction();

try {
    // 商品情報を `shop` テーブルに挿入
    $sql = "INSERT INTO shop (thumbnail, brand_id, goods, price, size, color, category_id, subcategory_id, gender, exp,original_price) 
            VALUES (:thumbnail, :brand_id, :goods_name, :price, :size, :color, :category_id, :subcategory_id, :gender_id, :goods_info ,:original)";
    $stmt = $dbh->prepare($sql);

    // 各商品を挿入
    foreach ($brands as $index => $brand_id) {
        $stmt->execute([
            ':thumbnail' => $thumbnailData,
            ':brand_id' => $brand_id,
            ':goods_name' => $goods[$index],
            ':price' => $prices[$index],
            ':size' => $sizes[$index],
            ':color' => $colors[$index],
            ':category_id' => $categories[$index],
            ':subcategory_id' => $subcategories[$index],
            ':gender_id' => $genders[$index],
            ':goods_info' => $goods_info[$index],
            ':original' => $prices[$index]
        ]);

        // 挿入した商品IDを取得
        $shop_id = $dbh->lastInsertId();

        // サブサムネイル画像を `image` テーブルに挿入
        foreach ($_FILES['subthumbnail']['tmp_name'] as $subIndex => $tmpFile) {
            if (!empty($tmpFile)) {
                $subThumbnailData = file_get_contents($tmpFile);
                $imageStmt = $dbh->prepare("INSERT INTO image (img, shop_id) VALUES (:img, :shop_id)");
                $imageStmt->execute([
                    ':img' => $subThumbnailData,
                    ':shop_id' => $shop_id
                ]);
            }
        }
    }

    // コミット
    $dbh->commit();
    echo "<script><alert>商品が正常に追加されました。</alert></script>";
    header('Location: success_page.php'); // 追加後にリダイレクト
    exit;
} catch (PDOException $e) {
    // エラー発生時はロールバック
    $dbh->rollBack();
    echo "エラーが発生しました: " . $e->getMessage();
    exit;
}
?>
