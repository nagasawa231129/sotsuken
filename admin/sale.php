<link rel="stylesheet" href="sale.css">

<?php
// データベース接続
include './../../db_open.php';

// 検索フォームからの値を取得
$search_query = '';
$search_params = [];

// 商品名の検索
if (isset($_GET['search']) && $_GET['search'] !== '') {
    $search_query .= "WHERE goods LIKE :search";
    $search_params[':search'] = '%' . $_GET['search'] . '%';
}

// ブランドで絞り込み
if (isset($_GET['brand_id']) && $_GET['brand_id'] !== '') {
    if ($search_query === '') {
        $search_query .= "WHERE brand_id = :brand_id";
    } else {
        $search_query .= " AND brand_id = :brand_id";
    }
    $search_params[':brand_id'] = $_GET['brand_id'];
}

// 価格で絞り込み
if (isset($_GET['min_price']) && $_GET['min_price'] !== '') {
    if ($search_query === '') {
        $search_query .= "WHERE price >= :min_price";
    } else {
        $search_query .= " AND price >= :min_price";
    }
    $search_params[':min_price'] = $_GET['min_price'];
}

if (isset($_GET['max_price']) && $_GET['max_price'] !== '') {
    if ($search_query === '') {
        $search_query .= "WHERE price <= :max_price";
    } else {
        $search_query .= " AND price <= :max_price";
    }
    $search_params[':max_price'] = $_GET['max_price'];
}

// 割引率で絞り込み
if (isset($_GET['sale_select']) && $_GET['sale_select'] !== '') {
    if ($search_query === '') {
        $search_query .= "WHERE sale_id = :sale_select";
    } else {
        $search_query .= " AND sale_id = :sale_select";
    }
    $search_params[':sale_select'] = $_GET['sale_select'];
}


// 商品検索結果の取得
$stmt = $dbh->prepare("SELECT shop_id, goods,price, original_price, size, color, brand_id, category_id, subcategory_id,sale_id, gender FROM shop $search_query");
$stmt->execute($search_params);

// POSTされた割引IDと選択された商品
$sale_id = $_POST['sale_id'] ?? null;
$selected_items = $_POST['selected_items'] ?? []; // 選択された商品（配列）
$sale_percentage = 0;

// 割引率を取得（POST送信された場合）
if ($sale_id) {
    $sale_stmt = $dbh->prepare("SELECT sale FROM sale WHERE sale_id = :sale_id");
    $sale_stmt->bindValue(':sale_id', $sale_id, PDO::PARAM_INT);
    $sale_stmt->execute();
    $sale = $sale_stmt->fetch(PDO::FETCH_ASSOC);
    if ($sale) {
        $sale_percentage = $sale['sale'];
    }
}




if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_discount'])) {
    // 割引を適用する
    // $selected_items = $_POST['selected_items'] ?? [];

    // // デバッグ表示
    // echo '<pre>';
    // print_r($selected_items);
    // echo '</pre>';
    if (!empty($selected_items)) {

        foreach ($selected_items as $shop_id) {
            // 割引前の価格を取得
            $get_price_stmt = $dbh->prepare("SELECT original_price FROM shop WHERE shop_id = :shop_id");
            $get_price_stmt->bindValue(':shop_id', $shop_id, PDO::PARAM_INT);
            $get_price_stmt->execute();
            $price_data = $get_price_stmt->fetch(PDO::FETCH_ASSOC);
            $original_price = $price_data['original_price'];

            // 割引後の価格を計算
            $discounted_price = $original_price * (1 - $sale_percentage / 100);

            // sale_idを取得（$_POST['sale_id']から）
            $sale_id = $_POST['sale_id'];  // 割引率を適用するsale_idを指定

            // `price`に割引後の価格を、`discount_price`に元の価格を設定、`sale_id`も更新
            $update_stmt = $dbh->prepare("UPDATE shop 
                                          SET price = :discounted_price, 
                                        sale_id = :sale_id 
                                          WHERE shop_id = :shop_id");
            $update_stmt->bindValue(':shop_id', $shop_id, PDO::PARAM_INT);
            $update_stmt->bindValue(':discounted_price', $discounted_price, PDO::PARAM_STR);
            $update_stmt->bindValue(':sale_id', $sale_id, PDO::PARAM_INT);  // sale_idの更新
            $update_stmt->execute();
        }
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit();
    }
}
?>


