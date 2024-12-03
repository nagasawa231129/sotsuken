<link rel="stylesheet" href="goods_info.css">

<a href="admin_toppage.php">戻る</a>

<?php
// データベース接続
include './../../db_open.php';
// 商品情報を更新する処理
if (isset($_POST['update'])) {
    $shop_id = $_POST['shop_id'];
    $goods = $_POST['goods'];
    $price = $_POST['price'];
    $size = $_POST['size'];
    $color = $_POST['color'];
    $category = $_POST['category'];
    $subcategory = $_POST['subcategory'];
    $gender = $_POST['gender'];
    $brand = $_POST['brand'];

    // 商品情報を更新するSQLクエリ
    $update_sql = "UPDATE shop 
                   SET goods = :goods, price = :price, size = :size, color = :color, category_id = :category, 
                       subcategory_id = :subcategory, gender = :gender, brand_id = :brand 
                   WHERE shop_id = :shop_id";

    // SQLの準備
    $stmt = $dbh->prepare($update_sql);
    $stmt->bindParam(':goods', $goods);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':size', $size);
    $stmt->bindParam(':color', $color);
    $stmt->bindParam(':category', $category);
    $stmt->bindParam(':subcategory', $subcategory);
    $stmt->bindParam(':gender', $gender);
    $stmt->bindParam(':brand', $brand);
    $stmt->bindParam(':shop_id', $shop_id);

    // 実行
    $stmt->execute();
}

// 商品情報を削除する処理
if (isset($_POST['delete'])) {
    $shop_id = $_POST['shop_id'];

    // 商品を削除するSQLクエリ
    $delete_sql = "DELETE FROM shop WHERE shop_id = :shop_id";

    // SQLの準備
    $stmt = $dbh->prepare($delete_sql);
    $stmt->bindParam(':shop_id', $shop_id);

    // 実行
    $stmt->execute();
}

// 検索条件が送信された場合
$search_query = '';
if (isset($_POST['search'])) {
    $search_query = $_POST['search_query'];  // フォームから送信された検索キーワードを受け取る
}

// 商品情報を取得するSQLクエリ
$sql = "SELECT 
            shop.shop_id,
            shop.goods,
            shop.price,
            shop.size AS size_id,
            shop.color AS color_id,
            shop.category_id AS category_id,
            shop.gender AS gender_id,
            brand.brand_id, 
            brand.brand_name,          
            color.ja_color AS color_name,  
            gender.gender AS gender_name,
            size.size,
            subcategory.subcategory_name,
            category.category_name,
            shop.subcategory_id AS subcategory_id,
            shop.thumbnail
        FROM shop
        LEFT JOIN brand ON shop.brand_id = brand.brand_id  
        LEFT JOIN color ON shop.color = color.color_id  
        LEFT JOIN category ON shop.category_id = category.category_id  
        LEFT JOIN gender ON shop.gender = gender.gender_id
        LEFT JOIN subcategory ON shop.subcategory_id = subcategory.subcategory_id
        LEFT JOIN size ON shop.size = size.size_id
        WHERE shop.goods LIKE :search_query";  // 商品名による検索条件を追加

// プレースホルダに検索条件をバインド
$stmt = $dbh->prepare($sql);
$stmt->bindValue(':search_query', '%' . $search_query . '%', PDO::PARAM_STR);
$stmt->execute();
?>
<div class="select">
    <button id="add-button" onclick="location.href='add_goods.php'">追加</button>
    <button id="sale-button" onclick="location.href='sale.php'">セール</button>
</div>

