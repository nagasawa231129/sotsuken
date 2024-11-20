<link rel="stylesheet" href="goods_info.css">

<?php
// データベース接続
include './../../db_open.php';
include 'function.php';
// すべての商品情報を取得
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

// フォーム送信が行われた場合、追加処理、更新処理または削除処理を実行
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 更新処理
    if (isset($_POST['update'])) {
        update();
    }
    // 削除処理
    if (isset($_POST['delete'])) {
       delete();
    }

    // 追加処理
    if (isset($_POST['add_new'])) {
       insert();
    }
}
?>
<button id="addNewButton" onclick="toggleAddForm()">新しい商品を追加</button>
<div id="addFormContainer" style="display:none; margin-top: 20px;">
    <form method="post" action="">
        <table>
            <tr>
                <td>ブランド</td>
                <td>
                    <select name="brand" required>
                        <?php
                        $brand_sql = "SELECT brand_id, brand_name FROM brand";
                        foreach ($dbh->query($brand_sql) as $brand_option) {
                            echo "<option value='{$brand_option['brand_id']}'>{$brand_option['brand_name']}</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>商品名</td>
                <td><input type="text" name="goods" required></td>
            </tr>
            <tr>
                <td>価格</td>
                <td><input type="number" name="price" required></td>
            </tr>
            <tr>
                <td>サイズ</td>
                <td>
                    <select name="size" required>
                        <?php
                        $size_sql = "SELECT size_id, size FROM size";
                        foreach ($dbh->query($size_sql) as $size_option) {
                            echo "<option value='{$size_option['size_id']}'>{$size_option['size']}</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>色</td>
                <td>
                    <select name="color" required>
                        <?php
                        $color_sql = "SELECT color_id, ja_color FROM color";
                        foreach ($dbh->query($color_sql) as $color_option) {
                            echo "<option value='{$color_option['color_id']}'>{$color_option['ja_color']}</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
    <td>カテゴリ</td>
    <td>
        <select name="category" id="category" onchange="updateSubcategory()" required>
            <option value="">カテゴリを選択</option>
            <?php
            $category_sql = "SELECT category_id, category_name FROM category";
            foreach ($dbh->query($category_sql) as $category_option) {
                echo "<option value='{$category_option['category_id']}'>{$category_option['category_name']}</option>";
            }
            ?>
        </select>
    </td>
</tr>

<tr>
    <td>サブカテゴリー</td>
    <td>
        <select name="subcategory" id="subcategory" required>
            <option value="">サブカテゴリーを選択</option>
        </select>
    </td>
</tr>

<script>
  
  function updateSubcategory() {
    const category_id = document.getElementById('category').value;
    const subcategorySelect = document.getElementById('subcategory');

    if (category_id) {
        // サーバーにリクエストを送信
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'get_subcategories.php?category_id=' + category_id, true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                const subcategories = JSON.parse(xhr.responseText);

                // サブカテゴリの選択肢を更新
                subcategorySelect.innerHTML = '<option value="">サブカテゴリを選択</option>'; // 初期化
                subcategories.forEach(function (subcategory) {
                    const option = document.createElement('option');
                    option.value = subcategory.subcategory_id;
                    option.textContent = subcategory.subcategory_name;
                    subcategorySelect.appendChild(option);
                });
            } else {
                console.error('サブカテゴリの取得に失敗しました');
            }
        };
        xhr.send();
    } else {
        // カテゴリ未選択の場合、サブカテゴリを初期化
        subcategorySelect.innerHTML = '<option value="">サブカテゴリを選択</option>';
    }
}




</script>
            <tr>
                <td>性別</td>
                <td>
                    <select name="gender" required>
                        <?php
                        $gender_sql = "SELECT gender_id, gender FROM gender";
                        foreach ($dbh->query($gender_sql) as $gender_option) {
                            echo "<option value='{$gender_option['gender_id']}'>{$gender_option['gender']}</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
        </table>
        <button type="submit" name="add_new">追加</button>
    </form>
</div>
<div class="form-container">
    <!-- 商品情報一覧 -->
    <table>
        <thead>
            <tr>
                <th>ブランド</th>
                <th>商品名</th>
                <th>価格</th>
                <th>セール</th>
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
                            <td>
                                <input type="hidden" name="shop_id" value="<?= $product['shop_id'] ?>">
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
                                <select name="sale" required>
                                    <?php
                                    $sale_sql = "SELECT sale_id,sale FROM sale";
                                    foreach ($dbh->query($sale_sql) as $sale_option) {
                                        $selected = ($product['sale_id'] == $size_option['sale_id']) ? 'selected' : '';
                                        echo "<option value='{$sale_option['sale_id']}' {$selected}>{$sale_option['sale']}%</option>";
                                    }
                                    ?>
                                </select>
                            </td>
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
                                <select name="category" required>
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
                                <select name="subcategory" required>
                                    <?php
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
                                <button type="submit" name="delete" onclick="return confirm('本当に削除しますか？')">削除</button>
                            </td>
                        </form>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9">商品情報が見つかりません。</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- 新規追加フォーム -->


</div>

<script>
    function toggleAddForm() {
        const form = document.getElementById('addFormContainer');
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }
</script>