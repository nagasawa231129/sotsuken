<link rel="stylesheet" href="goods_info.css">

<?php
// データベース接続
include './../../db_open.php';

// フォームが送信された場合
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // フォームから送信されたデータを受け取る
    $shop_id = $_POST['shop_id'];
    $brand_id = $_POST['brand'];
    $goods = $_POST['goods'];
    $price = $_POST['price'];
    $size_id = $_POST['size'];
    $color_id = $_POST['color'];
    $category_id = $_POST['category'];
    $subcategory_id = $_POST['subcategory'];
    $gender_id = $_POST['gender'];

    if (isset($_POST['update'])) {
        // 更新SQL文
        $update_sql = "
        UPDATE shop SET 
            brand_id = :brand_id,
            goods = :goods,
            price = :price,
            size = :size_id,
            color = :color_id,
            category_id = :category_id,
            subcategory_id = :subcategory_id,
            gender = :gender_id
        WHERE shop_id = :shop_id
    ";

        // 更新クエリの準備
        $stmt = $dbh->prepare($update_sql);
        $stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
        $stmt->bindParam(':brand_id', $brand_id, PDO::PARAM_INT);
        $stmt->bindParam(':goods', $goods, PDO::PARAM_STR);
        $stmt->bindParam(':price', $price, PDO::PARAM_INT);
        $stmt->bindParam(':size_id', $size_id, PDO::PARAM_INT);
        $stmt->bindParam(':color_id', $color_id, PDO::PARAM_INT);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->bindParam(':subcategory_id', $subcategory_id, PDO::PARAM_INT);
        $stmt->bindParam(':gender_id', $gender_id, PDO::PARAM_INT);

        // 更新を実行
        if ($stmt->execute()) {
            echo "商品情報が更新されました。";
        } else {
            echo "更新に失敗しました。";
        }
    }
    if (isset($_POST['delete'])) {
        // 削除対象のshop_idを取得
        $shop_id = $_POST['shop_id'];

        // 削除SQL文
        $delete_sql = "DELETE FROM shop WHERE shop_id = :shop_id";

        // SQLを準備して実行
        $stmt = $dbh->prepare($delete_sql);
        $stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);

        // 実行して結果を確認
        if ($stmt->execute()) {
            echo "商品情報が削除されました。";
        } else {
            echo "削除に失敗しました。";
        }
    }
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
            shop.subcategory_id AS subcategory_id
        FROM shop
        LEFT JOIN brand ON shop.brand_id = brand.brand_id  
        LEFT JOIN color ON shop.color = color.color_id  
        LEFT JOIN category ON shop.category_id = category.category_id  
        LEFT JOIN gender ON shop.gender = gender.gender_id
        LEFT JOIN subcategory ON shop.subcategory_id = subcategory.subcategory_id
        LEFT JOIN size ON shop.size = size.size_id";

$stmt = $dbh->query($sql);
?>

<div class="form-container">
    <table>
        <thead>
            <tr>
                <th>ブランド</th>
                <th>商品名</th>
                <th>価格</th>
                <th>サイズ</th>
                <th>色</th>
                <th>カテゴリ</th>
                <th>サブカテゴリー</th>
                <th>性別</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($stmt->rowCount() > 0): ?>
                <?php while ($product = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <form method="post" action="">
                            <input type="hidden" name="shop_id" value="<?= htmlspecialchars($product['shop_id']) ?>">
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
                            <td><input type="number" name="price" value="<?= htmlspecialchars($product['price']) ?>" required></td>
                            <td>
                                <select name="size" required>
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
                                <button type="button" onclick="confirmDelete(this.form)">削除</button>
                            </td>

                        </form>
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

    function confirmDelete(form) {
    // 確認ダイアログを表示
    if (confirm("本当に削除しますか？")) {
        
    } else {
        // キャンセルの場合は何もしない
        console.log("削除がキャンセルされました。");
    }
}
</script>これにかいて