<div class="form-container">
    <!-- 商品名検索フォーム -->
    <form method="post" action="">
        <input type="text" name="search_query" placeholder="商品名で検索" value="<?= htmlspecialchars($search_query) ?>">
        <button type="submit" name="search">検索</button>
        <!-- 全て表示するボタン -->
        <button type="submit" name="reset_search">全て表示する</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>サムネ</th>
                <th>ブランド</th>
                <th>商品名</th>
                <th>価格(円)</th>
                <th>サイズ</th>
                <th>色</th>
                <th>カテゴリ</th>
                <th>サブカテゴリー</th>
                <th>性別</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // 「全て表示する」ボタンが押された場合、検索条件をリセット
            if (isset($_POST['reset_search'])) {
                $search_query = '';  // 検索クエリを空にしてすべての商品を表示
                $stmt = $dbh->prepare("SELECT 
                                        shop.shop_id,
                                        shop.goods,
                                        shop.price,
                                        shop.size AS size_id,
                                        shop.color AS color_id,
                                        shop.category_id AS category_id,
                                        shop.gender AS gender_id,
                                        brand.brand_id, 
                                        brand.brand_name,          
                                        color.ja_color AS color_name,  
                                        gender.gender AS gender_name,
                                        size.size,
                                        subcategory.subcategory_name,
                                        category.category_name,
                                        shop.subcategory_id AS subcategory_id,
                                   
                                    FROM shop
                                    LEFT JOIN brand ON shop.brand_id = brand.brand_id  
                                    LEFT JOIN color ON shop.color = color.color_id  
                                    LEFT JOIN category ON shop.category_id = category.category_id  
                                    LEFT JOIN gender ON shop.gender = gender.gender_id
                                    LEFT JOIN subcategory ON shop.subcategory_id = subcategory.subcategory_id
                                    LEFT JOIN size ON shop.size = size.size_id");
                $stmt->execute();
            }
            ?>

            <?php if ($stmt->rowCount() > 0): ?>
                <?php while ($product = $stmt->fetch(PDO::FETCH_ASSOC)): ?>

                    <form method="post" action="">
                        <input type="hidden" name="shop_id" value="<?= htmlspecialchars($product['shop_id']) ?>">
                        <tr>

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
                            <td>
                                <select name="brand" required>
                                    <?php
                                    $brand_sql = "SELECT brand_id, brand_name FROM brand";
                                    foreach ($dbh->query($brand_sql) as $brand_option) {
                                        $selected = ($product['brand_id'] == $brand_option['brand_id']) ? 'selected' : '';
                                        echo "<option value='{$brand_option['brand_id']}' {$selected}>{$brand_option['brand_name']}</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                            <td><input type="text" name="goods" value="<?= htmlspecialchars($product['goods']) ?>" required></td>
                            <td class="price-select"><input type="number" name="price" value="<?= htmlspecialchars($product['price']) ?>" required></td>
                            <td>
                                <select name="size" class="size-select" required>
                                    <?php
                                    $size_sql = "SELECT size_id, size FROM size";
                                    foreach ($dbh->query($size_sql) as $size_option) {
                                        $selected = ($product['size_id'] == $size_option['size_id']) ? 'selected' : '';
                                        echo "<option value='{$size_option['size_id']}' {$selected}>{$size_option['size']}</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <select name="color" required>
                                    <?php
                                    $color_sql = "SELECT color_id, ja_color FROM color";
                                    foreach ($dbh->query($color_sql) as $color_option) {
                                        $selected = ($product['color_id'] == $color_option['color_id']) ? 'selected' : '';
                                        echo "<option value='{$color_option['color_id']}' {$selected}>{$color_option['ja_color']}</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <select name="category" class="category" required onchange="updateSubcategory(this)">
                                    <?php
                                    $category_sql = "SELECT category_id, category_name FROM category";
                                    foreach ($dbh->query($category_sql) as $category_option) {
                                        $selected = ($product['category_id'] == $category_option['category_id']) ? 'selected' : '';
                                        echo "<option value='{$category_option['category_id']}' {$selected}>{$category_option['category_name']}</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <select name="subcategory" class="subcategory" required>
                                    <?php
                                    // サブカテゴリーを取得して選択肢を表示
                                    $subcategory_sql = "SELECT subcategory_id, subcategory_name FROM subcategory WHERE category_id = ?";
                                    $stmt_subcategory = $dbh->prepare($subcategory_sql);
                                    $stmt_subcategory->execute([$product['category_id']]);
                                    while ($subcategory_option = $stmt_subcategory->fetch(PDO::FETCH_ASSOC)) {
                                        $selected = ($product['subcategory_id'] == $subcategory_option['subcategory_id']) ? 'selected' : '';
                                        echo "<option value='{$subcategory_option['subcategory_id']}' {$selected}>{$subcategory_option['subcategory_name']}</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <select name="gender" required>
                                    <?php
                                    $gender_sql = "SELECT gender_id, gender FROM gender";
                                    foreach ($dbh->query($gender_sql) as $gender_option) {
                                        $selected = ($product['gender_id'] == $gender_option['gender_id']) ? 'selected' : '';
                                        echo "<option value='{$gender_option['gender_id']}' {$selected}>{$gender_option['gender']}</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <button type="submit" name="update">更新</button>
                            </td>
                            <td>
                                <button type="submit" name="delete">削除</button>
                            </td>
                    </form>
                    <div id="imageModal" class="modal">
                        <div class="modal-content" id="modalContent">
                            <!-- ここに画像が追加されます -->
                        </div>
                        <span id="closeModal" class="close">&times;</span>
                    </div>

                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">商品情報が見つかりません。</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    // categoryが選択された時にsubcategoriesを更新
    function updateSubcategory(categoryElement) {
        var categoryId = categoryElement.value;
        var subcategorySelect = categoryElement.closest('tr').querySelector('.subcategory');

        // AJAXを使用してサーバーにリクエストを送信
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'get_subcategories.php?category_id=' + categoryId, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                // サブカテゴリーを更新
                subcategorySelect.innerHTML = xhr.responseText;
            }
        };
        xhr.send();
    }
    const thumbnails = document.querySelectorAll('.thumbnail');
    const modal = document.getElementById('imageModal');
    const modalContent = document.getElementById('modalContent');
    const closeModal = document.getElementById('closeModal');

    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function() {
            const shopId = this.dataset.shopId; // クリックしたサムネイルのshop_idを取得
            fetch(`show_images.php?shop_id=${shopId}`) // shop_idを渡して画像を取得
                .then(response => response.json()) // 画像のBase64エンコードされた配列を取得
                .then(images => {
                    // モーダル内のコンテンツをクリア
                    modalContent.innerHTML = '';

                    if (images.length > 0) {
                        // 画像を順にモーダルに追加
                        images.forEach(encodedImg => {
                            const imgElement = document.createElement('img');
                            imgElement.src = encodedImg; // Base64エンコードされた画像をセット
                            imgElement.alt = '商品画像';
                            modalContent.appendChild(imgElement); // モーダル内に画像を追加
                        });
                        modal.style.display = 'flex'; // モーダルを表示
                    } else {
                        modalContent.innerHTML = "画像が見つかりません"; // 画像がない場合
                        modal.style.display = 'flex'; // モーダルを表示
                    }
                })
                .catch(error => {
                    console.error("画像の取得に失敗しました:", error);
                });
        });
    });

    // モーダルを閉じる処理
    closeModal.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    // モーダルの外側をクリックすると閉じる
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
</script>