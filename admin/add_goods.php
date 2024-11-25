<!DOCTYPE html>
<html lang="ja">

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="add_goods.css">
<title>商品追加フォーム</title>
<a href="admin_toppage.php">トップページ</a>
<body>
    <h1>商品追加フォーム</h1>
    <form method="post" action="add_goods_process.php">
        <table id="goods-table">
            <thead>
                <tr>
                    <th>ブランド</th>
                    <th>商品名</th>
                    <th>価格</th>
                    <th>サイズ</th>
                    <th>色</th>
                    <th>カテゴリ</th>
                    <th>サブカテゴリ</th>
                    <th>性別</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <select name="brand[]" required>
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
                        <select name="size[]" required>
                            <?php
                            $size_sql = "SELECT size_id, size FROM size";
                            foreach ($dbh->query($size_sql) as $size) {
                                echo "<option value='{$size['size_id']}'>{$size['size']}</option>";
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <select name="color[]" required>
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
                    <td><button type="button" class="delete-row" onclick="deleteRow(this)">削除</button></td>
                </tr>
            </tbody>
        </table>
        <div class="button-container">
            <button type="button" id="add-row">＋ 1行追加</button>
            <button type="button" id="add-5-rows">＋ 5行追加</button>
            <button type="button" id="add-10-rows">＋ 10行追加</button>
            <button type="submit">追加</button>
        </div>
    </form>

    <script>
        // 行を削除する
        function deleteRow(button) {
            const row = button.closest('tr');
            const tbody = row.closest('tbody');
            if (tbody.children.length > 1) { // 最低1行を残す
                row.remove();
            } else {
                alert("最低1行必要です。");
            }
        }

        // 行を追加する関数
        function addRows(count) {
            const tableBody = document.querySelector('#goods-table tbody');
            const firstRow = tableBody.querySelector('tr');

            for (let i = 0; i < count; i++) {
                const newRow = firstRow.cloneNode(true);

                // 新しい行の入力フィールドをクリア
                newRow.querySelectorAll('input').forEach(input => input.value = '');
                newRow.querySelectorAll('select').forEach(select => select.selectedIndex = 0);

                tableBody.appendChild(newRow);
            }
        }

        // ボタンイベントの設定
        document.getElementById('add-row').addEventListener('click', function() {
            addRows(1);
        });

        document.getElementById('add-5-rows').addEventListener('click', function() {
            addRows(5);
        });

        document.getElementById('add-10-rows').addEventListener('click', function() {
            addRows(10);
        });

        // カテゴリ変更時にサブカテゴリを更新
        function updateSubcategory(categoryElement) {
            const categoryId = categoryElement.value;
            const subcategorySelect = categoryElement.closest('tr').querySelector('.subcategory');

            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_subcategories.php?category_id=' + categoryId, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    subcategorySelect.innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }
    </script>
</body>

</html>
