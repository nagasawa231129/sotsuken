<?php
include "../../db_open.php";
include "../head.php";
include "../header.php";
echo "<link rel='stylesheet' href='header.css'>";
echo "<link rel='stylesheet' href='category.css'>";
echo "<link rel='stylesheet' href='advance_search.css'>";

$sql = "SELECT * FROM brand";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$brands = $stmt->fetchAll();

?>

<form action="advanced_search.php" method="get">
    <!-- キーワード -->
    <label for="keyword" data-i18n="keyword_label"><?php echo $translations['Keyword Label'] ?></label>
    <input type="text" name="keyword" placeholder="<?php echo $translations['Keyword Placeholder'] ?>" value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>" data-i18n="keyword_placeholder" />

    <!-- 性別 -->
    <label for="gender" data-i18n="gender_label"><?php echo $translations['Gender'] ?>：</label>
    <select name="gender" data-i18n="gender_label">
        <option value="" data-i18n="all"><?php echo $translations['All'] ?></option>
        <option value="1" <?php echo (isset($_GET['gender']) && $_GET['gender'] === '1') ? 'selected' : ''; ?> data-i18n="man"><?php echo $translations['Man'] ?></option>
        <option value="0" <?php echo (isset($_GET['gender']) && $_GET['gender'] === '0') ? 'selected' : ''; ?> data-i18n="woman"><?php echo $translations['Woman'] ?></option>
    </select>

    <label for="brand" data-i18n="brand"><?php echo $translations['Brand'] ?>：</label>
    <select name="brand" id="brand">
        <option value=""><?php echo $translations['All'] ?></option> <!-- デフォルトで「すべて」選択肢を表示 -->
        <?php
        // $brands 配列を使ってブランドを表示
        foreach ($brands as $brand) {
            // ブランドIDと名前を表示
            $brand_id = htmlspecialchars($brand['brand_id']);
            $brand_name = htmlspecialchars($brand['brand_name']);

            $selected = (isset($_GET['brand']) && $_GET['brand'] === $brand_id) ? 'selected' : '';

            echo "<option value=\"$brand_id\" $selected>$brand_name</option>";
        }
        ?>
    </select>

    <!-- カテゴリー -->
    <label for="category" data-i18n="category_label"><?php echo $translations['Category Label'] ?>：</label>
    <select name="category" id="category" onchange="updateSubcategories()" data-i18n="category_label">
        <option value="" data-i18n="all"><?php echo $translations['All'] ?></option>
        <option value="1" <?php echo (isset($_GET['category']) && $_GET['category'] === '1') ? 'selected' : ''; ?> data-i18n="tops"><?php echo $translations['Tops'] ?></option>
        <option value="2" <?php echo (isset($_GET['category']) && $_GET['category'] === '2') ? 'selected' : ''; ?> data-i18n="outerwear"><?php echo $translations['Jacket'] ?></option>
        <option value="3" <?php echo (isset($_GET['category']) && $_GET['category'] === '3') ? 'selected' : ''; ?> data-i18n="pants"><?php echo $translations['Pants'] ?></option>
        <option value="4" <?php echo (isset($_GET['category']) && $_GET['category'] === '4') ? 'selected' : ''; ?> data-i18n="skirt"><?php echo $translations['Skirt'] ?></option>
        <option value="5" <?php echo (isset($_GET['category']) && $_GET['category'] === '5') ? 'selected' : ''; ?> data-i18n="onepiece"><?php echo $translations['Onepiece'] ?></option>
    </select>

    <!-- サブカテゴリー -->
    <div id="subcategory-container" style="display: none;">
        <label for="subcategory" data-i18n="subcategory_label"><?php echo $translations['Subcategory Label'] ?>：</label>
        <select name="subcategory" id="subcategory" data-i18n="subcategory_label">
            <!-- サブカテゴリーが動的に追加されます -->
        </select>
    </div>

    <!-- 価格帯 -->
    <label for="min_price" data-i18n="price_range"><?php echo $translations['Price Range'] ?>：</label>
    <input type="number" name="min_price" placeholder="<?php echo $translations['Min Price Placeholder'] ?>" value="<?php echo isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : ''; ?>" data-i18n="min_price_placeholder" />
    ～
    <input type="number" name="max_price" placeholder="<?php echo $translations['Max Price Placeholder'] ?>" value="<?php echo isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : ''; ?>" data-i18n="max_price_placeholder" />

    <!-- セール対象商品 -->
    <label for="sale_subject" data-i18n="sale_label"><?php echo $translations['Sale'] ?>：</label>
    <select name="sale_subject" data-i18n="sale_label">
        <option value="" data-i18n="all">すべて</option>
        <option value="1" <?php echo (isset($_GET['sale_subject']) && $_GET['sale_subject'] === '1') ? 'selected' : ''; ?> data-i18n="sale">セール対象</option>
        <option value="0" <?php echo (isset($_GET['sale_subject']) && $_GET['sale_subject'] === '0') ? 'selected' : ''; ?> data-i18n="no_sale">セールなし</option>
    </select>

    <input type="submit" value="検索" data-i18n="search_button" />
