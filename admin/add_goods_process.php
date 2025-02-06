<?php
// データベース接続
include './../../db_open.php';

// 現在の最大 shop_group 値を取得する関数
function getMaxShopGroup($dbh) {
    $stmt = $dbh->prepare("SELECT MAX(shop_group) FROM shop");
    $stmt->execute();
    return $stmt->fetchColumn() ?: 0;
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
$material = $_POST['material'];

// データを一つの配列にまとめる
$items = [];
foreach ($brands as $index => $brand_id) {
    $items[] = [
        'brand_id' => $brand_id,
        'goods_name' => $goods[$index],
        'price' => $prices[$index],
        'size' => $sizes[$index],
        'color' => $colors[$index],
        'category' => $categories[$index],
        'subcategory' => $subcategories[$index],
        'gender' => $genders[$index],
        'goods_info' => $goods_info[$index],
        'group' => $groupRequests[$index],
        'material' => $material[$index],
    ];
}

// グループをソート (groupの値が小さい順)
usort($items, function ($a, $b) {
    return $a['group'] <=> $b['group'];
});

// データベースの最大値を取得
$currentShopGroup = getMaxShopGroup($dbh); // データベースの最大 shop_group
$previousGroup = null; // 前回の group を記録

// トランザクション開始
$dbh->beginTransaction();
try {
    foreach ($items as $item) {
        // groupの値が変わったらshop_groupを更新
        if ($previousGroup === null || $previousGroup !== $item['group']) {
            $currentShopGroup++; // shop_groupを増やす
            $previousGroup = $item['group'];
        }

        // 商品をshopテーブルに挿入
        $sql = "INSERT INTO shop (thumbnail, brand_id, goods, price, size, color, category_id, subcategory_id, gender, exp, original_price, shop_group, material)
                VALUES (:thumbnail, :brand_id, :goods_name, :price, :size, :color, :category_id, :subcategory_id, :gender_id, :goods_info, :original_price, :shop_group, :material)";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([
            ':thumbnail' => file_get_contents($_FILES['thumbnail']['tmp_name'][0]),  // サムネイル画像
            ':brand_id' => $item['brand_id'],
            ':goods_name' => $item['goods_name'],
            ':price' => $item['price'],
            ':size' => $item['size'],
            ':color' => $item['color'],
            ':category_id' => $item['category'],
            ':subcategory_id' => $item['subcategory'],
            ':gender_id' => $item['gender'],
            ':goods_info' => $item['goods_info'],
            ':original_price' => $item['price'],
            ':shop_group' => $currentShopGroup,
            ':material' => $item['material']
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
