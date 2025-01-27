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

// グループ番号を割り当てる関数
function assignShopGroups($data, $dbh) {
    $maxGroup = getMaxShopGroup($dbh);
    $newGroup = $maxGroup + 1; // 既存の最大値に+1した値から始める
    $groupAssignments = [];
    $lastGroup = null;

    foreach ($data as &$item) {
        if ($item['group'] == 0) {
            // group が 0 の場合は、maxGroup + 1 から開始
            $item['shop_group'] = $newGroup++;  // 新しい shop_group 番号を割り当てる
        } else {
            // group が変わったタイミングで shop_group 番号をインクリメント
            if ($lastGroup !== $item['group']) {
                // group 番号が変わったら、新しい番号を割り当て
                $item['shop_group'] = $newGroup++;
            } else {
                // 同じ group 番号の商品は、同じ shop_group 番号を維持
                $item['shop_group'] = $groupAssignments[$item['group']];
            }
        }
        // 現在の group 番号を記録
        $lastGroup = $item['group'];

        // 現在の group 番号に対する shop_group 番号を保存
        if ($item['group'] !== 0) {
            $groupAssignments[$item['group']] = $item['shop_group'];
        }
    }

    return $data;
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

// 商品データの組み立て
$combinedData = [];
for ($i = 0; $i < count($brands); $i++) {
    $combinedData[] = [
        'brand' => $brands[$i],
        'goods' => $goods[$i],
        'price' => $prices[$i],
        'size' => $sizes[$i],
        'color' => $colors[$i],
        'category' => $categories[$i],
        'subcategory' => $subcategories[$i],
        'gender' => $genders[$i],
        'goods_info' => $goods_info[$i],
        'group' => $groupRequests[$i] // フォームからのグループ情報
    ];
}

// グループごとのデータ整理
$groupedData = [];
foreach ($combinedData as $data) {
    $groupedData[$data['goods']][] = $data;
}

// 商品名ごとにデータを並び替え
foreach ($groupedData as &$group) {
    usort($group, function ($a, $b) {
        if ($a['group'] === 0 && $b['group'] !== 0) return -1;
        if ($a['group'] !== 0 && $b['group'] === 0) return 1;
        return $a['group'] - $b['group'];
    });
}
unset($group);

// 再び全データを一つの配列にまとめる
$sortedData = [];
foreach ($groupedData as $group) {
    $sortedData = array_merge($sortedData, $group);
}

// shop_group を順番に割り当てる
$sortedData = assignShopGroups($sortedData, $dbh);

// 画像の読み込み
$thumbnailData = file_get_contents($_FILES['thumbnail']['tmp_name'][0]);

// トランザクション開始
$dbh->beginTransaction();
try {
    foreach ($sortedData as $data) {
        // 商品を shop テーブルに挿入
        $sql = "INSERT INTO shop (thumbnail, brand_id, goods, price, size, color, category_id, subcategory_id, gender, exp, original_price, shop_group)
                VALUES (:thumbnail, :brand_id, :goods_name, :price, :size, :color, :category_id, :subcategory_id, :gender_id, :goods_info, :original_price, :shop_group)";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([
            ':thumbnail' => $thumbnailData,
            ':brand_id' => $data['brand'],
            ':goods_name' => $data['goods'],
            ':price' => $data['price'],
            ':size' => $data['size'],
            ':color' => $data['color'],
            ':category_id' => $data['category'],
            ':subcategory_id' => $data['subcategory'],
            ':gender_id' => $data['gender'],
            ':goods_info' => $data['goods_info'],
            ':original_price' => $data['price'],
            ':shop_group' => $data['shop_group']
        ]);

        $shop_id = $dbh->lastInsertId(); // 挿入した商品の ID を取得

        // サブサムネイル画像の処理
        if (!empty($_FILES['subthumbnail']['tmp_name'][$index])) {
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
