<?php
include "../../../../db_open.php"; // PDO接続のファイルをインクルード
include "../../../head.php";
include "../../../header.php";
echo "<link rel='stylesheet' href='../../header.css'>";
echo "<link rel='stylesheet' href='../category.css'>";

$sql = "SELECT shop.shop_id, shop.goods 
FROM shop 
JOIN subcategory ON shop.subcategory_id = subcategory.subcategory_id
WHERE subcategory.subcategory_name = 'ブルゾン'";

$stmt = $dbh->query($sql);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>その他トップス商品一覧</title>
</head>
<body>
    <h1>その他トップス 商品一覧</h1>
    <ul>
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <li><?php echo htmlspecialchars($product['goods'], ENT_QUOTES, 'UTF-8'); ?></li>
            <?php endforeach; ?>
        <?php else: ?>
            <p>商品がありません。</p>
        <?php endif; ?>
    </ul>
</body>
</html>