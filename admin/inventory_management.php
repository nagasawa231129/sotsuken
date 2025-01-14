<?php
// DB接続
// データベース接続
include './../../db_open.php';

// ページ番号の取得（デフォルトは1ページ目）
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 30; // 1ページに表示する商品数
$offset = ($page - 1) * $items_per_page; // 取得する商品の開始位置
$query = isset($_GET['query']) ? $_GET['query'] : ''; // 検索クエリ

// 検索クエリがあれば、それに基づいて商品を取得
if (!empty($query)) {
    $stmt = $dbh->prepare("SELECT s.shop_id, s.goods, s.price, s.sale_id, s.material, sz.size, c.ja_color, b.brand_name, s.thumbnail
                           FROM shop s
                           LEFT JOIN size sz ON s.size = sz.size_id
                           LEFT JOIN color c ON s.color = c.color_id
                           LEFT JOIN brand b ON s.brand_id = b.brand_id
                           WHERE s.goods LIKE :query
                           LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
    $stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // 検索条件がない場合、全商品を表示
    $stmt = $dbh->prepare("SELECT s.shop_id, s.goods, s.sale_id, s.price, s.material, sz.size, c.ja_color, s.thumbnail, b.brand_name
                           FROM shop s
                           LEFT JOIN size sz ON s.size = sz.size_id
                           LEFT JOIN color c ON s.color = c.color_id
                           LEFT JOIN brand b ON s.brand_id = b.brand_id
                           LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 次のページと前のページにリンクを表示するために、総商品数を取得
$stmt = $dbh->prepare("SELECT COUNT(*) FROM shop s WHERE s.goods LIKE :query");
$stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
$stmt->execute();
$total_items = $stmt->fetchColumn();
$total_pages = ceil($total_items / $items_per_page);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="inventory_management.css">
    <title>商品管理</title>
    <style>
        /* ページネーション */
        .pagination {
            margin-top: 20px;
            text-align: center;
        }

        .pagination a {
            margin: 0 5px;
            text-decoration: none;
        }
    </style>
</head>

<body>
<a href="admin_toppage.php" class="back-link">戻る</a>

    <h1 class="title">商品管理</h1>

    <form method="get" style="text-align: center; margin-bottom: 20px;">
        <input type="text" name="query" value="<?= htmlspecialchars($query) ?>" placeholder="商品名で検索" style="padding: 10px; font-size: 16px; width: 300px; margin-right: 10px;">
        <input type="submit" value="検索" style="padding: 10px 20px; font-size: 16px; background-color: #4CAF50; color: white; border: none; cursor: pointer;">
    </form>

    <!-- 商品リスト -->
    <table border="1">
        <tr>
            <th>商品ID</th>
            <th>サムネ</th>
            <th>ブランド</th>
            <th>商品名</th>
            <th>価格</th>
            <th>材料</th>
            <th>サイズ</th>
            <th>色</th>
            <th>在庫の増減</th>
        </tr>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?= htmlspecialchars($product['shop_id']) ?></td>
                <td>
                    <?php
                    $imgBlob = $product['thumbnail'];
                    $shopId = $product['shop_id'];
                    if ($imgBlob) {
                        $encodedImg = base64_encode($imgBlob);
                        echo "<img src='data:image/jpeg;base64,$encodedImg' alt='サムネイル' width='100' class='thumbnail' data-shop-id='$shopId' />";
                    }
                    ?>
                </td>
                <td><?= htmlspecialchars($product['brand_name']) ?></td>
                <td><?= htmlspecialchars($product['goods']) ?></td>
                <td>¥<?= htmlspecialchars(number_format($product['price'])) ?>
                    <?php
                    if ($product['sale_id'] != null) {
                        switch ($product['sale_id']) {
                            case 1:
                                echo ' <span style="color: red;">10%OFF中</span>';
                                break;
                            case 2:
                                echo ' <span style="color: red;">20%OFF中</span>';
                                break;
                            case 3:
                                echo ' <span style="color: red;">30%OFF中</span>';
                                break;
                            case 4:
                                echo ' <span style="color: red;">40%OFF中</span>';
                                break;
                            case 5:
                                echo ' <span style="color: red;">50%OFF中</span>';
                                break;
                            case 6:
                                echo ' <span style="color: red;">60%OFF中</span>';
                                break;
                            case 7:
                                echo ' <span style="color: red;">70%OFF中</span>';
                                break;
                            default:
                                break;
                        }
                    }
                    ?>
                </td>
                <td><?= htmlspecialchars($product['material']) ?></td>
                <td><?= htmlspecialchars($product['size']) ?></td>
                <td><?= htmlspecialchars($product['ja_color']) ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="shop_id" value="<?= htmlspecialchars($product['shop_id']) ?>">
                        <input type="number" name="stock_change" style="width: 60px;">
                        <input type="submit" name="update_stock" value="更新">
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <!-- ページネーション -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="inventory_management.php?page=<?= $page - 1 ?>&query=<?= htmlspecialchars($query) ?>">前のページ</a>
        <?php endif; ?>

        <span>ページ <?= $page ?> / <?= $total_pages ?></span>

        <?php if ($page < $total_pages): ?>
            <a href="inventory_management.php?page=<?= $page + 1 ?>&query=<?= htmlspecialchars($query) ?>">次のページ</a>
        <?php endif; ?>
    </div>
</body>

</html>