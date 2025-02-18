<?php
// データベース接続
include './../../db_open.php';

// エラーチェック用フラグ
$hasError = false;
$errorMessages = [];

// サムネイル画像のチェック
if (!isset($_FILES['thumbnail']['tmp_name']) || empty($_FILES['thumbnail']['tmp_name'][0]) || $_FILES['thumbnail']['error'][0] !== UPLOAD_ERR_OK) {
    $hasError = true;
    $errorMessages[] = "サムネイル画像が正しくアップロードされていません。";
}

// サブサムネイル画像のチェック
if (empty($_FILES['subthumbnail']['tmp_name'])) {
    $hasError = true;
    $errorMessages[] = "サブ画像が選択されていません。";
}

// 必須POSTデータのバリデーション
$requiredFields = ['brand', 'goods', 'price', 'color', 'category', 'subcategory', 'gender', 'goods_info'];
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

// 既存のshop_groupを取得する関数
function getExistingShopGroup($dbh, $brand_id, $goods, $color, $category_id, $subcategory_id)
{
    $stmt = $dbh->prepare("SELECT shop_group FROM shop WHERE brand_id = :brand_id AND goods = :goods AND color = :color AND category_id = :category_id AND subcategory_id = :subcategory_id LIMIT 1");
    $stmt->execute([
        ':brand_id' => $brand_id,
        ':goods' => $goods,
        ':color' => $color,
        ':category_id' => $category_id,
        ':subcategory_id' => $subcategory_id
    ]);
    return $stmt->fetchColumn(); // 既存のshop_groupがあれば取得、なければfalseを返す
}

// 新しいshop_groupを取得する関数
function getNewShopGroup($dbh)
{
    $stmt = $dbh->prepare("SELECT MAX(shop_group) FROM shop");
    $stmt->execute();
    $maxGroup = $stmt->fetchColumn();
    return $maxGroup ? $maxGroup + 1 : 1; // 最大値が存在しない場合は1から開始
}

// データの取得
$brands = $_POST['brand'];
$goods = $_POST['goods'];
$prices = $_POST['price'];
$sizes = $_POST['size'];
$colors = $_POST['color'];
$categories = $_POST['category'];
$subcategories = $_POST['subcategory'];
$genders = $_POST['gender'];
$goods_info = $_POST['goods_info'];
$thumbnailData = file_get_contents($_FILES['thumbnail']['tmp_name'][0]);

// トランザクション開始
$dbh->beginTransaction();

try {
    foreach ($brands as $index => $brand_id) {
        // 既存のshop_groupがあるか確認
        $existingShopGroup = getExistingShopGroup($dbh, $brand_id, $goods[$index], $colors[$index], $categories[$index], $subcategories[$index]);

        // なければ新しいshop_groupを作成
        $shopGroup = $existingShopGroup ? $existingShopGroup : getNewShopGroup($dbh);

        foreach ($sizes as $size) {
            // shopテーブルにデータ挿入
            $sql = "INSERT INTO shop (thumbnail, brand_id, goods, price, size, color, category_id, subcategory_id, gender, exp, original_price, shop_group) 
                    VALUES (:thumbnail, :brand_id, :goods_name, :price, :size, :color, :category_id, :subcategory_id, :gender_id, :goods_info, :original_price, :shop_group)";
            $stmt = $dbh->prepare($sql);
            $stmt->execute([
                ':thumbnail' => $thumbnailData,
                ':brand_id' => $brand_id,
                ':goods_name' => $goods[$index],
                ':price' => $prices[$index],
                ':size' => $size,
                ':color' => $colors[$index],
                ':category_id' => $categories[$index],
                ':subcategory_id' => $subcategories[$index],
                ':gender_id' => $genders[$index],
                ':goods_info' => $goods_info[$index],
                ':original_price' => $prices[$index],
                ':shop_group' => $shopGroup
            ]);

            // 挿入した shop_id を取得
            $shop_id = $dbh->lastInsertId();

            // サブサムネイル画像の処理
            foreach ($_FILES['subthumbnail']['tmp_name'] as $tmpFile) {
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
    }

    // コミット
    $dbh->commit();
    header('Location: success_page.php');
    exit;
} catch (Exception $e) {
    // ロールバック
    $dbh->rollBack();
    error_log("データ挿入エラー: " . $e->getMessage());
    echo "エラーが発生しました: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    exit;
}
?>
