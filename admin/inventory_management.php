<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="inventory_management.css">
    <title>在庫管理ページ</title>
</head>

<body>
<a href="admin_toppage.php">トップページ</a>
    <h2 style="text-align: center;">在庫管理ページ</h2>

    <!-- 検索フォーム -->
    <div class="search-container">
        <form method="GET" action="" name="search">
            <input type="text" name="query" placeholder="商品名で検索" value="<?= isset($_GET['query']) ? htmlspecialchars($_GET['query'], ENT_QUOTES, 'UTF-8') : '' ?>" required>
            <input type="submit" value="検索">
        </form>

        <!-- 検索後に全て表示ボタンを表示 -->
        <?php if (isset($_GET['query']) && !empty($_GET['query'])): ?>
            <a href="inventory_management.php">
                <button>全て表示</button>
            </a>
        <?php endif; ?>
    </div>

    <?php
    include "./../../db_open.php"; // DB接続
    session_start();

    // 検索処理
    $query = isset($_GET['query']) ? htmlspecialchars($_GET['query'], ENT_QUOTES, 'UTF-8') : '';
    if (!empty($query)) {
        $stmt = $dbh->prepare("SELECT s.shop_id, s.goods, s.price,s.sale_id, s.material, sz.size, c.ja_color, b.brand_name, s.thumbnail
                               FROM shop s
                               LEFT JOIN size sz ON s.size = sz.size_id
                               LEFT JOIN color c ON s.color = c.color_id
                               LEFT JOIN brand b ON s.brand_id = b.brand_id
                               WHERE s.goods LIKE :query");
        $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($products) === 0) {
            $stmt = $dbh->prepare("SELECT s.shop_id, s.goods, s.sale_id, s.price, s.material, sz.size, c.ja_color, b.brand_name, s.thumbnail
                                   FROM shop s
                                   LEFT JOIN size sz ON s.size = sz.size_id
                                   LEFT JOIN color c ON s.color = c.color_id
                                   LEFT JOIN brand b ON s.brand_id = b.brand_id
                                   WHERE b.brand_name LIKE :query");
            $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } else {
        $stmt = $dbh->prepare("SELECT s.shop_id, s.goods,s.sale_id, s.price, s.material, sz.size, c.ja_color, s.thumbnail, b.brand_name
                               FROM shop s
                               LEFT JOIN size sz ON s.size = sz.size_id
                               LEFT JOIN color c ON s.color = c.color_id
                               LEFT JOIN brand b ON s.brand_id = b.brand_id
                               WHERE s.shop_id LIKE :query");
        $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 在庫更新処理
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_stock'])) {
        $shop_id = $_POST['shop_id'];
        $change_stock = isset($_POST['stock_change']) ? (int)$_POST['stock_change'] : 0; // 明示的に整数にキャスト
    
        $stmt = $dbh->prepare("SELECT material FROM shop WHERE shop_id = :shop_id");
        $stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
        $stmt->execute();
        $current_stock = (int)$stmt->fetchColumn(); // 明示的に整数にキャスト
    
        $new_stock = max(0, $current_stock + $change_stock); // ここで型が正しくなる

        // 現在の日時を取得
        $current_time = date('Y-m-d H:i:s'); // フォーマット: YYYY-MM-DD HH:MM:SS
        
        // UPDATE 文で material と arrival を同時に更新
        $stmt = $dbh->prepare("UPDATE shop SET material = :material, arrival = :arrival WHERE shop_id = :shop_id");
        
        // パラメータをバインド
        $stmt->bindParam(':material', $new_stock, PDO::PARAM_INT);
        $stmt->bindParam(':arrival', $current_time, PDO::PARAM_STR);
        $stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
        
        // クエリを実行
        $stmt->execute();
        
        $_SESSION['flash_message'] = '在庫が更新されました。';
        
        // 検索条件を保持してリダイレクト
        header("Location: inventory_management.php?query=" . urlencode($query));
        exit();
    }
    ?>

    <!-- 商品情報テーブル -->
    <table>
        <tr>
            <th>商品ID</th>
            <th>サムネ</th>
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
                
                <td>
                    <?php
                    $imgBlob = $product['thumbnail']; // サムネイルのBLOBデータ
                    $shopId = $product['shop_id'];    // shop_idを取得
                    if ($imgBlob) {
                        $encodedImg = base64_encode($imgBlob); // Base64エンコード
                        // 画像をクリックするとモーダルが開くように設定
                        echo "<img src='data:image/jpeg;base64,$encodedImg' alt='サムネイル' width='100' class='thumbnail' data-shop-id='$shopId' />";
                    }
                    ?>
                </td>

                <td><?= htmlspecialchars($product['brand_name']) ?></td>
                <td><?= htmlspecialchars($product['goods']) ?></td>
                <td>¥<?= htmlspecialchars(number_format($product['price'])) ?>
            <?php
                // sale_idに基づいて割引パーセンテージを表示
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
                ?></td>
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

    <script>
        window.addEventListener('load', function() {
            const thumbnails = document.querySelectorAll('.thumbnail');
            thumbnails.forEach(thumbnail => {
                thumbnail.addEventListener('click', function() {
                    const shopId = this.getAttribute('data-shop-id');
                    window.location.href = `product_detail.php?shop_id=${shopId}`;
                });
            });
        });
    </script>
</body>
</html>