<!-- 検索フォーム -->
<form id="search-form" method="GET">
    <!-- 商品名で検索 -->
    <input type="text" name="search" placeholder="商品名で検索" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">

    <!-- ブランドで絞り込み -->
    <select name="brand_id">
        <option value="">ブランド選択</option>
        <?php
        $brands = $dbh->query("SELECT * FROM brand");
        while ($brand = $brands->fetch(PDO::FETCH_ASSOC)) {
            // 現在選択されているブランドをチェック
            $selected = isset($_GET['brand_id']) && $_GET['brand_id'] == $brand['brand_id'] ? ' selected' : '';
            echo "<option value='{$brand['brand_id']}'{$selected}>{$brand['brand_name']}</option>";
        }
        ?>
    </select>

    <!-- 価格で絞り込み -->
    <input type="number" name="min_price" placeholder="最小価格" value="<?php echo htmlspecialchars($_GET['min_price'] ?? ''); ?>">
    <input type="number" name="max_price" placeholder="最大価格" value="<?php echo htmlspecialchars($_GET['max_price'] ?? ''); ?>">

    <!-- 割引で絞り込み -->
    <select name="sale_select" id="sale_select">
        <option value="">全ての商品</option>
        <?php
        $sale_select = $dbh->query("SELECT * FROM sale");
        $counter = 0;

        while ($sale_sele = $sale_select->fetch(PDO::FETCH_ASSOC)) {
            $counter++;
            if ($counter === 10) {
                // 10個目の値を「割引なし」として出力
                $selected = isset($_GET['sale_select']) && $_GET['sale_select'] == $sale_sele['sale_id'] ? ' selected' : '';
                echo "<option value='{$sale_sele['sale_id']}'{$selected}>割引なし</option>";
            } else {
                $selected = isset($_GET['sale_select']) && $_GET['sale_select'] == $sale_sele['sale_id'] ? ' selected' : '';
                echo "<option value='{$sale_sele['sale_id']}'{$selected}>{$sale_sele['sale']}%割引商品</option>";
            }
        }
        ?>
    </select>

    <!-- 検索ボタン -->
    <button type="submit">検索</button>

    <!-- すべて表示ボタン -->
    <a href="sale.php" class="button">すべて表示</a>
</form>

<!-- 商品リストの表示 -->
<h3>商品一覧</h3>

