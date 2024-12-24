<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="add_goods.css">
    <title>商品追加フォーム</title>
    <style>
        .checkbox-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .checkbox-container label {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .checkbox-container input {
            margin: 0;
        }

        .delete-form-button {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #ff4d4d;
            color: white;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>
    <a href="admin_toppage.php">トップページ</a>
    <h1>商品追加フォーム</h1>
    <div class="form-container" id="form-container">
        <form method="post" action="add_goods_process.php" enctype="multipart/form-data" class="goods-form">
            
            <div class="single-form">

                <table id="goods-table">
                    <thead>
                        <tr>
                            <th class="thumbnail">サムネ</th>
                            <th class="thumbnail">サブimg</th>
                            <th class="goods_info">商品の説明</th>
                            <th class="group_sele">グループ指定</th>
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
                            <td>
                                <select name="group[]" class="group-select">
                                    <option value="">指定なし</option>
                                    <?php
                                    for ($i = 1; $i <= 50; $i++) {
                                        echo "<option value='{$i}'>{$i}</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- 他の商品の詳細情報を記入するためのフォーム -->
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
                                <!-- サイズの選択肢をチェックボックスで複数選択 -->
                                <div class="checkbox-container">
                                    <?php
                                    $size_sql = "SELECT size_id, size FROM size";
                                    foreach ($dbh->query($size_sql) as $size) {
                                        echo "<label><input type='checkbox' name='size[]' value='{$size['size_id']}'> {$size['size']}</label>";
                                    }
                                    ?>
                                </div>
                            </td>
                            <td>
                                <!-- 色の選択肢は単一選択 -->
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
        </form>
    </div>

    <div class="button-container">
        <button type="button" id="add-row">＋ 1行追加</button>
        <button type="button" onclick="submitForms()">追加</button>
    </div>

    <script>
        function addRow() {
            const formContainer = document.getElementById('form-container');
            const forms = document.querySelectorAll('.goods-form');
            const lastForm = forms[forms.length - 1];
            const newForm = lastForm.cloneNode(true);

            newForm.querySelectorAll('input, select, textarea').forEach((input) => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/\[\d+\]/, `[${forms.length}]`)); // 新しいインデックスを設定
                }
            });

            formContainer.appendChild(newForm);
        }

        document.getElementById('add-row').addEventListener('click', addRow);

        function submitForms() {
            const forms = document.querySelectorAll('.goods-form');
            let allFormsSubmitted = true;

            forms.forEach(function(form, index) {
                const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
                let allFieldsFilled = true;

                // 必須フィールドのチェック
                inputs.forEach(input => {
                    if (!input.value) {
                        allFieldsFilled = false;
                        input.style.border = '2px solid red'; // 未入力のフィールドを強調表示
                    } else {
                        input.style.border = ''; // 入力済みのフィールドの強調表示を解除
                    }
                });

                if (allFieldsFilled) {
                    const formData = new FormData(form);
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', 'add_goods_process.php', true);

                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            form.reset(); // フォームをリセット
                        } else {
                            console.error(`フォーム ${index + 1} の送信に失敗しました。`);
                            alert('送信に失敗しました。もう一度試してください。');
                            allFormsSubmitted = false;
                        }
                    };

                    xhr.send(formData);
                } else {
                    allFormsSubmitted = false;
                }
            });

            if (allFormsSubmitted) {
                alert('すべての商品が正常に追加されました。');
            } else {
                alert('必須項目をすべて入力してください。');
            }
        }
    </script>
</body>

</html>
