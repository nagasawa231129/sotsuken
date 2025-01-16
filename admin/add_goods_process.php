<?php
// データベース接続
include './../../db_open.php';

// エラーチェック用フラグ
$hasError = false;
$errorMessages = [];

// 必須項目のバリデーション
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

// 現在の最大 shop_group 値を取得する関数
function getMaxShopGroup($dbh)
{
    $stmt = $dbh->prepare("SELECT MAX(shop_group) FROM `group`");
    $stmt->execute();
    return $stmt->fetchColumn() ?: 0;
}

// グループ指定を処理する関数
function assignShopGroup($dbh, $groupRequests)
{
    $maxGroup = getMaxShopGroup($dbh); // 現在の最大値
    $newGroup = $maxGroup + 1;  // 新しいグループ番号

    // グループ指定を追跡する配列
    $groupAssignments = [];

    foreach ($groupRequests as $groupRequest) {
        // 既に存在するグループ番号を再利用する
        if (isset($groupAssignments[$groupRequest])) {
            $groupAssignments[] = $groupAssignments[$groupRequest];  // 以前のグループを再利用
        } else {
            $groupAssignments[$groupRequest] = $newGroup;
            $newGroup++;  // 新しいグループ番号をインクリメント
        }
    }

    return $groupAssignments;
}

// フォームデータの取得
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

// グループ指定（例）
$groupRequests = ['指定なし', '指定1', '指定1', '指定2', '指定3'];  // フォームからのグループ指定（例）

// グループ番号の割り当て
$groupAssignments = assignShopGroup($dbh, $groupRequests);

// トランザクション開始
$dbh->beginTransaction();
try {
    foreach ($brands as $index => $brand_id) {
        foreach ($sizes as $size) {
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

            // グループ番号を取得
            $group = $groupAssignments[$groupRequests[$index]];

            // groupテーブルにデータ挿入
            $groupStmt = $dbh->prepare("INSERT INTO `group` (shop_group, shop_id) VALUES (:shop_group, :shop_id)");
            $groupStmt->execute([
                ':shop_group' => $group,  // 割り当てたグループ番号を使用
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
