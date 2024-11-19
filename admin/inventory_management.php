<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="inventory_management.css">
    <title>在庫管理ページ</title>

</head>

<body>
    <h2 style="text-align: center;">在庫管理ページ</h2>

    <!-- 検索フォームと「全て表示」ボタンを横並びにするコンテナ -->
    <div class="search-container">
        <form method="GET" action="" name="search">
            <input type="text" name="query" placeholder="商品名で検索" value="<?= isset($_GET['query']) ? htmlspecialchars($_GET['query'], ENT_QUOTES, 'UTF-8') : '' ?>" required>
            <input type="submit" value="検索">
        </form>

        <!-- 「全て表示」ボタン -->
        <?php if (isset($_GET['query']) && !empty($_GET['query'])): ?>
            <a href="inventory_management.php">
                <button>全て表示</button>
            </a>
        <?php endif; ?>
    </div>

    <?php
    include "./../../db_open.php"; // DB接続

    if (isset($_GET['query']) && !empty($_GET['query'])) {
        $query = htmlspecialchars($_GET['query'], ENT_QUOTES, 'UTF-8'); // 入力された検索キーワードを取得
        
        // 最初に商品名（goods）で検索
        $stmt = $dbh->prepare("SELECT s.shop_id, s.goods, s.price, s.material, sz.size, c.color, b.brand_name 
                               FROM shop s
                               LEFT JOIN size sz ON s.size = sz.size_id
                               LEFT JOIN color c ON s.color = c.color_id
                               LEFT JOIN brand b ON s.brand = b.brand_id
                               WHERE s.goods LIKE :query"); // 商品名に一致するデータを取得
        $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR); // 部分一致検索
        $stmt->execute();
    
        // 検索結果の取得
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // 商品名で検索した結果が空の場合、同じ検索ワードで商品ID（shop_id）で再検索
        if (count($products) === 0) {
            $stmt = $dbh->prepare("SELECT s.shop_id, s.goods, s.price, s.material, sz.size, c.color, b.brand_name 
                                   FROM shop s
                                   LEFT JOIN size sz ON s.size = sz.size_id
                                   LEFT JOIN color c ON s.color = c.color_id
                                   LEFT JOIN brand b ON s.brand = b.brand_id
                                   WHERE b.brand_name LIKE :query"); // ブランド名で再検索
            $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR); // 部分一致検索
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } else {
        $query = ''; // $_GET['query']が空の場合、デフォルトで空文字を設定
    
        // 商品ID（shop_id）で検索
        $stmt = $dbh->prepare("SELECT s.shop_id, s.goods, s.price, s.material, sz.size, c.color, b.brand_name 
                               FROM shop s
                               LEFT JOIN size sz ON s.size = sz.size_id
                               LEFT JOIN color c ON s.color = c.color_id
                               LEFT JOIN brand b ON s.brand = b.brand_id
                               WHERE s.shop_id LIKE :query"); // 商品IDで検索
        $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR); // 部分一致検索
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // 検索結果の表示
    if (count($products) > 0) {
        echo "<h3>在庫一覧</h3>";
    
    } else {
        echo "<p>該当する商品はありません。</p>";
    }


    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_stock'])) {
        $shop_id = $_POST['shop_id'];
        $change_stock = isset($_POST['stock_change']) ? $_POST['stock_change'] : 0;

        // 現在の在庫数を取得
        $stmt = $dbh->prepare("SELECT material FROM shop WHERE shop_id = :shop_id");
        $stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
        $stmt->execute();
        $current_stock = $stmt->fetchColumn();

        // 新しい在庫数を計算し、0未満にならないよう制限
        $new_stock = max(0, $current_stock + $change_stock);

        // 在庫数の更新
        $stmt = $dbh->prepare("UPDATE shop SET material = :material WHERE shop_id = :shop_id");
        $stmt->bindParam(':material', $new_stock, PDO::PARAM_INT);
        $stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
        $stmt->execute();

        echo "<script>alert('在庫が更新されました。');</script>";
    }
    ?>

    <!-- 商品情報テーブル -->
    <table>
        <tr>
            <th>商品ID</th>
            <th>ブランド</th>
            <th>商品名</th>
            <th>価格</th>
            <th>現在の在庫数</th>
            <th>サイズ</th>
            <th>色</th>
            <th>在庫の増減</th>
        </tr>
        <?php foreach ($products as $product): ?>
        <tr>
            <td><?= htmlspecialchars($product['shop_id']) ?></td>
            <td><?= htmlspecialchars($product['brand_name']) ?></td> <!-- 変更: brand → brand_name -->
            <td><?= htmlspecialchars($product['goods']) ?></td>
            <td>¥<?= htmlspecialchars(number_format($product['price'])) ?></td>
            <td><?= htmlspecialchars($product['material']) ?></td>
            <td><?= htmlspecialchars($product['size']) ?></td>
            <td><?= htmlspecialchars($product['color']) ?></td>
            <td>
                <form method="post" action="">
                    <input type="hidden" name="shop_id" value="<?= htmlspecialchars($product['shop_id']) ?>">
                    <input type="number" name="stock_change" placeholder="増減数">
                    <input type="submit" name="update_stock" value="更新">
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>