<form method="POST">
    <table>
        <thead>
            <tr>
                <th><input type="checkbox" id="select-all"> すべて選択</th>
                <th>ブランド</th>
                <th>商品名</th>
                <th>価格(円)</th> <!-- 割引後の価格 -->
                <th>サイズ</th>
                <th>色</th>
                <th>カテゴリ</th>
                <th>サブカテゴリー</th>
                <th>性別</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($stmt->rowCount() > 0) { ?>
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $original_price = $row['price'];
                    $discounted_price = $original_price;

                    // 割引を適用
                    if (in_array($row['shop_id'], $selected_items) && $sale_percentage > 0) {
                        // 割引を計算
                        $discounted_price = $original_price * (1 - $sale_percentage / 100);
                    }
                ?>
                    <tr>
                        <td><input type="checkbox" name="selected_items[]" value="<?php echo $row['shop_id']; ?>" <?php echo in_array($row['shop_id'], $selected_items) ? 'checked' : ''; ?>></td>
                        <td>
                            <?php
                            // ブランド名の取得
                            $brand_stmt = $dbh->prepare("SELECT brand_name FROM brand WHERE brand_id = :brand_id");
                            $brand_stmt->bindValue(':brand_id', $row['brand_id'], PDO::PARAM_INT);
                            $brand_stmt->execute();
                            $brand = $brand_stmt->fetch(PDO::FETCH_ASSOC);
                            echo htmlspecialchars($brand['brand_name']);
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['goods']); ?></td>
                        <td>
                            <?php
                            echo number_format($discounted_price) . '円';

                            // sale_idに基づいて割引パーセンテージを表示
                            if ($row['sale_id'] != null) {
                                switch ($row['sale_id']) {
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
                                    case 8:
                                        echo ' <span style="color: red;">80%OFF中</span>';
                                        break;
                                    case 9:
                                        echo ' <span style="color: red;">90%OFF中</span>';
                                        break;
                                    default:
                                        // 他のsale_idの場合は表示しない
                                        break;
                                }
                            }
                            ?>
                        </td>



                        <td>
                            <?php
                            // サイズ名の取得
                            $size_stmt = $dbh->prepare("SELECT size FROM size WHERE size_id = :size_id");
                            $size_stmt->bindValue(':size_id', $row['size'], PDO::PARAM_INT);
                            $size_stmt->execute();
                            $size = $size_stmt->fetch(PDO::FETCH_ASSOC);
                            echo htmlspecialchars($size['size']);
                            ?>
                        </td>
                        <td>
                            <?php
                            // 色名の取得
                            $color_stmt = $dbh->prepare("SELECT color FROM color WHERE color_id = :color_id");
                            $color_stmt->bindValue(':color_id', $row['color'], PDO::PARAM_INT);
                            $color_stmt->execute();
                            $color = $color_stmt->fetch(PDO::FETCH_ASSOC);
                            echo htmlspecialchars($color['color']);
                            ?>
                        </td>
                        <td>
                            <?php
                            // カテゴリ名の取得
                            $category_stmt = $dbh->prepare("SELECT category_name FROM category WHERE category_id = :category_id");
                            $category_stmt->bindValue(':category_id', $row['category_id'], PDO::PARAM_INT);
                            $category_stmt->execute();
                            $category = $category_stmt->fetch(PDO::FETCH_ASSOC);
                            echo htmlspecialchars($category['category_name']);
                            ?>
                        </td>
                        <td>
                            <?php
                            // サブカテゴリ名の取得
                            $subcategory_stmt = $dbh->prepare("SELECT subcategory_name FROM subcategory WHERE subcategory_id = :subcategory_id");
                            $subcategory_stmt->bindValue(':subcategory_id', $row['subcategory_id'], PDO::PARAM_INT);
                            $subcategory_stmt->execute();
                            $subcategory = $subcategory_stmt->fetch(PDO::FETCH_ASSOC);
                            echo htmlspecialchars($subcategory['subcategory_name']);
                            ?>
                        </td>
                        <td>
                            <?php
                            // 性別の取得
                            $ge_stmt = $dbh->prepare("SELECT gender FROM gender WHERE gender_id = :ge_id");
                            $ge_stmt->bindValue(':ge_id', $row['gender'], PDO::PARAM_INT);
                            $ge_stmt->execute();
                            $ge = $ge_stmt->fetch(PDO::FETCH_ASSOC);
                            echo htmlspecialchars($ge['gender']);

                            ?>
                        </td>
                    </tr>

                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="9" style="text-align:center;">検索に該当する商品はございません。</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>



    <label for="sale_id">割引率を選択:</label>
    <select name="sale_id" id="sale_id">
        <option value="">割引なし</option>
        <?php
        // 割引リストを取得
        $sale_stmt = $dbh->query("SELECT * FROM sale");
        while ($sale_row = $sale_stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<option value='{$sale_row['sale_id']}'" . ($sale_row['sale_id'] == $sale_id ? ' selected' : '') . ">{$sale_row['sale']}%</option>";
        }
        ?>
    </select>
    <button type="submit" name="apply_discount">割引適用</button>
</form>
<script>
    // 割引適用ボタンと割引解除ボタンの送信前にチェックボックスが選択されているかを確認


    // すべて選択のチェックボックス
    document.getElementById("select-all").addEventListener("click", function() {
        var checkboxes = document.querySelectorAll('input[name="selected_items[]"]');
        for (var checkbox of checkboxes) {
            checkbox.checked = this.checked;
        }
    });
</script>