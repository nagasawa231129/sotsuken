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
    if (isset($_GET['query']) && !empty($_GET['query'])) {
        $query = htmlspecialchars($_GET['query'], ENT_QUOTES, 'UTF-8');
        $stmt = $dbh->prepare("SELECT s.shop_id, s.goods, s.price, s.material, sz.size, c.color, b.brand_name, s.thumbnail
                               FROM shop s
                               LEFT JOIN size sz ON s.size = sz.size_id
                               LEFT JOIN color c ON s.color = c.color_id
                               LEFT JOIN brand b ON s.brand_id = b.brand_id
                               WHERE s.goods LIKE :query");
        $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($products) === 0) {
            $stmt = $dbh->prepare("SELECT s.shop_id, s.goods, s.price, s.material, sz.size, c.color, b.brand_name, s.thumbnail
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
        $query = '';
        $stmt = $dbh->prepare("SELECT s.shop_id, s.goods, s.price, s.material, sz.size, c.color, s.thumbnail, b.brand_name
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
        $change_stock = isset($_POST['stock_change']) ? $_POST['stock_change'] : 0;

        $stmt = $dbh->prepare("SELECT material FROM shop WHERE shop_id = :shop_id");
        $stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
        $stmt->execute();
        $current_stock = $stmt->fetchColumn();

        $new_stock = max(0, $current_stock + $change_stock);
  
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
        header("Location: inventory_management.php");
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

    <!-- モーダルのHTML -->
    <div id="imageModal" class="modal">
        <div class="modal-content" id="modalContent">
            <!-- ここに画像が追加されます -->
        </div>
        <span id="closeModal" class="close">&times;</span>
    </div>

    <script>
        // サムネイル画像をクリックした時の処理
        const thumbnails = document.querySelectorAll('.thumbnail');
        const modal = document.getElementById('imageModal');
        const modalContent = document.getElementById('modalContent');
        const closeModal = document.getElementById('closeModal');

        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                const shopId = this.dataset.shopId;  // クリックしたサムネイルのshop_idを取得
                fetch(`show_images.php?shop_id=${shopId}`)  // shop_idを渡して画像を取得
                    .then(response => response.json())  // 画像のBase64エンコードされた配列を取得
                    .then(images => {
                        // モーダル内のコンテンツをクリア
                        modalContent.innerHTML = '';

                        if (images.length > 0) {
                            // 画像を順にモーダルに追加
                            images.forEach(encodedImg => {
                                const imgElement = document.createElement('img');
                                imgElement.src = encodedImg;  // Base64エンコードされた画像をセット
                                imgElement.alt = '商品画像';
                                modalContent.appendChild(imgElement);  // モーダル内に画像を追加
                            });
                            modal.style.display = 'flex';  // モーダルを表示
                        } else {
                            modalContent.innerHTML = "画像が見つかりません";  // 画像がない場合
                            modal.style.display = 'flex';  // モーダルを表示
                        }
                    });
            });
        });

        closeModal.addEventListener('click', function() {
            modal.style.display = 'none';  // モーダルを閉じる
        });

        // モーダル外部をクリックして閉じる
        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.style.display = 'none';  // モーダルを閉じる
            }
        });
    </script>
</body>
</html>