</form>



<script>
    // カテゴリーごとのサブカテゴリー
    const subcategories = {
        1: [{
                value: '1',
                text: '<?php echo $translations['Tshirt Cutsew'] ?>'
            }, // t-shirt, cutsew
            {
                value: '2',
                text: '<?php echo $translations['Shirt Blouse'] ?>'
            }, // shirt, blouse
            {
                value: '3',
                text: '<?php echo $translations['Polo Shirt'] ?>'
            }, // polo-shirt
            {
                value: '4',
                text: '<?php echo $translations['Knit Sweater'] ?>'
            }, // knit, sweater
            {
                value: '5',
                text: '<?php echo $translations['Vest'] ?>'
            }, // vest
            {
                value: '6',
                text: '<?php echo $translations['Parka'] ?>'
            }, // parka
            {
                value: '7',
                text: '<?php echo $translations['Sweat'] ?>'
            }, // sweat
            {
                value: '8',
                text: '<?php echo $translations['Cardigan'] ?>'
            }, // cardigan, bolero
            {
                value: '9',
                text: '<?php echo $translations['Ensemble'] ?>'
            }, // ensemble
            {
                value: '10',
                text: '<?php echo $translations['Jersey'] ?>'
            }, // jersey
            {
                value: '11',
                text: '<?php echo $translations['Tanktop'] ?>'
            }, // tanktop
            {
                value: '12',
                text: '<?php echo $translations['Camisole'] ?>'
            }, // camisole
            {
                value: '13',
                text: '<?php echo $translations['Tubetop'] ?>'
            }, // tubetop
            {
                value: '14',
                text: '<?php echo $translations['Other Tops'] ?>'
            }, // other-tops
        ],
        2: [{
                value: '15',
                text: '<?php echo $translations['Collarless Coat'] ?>'
            }, // collarless coat
            {
                value: '16',
                text: '<?php echo $translations['Collarless Jacket'] ?>'
            }, // collarless jacket
            {
                value: '17',
                text: '<?php echo $translations['Denim Jacket'] ?>'
            }, // denim jacket
            {
                value: '18',
                text: '<?php echo $translations['Down Jacket'] ?>'
            }, // down jacket
            {
                value: '19',
                text: '<?php echo $translations['Down Vest'] ?>'
            }, // down vest
            {
                value: '20',
                text: '<?php echo $translations['Duffle Coat'] ?>'
            }, // duffle coat
            {
                value: '21',
                text: '<?php echo $translations['Blouson'] ?>'
            }, // blouson
            {
                value: '22',
                text: '<?php echo $translations['Military Jacket'] ?>'
            }, // military jacket
            {
                value: '23',
                text: '<?php echo $translations['Mods Coat'] ?>'
            }, // mods coat
            {
                value: '24',
                text: '<?php echo $translations['Nylon Jacket'] ?>'
            }, // nylon jacket
            {
                value: '25',
                text: '<?php echo $translations['Riders Jacket'] ?>'
            }, // riders jacket
            {
                value: '26',
                text: '<?php echo $translations['Tailored Jacket'] ?>'
            }, // tailored jacket
            {
                value: '27',
                text: '<?php echo $translations['Trench Coat'] ?>'
            }, // trench coat
            {
                value: '28',
                text: '<?php echo $translations['Other Outerwear'] ?>'
            }, // other outerwear
        ],
        3: [{
                value: '29',
                text: '<?php echo $translations['Cargo Pants'] ?>'
            }, // cargo pants
            {
                value: '30',
                text: '<?php echo $translations['Chino Pants'] ?>'
            }, // chino pants
            {
                value: '31',
                text: '<?php echo $translations['Denim Pants'] ?>'
            }, // denim pants
            {
                value: '32',
                text: '<?php echo $translations['Slacks'] ?>'
            }, // slacks
            {
                value: '33',
                text: '<?php echo $translations['Sweat Pants'] ?>'
            }, // sweat pants
            {
                value: '34',
                text: '<?php echo $translations['Other Pants'] ?>'
            }, // other pants
        ],
        4: [{
                value: '35',
                text: '<?php echo $translations['Denim Skirt'] ?>'
            }, // denim skirt
            {
                value: '36',
                text: '<?php echo $translations['Mini Skirt'] ?>'
            }, // mini skirt
            {
                value: '37',
                text: '<?php echo $translations['Midi Skirt'] ?>'
            }, // midi skirt
            {
                value: '38',
                text: '<?php echo $translations['Long Skirt'] ?>'
            }, // long skirt
        ],
        5: [{
                value: '39',
                text: '<?php echo $translations['Dress'] ?>'
            }, //dress
            {
                value: '40',
                text: '<?php echo $translations['Jumper Skirt'] ?>'
            }, // jumper skirt
            {
                value: '41',
                text: '<?php echo $translations['Onepiece'] ?>'
            }, // onepiece dress
            {
                value: '42',
                text: '<?php echo $translations['Pants Dress'] ?>'
            }, // pants dress
            {
                value: '43',
                text: '<?php echo $translations['Shirt Onepiece'] ?>'
            }, // shirt dress
            {
                value: '44',
                text: '<?php echo $translations['Tunic'] ?>'
            }, // tunic
        ],
    };

    // カテゴリー変更時にサブカテゴリーを更新する関数
    function updateSubcategories() {
        const categorySelect = document.getElementById('category');
        const subcategoryContainer = document.getElementById('subcategory-container');
        const subcategorySelect = document.getElementById('subcategory');

        // 選択されたカテゴリーの値を取得
        const selectedCategory = categorySelect.value;

        // サブカテゴリーをクリア
        subcategorySelect.innerHTML = '';

        // サブカテゴリーを非表示にする（デフォルト）
        subcategoryContainer.style.display = 'none';

        // 選択されたカテゴリーに対応するサブカテゴリーを表示
        if (subcategories[selectedCategory]) {
            subcategories[selectedCategory].forEach((subcat) => {
                const option = document.createElement('option');
                option.value = subcat.value;
                option.textContent = subcat.text;
                subcategorySelect.appendChild(option);
            });

            // サブカテゴリーを表示
            subcategoryContainer.style.display = 'block';
        }
    }
