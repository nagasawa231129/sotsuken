<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="add_goods.css">
    <title>商品追加フォーム</title>
</head>

<body>
    <a href="admin_toppage.php">トップページ</a>
    <h1>商品追加フォーム</h1>
    <form method="post" action="add_goods_process.php" enctype="multipart/form-data" id="goods-form">
        <div class="form-container">
            <div class="single-form">
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
                                <div class="input-group">
                                    <input type="file" name="thumbnail[]" accept="image/jpeg, image/jpg, image/png" required>
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="file" name="subthumbnail[]" accept="image/jpeg, image/jpg, image/png" required multiple>
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <textarea name="goods_info[]" rows="4" cols="50" required></textarea>
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
                                <select name="brand[]" class="brand-select" required>
                                    <?php
                                    include './../../db_open.php';
                                    $brand_sql = "SELECT brand_id, brand_name FROM brand";
                                    foreach ($dbh->query($brand_sql) as $brand) {
                                        echo "<option value='{$brand['brand_id']}'>{$brand['brand_name']}</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                            <td><input type="text" name="goods[]" required></td>
                            <td><input type="number" name="price[]" required></td>
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

        <div class="button-container">
            <button type="button" id="add-row">＋ 1行追加</button>
            <button type="submit">追加</button>
        </div>
    </form>

    <script>
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
        // フォームを1つ追加
        function addForms() {
            const formContainer = document.querySelector('.form-container');
            const firstForm = formContainer.querySelector('.single-form'); // 最初のフォームを取得
            const newForm = firstForm.cloneNode(true); // フォームをコピー

            // 新しいフォームの入力フィールドをクリア
            newForm.querySelectorAll('input').forEach(input => input.value = '');
            newForm.querySelectorAll('textarea').forEach(textarea => textarea.value = '');
            newForm.querySelectorAll('select').forEach(select => select.selectedIndex = 0);

            formContainer.appendChild(newForm); // 新しいフォームを追加
        }

        // ボタンにイベントリスナーを追加
        document.getElementById('add-row').addEventListener('click', addForms);
    </script>
</body>

</html>
