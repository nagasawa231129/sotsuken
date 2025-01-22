<?php
// データベース接続
include './../../db_open.php';

// 現在の最大 shop_group 値を取得する関数
function getMaxShopGroup($dbh)
{
    $stmt = $dbh->prepare("SELECT MAX(shop_group) FROM shop");
    $stmt->execute();
    return $stmt->fetchColumn() ?: 0;
}

function assignShopGroups($groups, $dbh) {
    // 現在使用されている shop_group を取得
    $existingGroups = [];
    $stmt = $dbh->prepare("SELECT DISTINCT shop_group FROM shop");
    $stmt->execute();
    while ($row = $stmt->fetch()) {
        $existingGroups[] = $row['shop_group'];
    }

    // 最大値を取得し、新しいグループ番号を設定
    $maxGroup = getMaxShopGroup($dbh);
    $newGroup = $maxGroup + 1; // 新しいグループ番号の初期値

    // グループ番号の割り当てを管理する配列
    $groupAssignments = [];
    $assignedGroups = []; // すでに割り当てたグループを追跡

    // グループ番号の割り当て処理
    foreach ($groups as $index => $group) {
        if (!isset($assignedGroups[$group])) {
            // グループが指定されていて、まだ割り当てていない場合
            // 最大値 + 1 の新しい番号を割り当て
            $assignedGroup = $newGroup++;
            $assignedGroups[$group] = $assignedGroup; // グループごとに割り当てた番号を記録
        } else {
            // すでに割り当てたグループ番号を再利用
            $assignedGroup = $assignedGroups[$group];
        }

        // 割り当てたグループ番号を保存
        $groupAssignments[$index] = $assignedGroup;
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
$groupRequests = $_POST['group']; // フォームから送信されたグループ指定
$thumbnailData = file_get_contents($_FILES['thumbnail']['tmp_name'][0]);

// グループ番号の割り当て
$groupAssignments = assignShopGroups($groupRequests, $dbh);

// トランザクション開始
$dbh->beginTransaction();
try {
    foreach ($brands as $index => $brand_id) {
        $assignedGroup = $groupAssignments[$index]; // 各商品のグループ番号を取得

        // 商品をshopテーブルに挿入
        $sql = "INSERT INTO shop (thumbnail, brand_id, goods, price, size, color, category_id, subcategory_id, gender, exp, original_price, shop_group)
                VALUES (:thumbnail, :brand_id, :goods_name, :price, :size, :color, :category_id, :subcategory_id, :gender_id, :goods_info, :original_price, :shop_group)";
        $stmt = $dbh->prepare($sql);
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
            ':original_price' => $prices[$index],
            ':shop_group' => $assignedGroup
        ]);

        $shop_id = $dbh->lastInsertId(); // 挿入した商品のIDを取得

        // サブサムネイル画像の処理
        if (isset($_FILES['subthumbnail']['tmp_name'][$index])) {
            foreach ($_FILES['subthumbnail']['tmp_name'][$index] as $tmpFile) {
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
    echo "エラーが発生しました: " . $e->getMessage();
    exit;
}

?>