</script>

<?php
// フィルタリング条件を取得
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : null;
$gender = isset($_GET['gender']) && $_GET['gender'] !== '' ? $_GET['gender'] : null;
$brand = isset($_GET['brand_name']) && $_GET['brand_name'] !== '' ? $_GET['brand_name'] : null;
$subcategory = isset($_GET['subcategory']) && $_GET['subcategory'] !== '' ? $_GET['subcategory'] : null;
$category = isset($_GET['category']) && $_GET['category'] !== '' ? $_GET['category'] : null;
$min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? $_GET['min_price'] : null;
$max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? $_GET['max_price'] : null;
$sale_subject = isset($_GET['sale_subject']) && $_GET['sale_subject'] !== '' ? $_GET['sale_subject'] : null;

// SQLクエリの作成
$sql = "
    SELECT DISTINCT shop.*, subcategory.*, category.*
    FROM shop
    LEFT OUTER JOIN subcategory ON subcategory.subcategory_id = shop.subcategory_id
    LEFT OUTER JOIN category ON category.category_id = shop.category_id
    WHERE 1 = 1
";

$params = [];

// キーワードフィルターの追加
if ($keyword !== null && $keyword !== '') {
    $sql .= " AND goods LIKE ?";
    $params[] = '%' . $keyword . '%';
}

// 性別フィルターの追加
if ($gender !== null) {
    $sql .= " AND gender = ?";
    $params[] = $gender;
}

//ブランド
if ($brand !== null) {
    $sql .= " AND brand_name = ?";
    $params[] = $brand;
}

// カテゴリーフィルターの追加
if ($category !== null) {
    $sql .= " AND category.category_id= ?";
    $params[] = $category;
}

// サブカテゴリーのフィルター追加
if ($subcategory !== null) {
    $sql .= " AND subcategory.subcategory_id = ?";
    $params[] = $subcategory;
}

// 価格帯フィルターの追加
if ($min_price !== null) {
    $sql .= " AND price >= ?";
    $params[] = $min_price;
}
if ($max_price !== null) {
    $sql .= " AND price <= ?";
    $params[] = $max_price;
}

// セール対象商品フィルターの追加
if ($sale_subject !== null) {
    $sql .= " AND sale_subject = ?";
    $params[] = $sale_subject;
}

// プリペアドステートメントでSQLを実行
$stmt = $dbh->prepare($sql);
$stmt->execute($params);


// 検索結果を取得
$results = $stmt->fetchAll();

// 検索結果がない場合の表示
if (empty($results)) {
    echo "該当する商品はありません。";
} else {
    // 検索結果の表示
    echo "<div class='sale-product-container'>";
    foreach ($results as $row) {
        $product_id = htmlspecialchars($row['shop_id']);
        $product_link = "goods.php?shop_id=" . $product_id;
    
        // 画像データの取得とBase64エンコード
        $imgBlob = $row['thumbnail'];
        $mimeType = 'image/png';  // デフォルトMIMEタイプ
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($imgBlob); // 実際のMIMEタイプを取得
        $encodedImg = base64_encode($imgBlob); // Base64エンコード
    
        // 商品表示
        echo "<div class='sale-product-item'>";
        echo "<a href=\"$product_link\" style=\"text-decoration: none; color: inherit;\">";
        echo "<img src='data:{$mimeType};base64,{$encodedImg}' alt='goods img' class='sale-product-image'></br>";
        echo  htmlspecialchars($row['goods']) . "<br>";
        echo  htmlspecialchars($row['price']) . "<br>";
    
        // 割引率の計算
        $sale_subject = $row['sale_subject'];
        if ($sale_subject >= 1 && $sale_subject <= 9) {
            $discount_percentage = $sale_subject * 10;
            echo  $discount_percentage . "%<br>";
        } else {
            echo " 0% (セールなし)<br>";
        }
    
        echo "</a>";
        echo "</div>";  // 商品アイテム終了
    }
    echo "</div>";  // 商品コンテナ終了
    
}

?>