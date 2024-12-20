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

// グループごとの最大shop_group値を取得する関数
function getNewShopGroup($dbh)
{
    // shop_groupの最大値を取得
    $stmt = $dbh->prepare("SELECT MAX(shop_group) FROM `group`");
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
        // サイズごとに同じグループを使用するための準備
        $newShopGroup = null;

        foreach ($sizes as $size) {
            // shop_groupを初期化または再利用
            if ($newShopGroup === null) {
                // 新しいshop_groupを取得
                $newShopGroup = getNewShopGroup($dbh);
            }

            // shopテーブルにデータ挿入
            $sql = "INSERT INTO shop (thumbnail, brand_id, goods, price, size, color, category_id, subcategory_id, gender, exp, original_price)
                    VALUES (:thumbnail, :brand_id, :goods_name, :price, :size, :color, :category_id, :subcategory_id, :gender_id, :goods_info, :original_price)";
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
                ':original_price' => $prices[$index]
            ]);

            // 挿入したshopのIDを取得
            $shop_id = $dbh->lastInsertId();

            // groupテーブルにデータ挿入
            $groupStmt = $dbh->prepare("INSERT INTO `group` (shop_group, shop_id) VALUES (:shop_group, :shop_id)");
            $groupStmt->execute([
                ':shop_group' => $newShopGroup,
                ':shop_id' => $shop_id
            ]);
        }

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

    // コミット
    $dbh->commit();
    header('Location: success_page.php');
    exit;
} catch (Exception $e) {
    // ロールバック
    $dbh->rollBack();
    echo "エラーが発生しました: " . $e->getMessage();
    exit;
}
?>
