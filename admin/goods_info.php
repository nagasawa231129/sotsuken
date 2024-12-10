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
            shop.exp,
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
</div>

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

            <div class="form-container">

                <table id="goods-table">
                    <thead>
                        <tr>
                            <th class="thumbnail">サムネ</th>
                            <th class="thumbnail">サブimg</th>
                            <th class="goods_info">商品の説明</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <?php
                                $imgBlob = $product['thumbnail']; // サムネイルのBLOBデータ
                                $shopId = $product['shop_id'];    // shop_idを取得
                                if ($imgBlob) {
                                    $encodedImg = base64_encode($imgBlob); // Base64エンコード
                                    // 画像をクリックするとサムネイル用のファイル選択ダイアログを開く
                                    echo "<img src='data:image/jpeg;base64,$encodedImg' alt='サムネイル' width='100' class='thumbnail' data-shop-id='$shopId' onclick='document.getElementById(\"thumbnailInput$shopId\").click();' id='shopImage$shopId' />";
                                    echo "<input type='file' id='thumbnailInput$shopId' style='display:none;' accept='image/jpeg, image/jpg, image/png' onchange='updateThumbnail($shopId)' />";
                                }
                                ?>
                            </td>
                            <td>
    <div class="input-group">
        <?php
        // 商品IDに基づいて、imageテーブルから画像を取得
        $shop_id = $product['shop_id']; // 現在の商品ID
        $img_sql = "SELECT img, image_id FROM image WHERE shop_id = :shop_id"; // 画像を取得するSQL（imageフィールドとimage_idを取得）
        $img_stmt = $dbh->prepare($img_sql);
        $img_stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
        $img_stmt->execute();

        // 画像が存在する場合、画像を表示
        if ($img_stmt->rowCount() > 0) {
            $img_data = $img_stmt->fetch(PDO::FETCH_ASSOC);
            $encodedImg = base64_encode($img_data['img']); // 画像データをBase64エンコード
            $imageId = $img_data['image_id']; // image_idを取得

            // 画像を表示し、クリックしたらファイル選択ダイアログを開く
            echo "<img src='data:image/jpeg;base64,$encodedImg' alt='サブサムネイル' width='100' class='subthumbnail' 
            data-shop-id='$shop_id' data-image-id='$imageId' id='shopImage$shop_id' onclick='triggerFileInput($shop_id)' />";
        }
        ?>

        <!-- 画像を選択するファイル入力（最初は非表示） -->
        <input type="file" id="imageInput<?php echo $shop_id; ?>" style="display:none;" accept="image/jpeg, image/jpg, image/png" onchange="updateImage(<?php echo $shop_id; ?>)" />
    </div>
</td>







                            <td>
                                <div class="input-group">
                                    <textarea name="goods_info[]" rows="4" cols="50" required><?= htmlspecialchars($product['exp']) ?></textarea>
                                </div>
                            </td>

                        </tr>
                    </tbody>
                </table>

                <table>
                    <thead>
                        <tr>
                            <th class="brand">ブランド</th>
                            <th class="goods">商品名</th>
                            <th>価格(円)</th>
                            <th>サイズ</th>
                            <th>色</th>
                            <th>カテゴリ</th>
                            <th>サブカテゴリ</th>
                            <th>性別</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
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
                                <select name="size[]" class="wide-select" required>
                                    <?php
                                    $size_sql = "SELECT size_id, size FROM size";
                                    foreach ($dbh->query($size_sql) as $size) {
                                        echo "<option value='{$size['size_id']}'>{$size['size']}</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <select name="color[]" class="wide-select" required>
                                    <?php
                                    $color_sql = "SELECT color_id, ja_color FROM color";
                                    foreach ($dbh->query($color_sql) as $color) {
                                        echo "<option value='{$color['color_id']}'>{$color['ja_color']}</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <select name="category[]" class="category" required onchange="updateSubcategory(this)">
                                    <?php
                                    $category_sql = "SELECT category_id, category_name FROM category";
                                    foreach ($dbh->query($category_sql) as $category) {
                                        echo "<option value='{$category['category_id']}'>{$category['category_name']}</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <select name="subcategory[]" class="subcategory" required>
                                    <?php
                                    $subcategory_sql = "SELECT subcategory_id, subcategory_name FROM subcategory";
                                    foreach ($dbh->query($subcategory_sql) as $subcategory) {
                                        echo "<option value='{$subcategory['subcategory_id']}'>{$subcategory['subcategory_name']}</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <select name="gender[]" required>
                                    <?php
                                    $gender_sql = "SELECT gender_id, gender FROM gender";
                                    foreach ($dbh->query($gender_sql) as $gender) {
                                        echo "<option value='{$gender['gender_id']}'>{$gender['gender']}</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            </div>


            <!-- 
                        <tr>


                            <td>
                                <button type="submit" name="update">更新</button>
                            </td>
                            <td>
                                <button type="submit" name="delete">削除</button>
                            </td> -->
        </form>
        <!-- <div id="imageModal" class="modal">
                        <div class="modal-content" id="modalContent"> -->
        <!-- ここに画像が追加されます -->
        <!-- </div>
                        <span id="closeModal" class="close">&times;</span>
                    </div> -->

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

<script src="goods_info.js"> </script>