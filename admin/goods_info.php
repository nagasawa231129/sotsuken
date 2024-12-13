<link rel="stylesheet" href="goods_info.css">

<a href="admin_toppage.php">戻る</a>

<head>
    <script>
        // ページのスクロール位置を保存
        window.addEventListener("beforeunload", () => {
            localStorage.setItem("scrollPosition", window.scrollY);
        });

        // ページ読み込み時にスクロール位置を復元
        window.addEventListener("load", () => {
            const scrollPosition = localStorage.getItem("scrollPosition");
            if (scrollPosition) {
                window.scrollTo(0, parseInt(scrollPosition));
            }
        });
    </script>
</head>

<body>
    <?php
    $scroll_position_flag = false;
    // データベース接続
    include './../../db_open.php';
    include './function.php';
    // 商品情報を更新する処理
    if (isset($_POST['update'])) {
        update();
        $scroll_position_flag = true; // 更新後のフラグ
    }


    // 商品情報を削除する処理
    if (isset($_POST['delete'])) {
        delete();
        $scroll_position_flag = true; // 削除後のフラグ
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
            shop.original_price,
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
        s_reset();
    }
    ?>

    <?php if ($stmt->rowCount() > 0): ?>
        <?php while ($product = $stmt->fetch(PDO::FETCH_ASSOC)): ?>

            <form method="post" action="" enctype="multipart/form-data"> <!-- enctypeを追加 -->

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
                                    $thumbimgBlob = $product['thumbnail'];
                                    $shopId = $product['shop_id'];
                                    if ($thumbimgBlob) {
                                        $thumbencodedImg = base64_encode($thumbimgBlob);
                                        echo "<img src='data:image/jpeg;base64,$thumbencodedImg' alt='サムネイル' width='100' class='thumbnail' data-shop-id='$shopId' onclick='document.getElementById(\"thumbnailInput$shopId\").click();' id='thumbnailImage$shopId' />";
                                        echo "<input type='file' id='thumbnailInput$shopId' style='display:none;' accept='image/jpeg, image/jpg, image/png' onchange='updateThumbnail($shopId)' />";
                                    }
                                    ?>
                                </td>

                                <td>
                                    <div class="input-group" style="position: relative;">
                                        <?php
                                        $shop_id = $product['shop_id'];
                                        $img_sql = "SELECT img, image_id FROM image WHERE shop_id = :shop_id";
                                        $img_stmt = $dbh->prepare($img_sql);
                                        $img_stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
                                        $img_stmt->execute();

                                        if ($img_stmt->rowCount() > 0) {
                                            while ($img_data = $img_stmt->fetch(PDO::FETCH_ASSOC)) {
                                                $subencodedImg = base64_encode($img_data['img']);
                                                $imageId = $img_data['image_id'];
                                                echo "<div class='image-container' style='position: relative; display: inline-block; margin-right: 10px;' id='imageContainer$imageId'>";
                                                echo "<img src='data:image/jpeg;base64,$subencodedImg' alt='サブサムネイル' width='100' class='subthumbnail' data-shop-id='$shop_id' data-image-id='$imageId' id='subthumbnailImage$imageId' />";
                                                echo "<button type='button' class='delete-button' onclick='deleteImage($shop_id, $imageId)'>×</button>";
                                                echo "</div>";
                                            }
                                        }

                                        ?>

                                        <div class="input-group">
                                            <input type="file" name="subthumbnail[]" accept="image/jpeg, image/jpg, image/png" multiple />
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <textarea name="goods_info" rows="4" cols="50" required><?= htmlspecialchars($product['exp']) ?></textarea>
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
                                <th class="price">価格(円)</th>
                                <th>サイズ</th>
                                <th>色</th>
                                <th class="category">カテゴリ</th>
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
                                <td class="price-select"><input type="number" name="price" value="<?= htmlspecialchars($product['original_price']) ?>" required></td>
                                <td>
                                    <select name="size" class="wide-select" required>
                                        <?php
                                        $size_sql = "SELECT size_id, size FROM size";
                                        foreach ($dbh->query($size_sql) as $size) {
                                            $selected = ($product['size_id'] == $size['size_id']) ? 'selected' : ''; // 選択されたサイズを設定
                                            echo "<option value='{$size['size_id']}' {$selected}>{$size['size']}</option>";
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td class="color">
                                    <select name="color" class="wide-select" required>
                                        <?php
                                        $color_sql = "SELECT color_id, ja_color FROM color";
                                        foreach ($dbh->query($color_sql) as $color) {
                                            $selected = ($product['color_id'] == $color['color_id']) ? 'selected' : ''; // 選択された色を設定
                                            echo "<option value='{$color['color_id']}' {$selected}>{$color['ja_color']}</option>";
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <select name="category" class="category" required onchange="updateSubcategory(this)">
                                        <?php
                                        $category_sql = "SELECT category_id, category_name FROM category";
                                        foreach ($dbh->query($category_sql) as $category) {
                                            $selected = ($product['category_id'] == $category['category_id']) ? 'selected' : ''; // 選択されたカテゴリを設定
                                            echo "<option value='{$category['category_id']}' {$selected}>{$category['category_name']}</option>";
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <select name="subcategory" class="subcategory" required>
                                        <?php
                                        $subcategory_sql = "SELECT subcategory_id, subcategory_name FROM subcategory";
                                        foreach ($dbh->query($subcategory_sql) as $subcategory) {
                                            $subselected = ($product['subcategory_id'] == $subcategory['subcategory_id']) ? 'selected' : ''; // 選択されたカテゴリを設定
                                            echo "<option value='{$subcategory['subcategory_id']}'{$subselected}>{$subcategory['subcategory_name']}</option>";
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <select name="gender" required>
                                        <?php
                                        $gender_sql = "SELECT gender_id, gender FROM gender";
                                        foreach ($dbh->query($gender_sql) as $gender) {
                                            $gselected = ($product['gender_id'] == $gender['gender_id']) ? 'selected' : ''; // 選択された色を設定
                                            echo "<option value='{$gender['gender_id']}'{$gselected}>{$gender['gender']}</option>";
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                </div>
                <div class="form-container">
                    <!-- 商品情報の入力フォーム (省略) -->
                    <div class="button-container">
                        <button type="submit" name="update">更新</button>
                    </div> <!-- 商品情報の入力フォーム (省略) -->
                    <div class="button-container">
                        <button type="submit" name="delete">削除</button>
                    </div>
                </div>

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

    <script src="goods_info.js">
        // ページを離れる前にスクロール位置を保存
        window.onbeforeunload = function() {
            localStorage.setItem("scrollPosition", window.scrollY); // 現在のスクロール位置を保存
        };

        // ページ読み込み時に保存したスクロール位置に戻る
        window.onload = function() {
            const savedPosition = localStorage.getItem("scrollPosition");
            if (savedPosition !== null) {
                window.scrollTo(0, parseInt(savedPosition, 10)); // 保存した位置にスクロール
            }
        };
    </script>
</body>