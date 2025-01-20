<?php
include "../../../../db_open.php"; // PDO接続のファイルをインクルード
include "../../../head.php";
include "../../../header.php";
echo "<link rel='stylesheet' href='../../header.css'>";

echo "<link rel='stylesheet' href='../tops.css'>";

$gender = isset($_GET['gender']) ? $_GET['gender'] : 'ALL';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'default';
$brand = isset($_GET['brand']) && $_GET['brand'] !== '' ? $_GET['brand'] : null;

// 初期化
$params = [];
$sql = "SELECT shop.*, brand.*, sale.*, `group`.shop_group
        FROM shop
        LEFT OUTER JOIN subcategory ON shop.subcategory_id = subcategory.subcategory_id
        LEFT OUTER JOIN brand ON shop.brand_id = brand.brand_id
        LEFT OUTER JOIN sale ON shop.sale_id = sale.sale_id
        LEFT OUTER JOIN gender ON gender.gender_id = shop.gender
        LEFT OUTER JOIN `group` ON `group`.shop_id = shop.shop_id
        WHERE subcategory.subcategory_name =  'ダウンベスト'";

// gender が ALL でない場合、shop.gender が指定された値または 0 の両方を表示
if ($gender == '0') {
    $sql .= " AND (shop.gender IN (0, 1, 2, 3))";
} elseif ($gender !== 'ALL') {
    // gender が ALL でない場合（1, 2, 3）のみフィルタリング
    $sql .= " AND shop.gender IN (0,:gender)";
    $params[':gender'] = $gender;
}

// ブランドフィルタがある場合
if ($brand !== null) {
    $sql .= " AND shop.brand_id = :brand";
    $params[':brand'] = $brand;
}

// ソート条件に応じてクエリを追加
switch ($sort) {
    case 'price_asc':
        $sql .= " ORDER BY shop.price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY shop.price DESC";
        break;
    case 'new_arrivals':
        $sql .= " ORDER BY shop.arrival DESC";
        break;
    case 'favorite':
        $sql .= " ORDER BY shop.buy DESC";
        break;
    default:
        $sql .= " ORDER BY shop.sale_id DESC";
        break;
}

// クエリ実行
try {
    $stmt = $dbh->prepare($sql);
    $stmt->execute($params);

    // 商品データの取得
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}

// ブランド一覧の取得
$sql = "SELECT * FROM brand";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$brands = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT * FROM gender";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$genders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>デニムスカート商品一覧</title>
</head>

<body>
    <div class="main-content">
        <aside class="sidebar">
            <h2 data-i18n="search"><?php echo $translations['Search'] ?></h2>
        <ul>
                <li><a href="../../brand.php" data-i18n="Search_By_brand"><?php echo $translations['Search By Brand'] ?></a></li>
                <li><a href="../category.php?gender=ALL" data-i18n="Search_By_category"><?php echo $translations['Search By Category'] ?></a></li>
                <li><a href="../../ranking.php" data-i18n="Search_By_ranking"><?php echo $translations['Search By Ranking'] ?></a></li>
                <li><a href="../../sale.php" data-i18n="Search_By_sale"><?php echo $translations['Search By Sale'] ?></a></li>
                <li><a href="../../diagnosis.php" data-i18n="Search_By_diagnosis"><?php echo $translations['Search By Diagnosis'] ?></a></li>
                <li><a href="../../advanced_search.php" data-i18n="advanced_search"><?php echo $translations['Advanced Search'] ?></a></li>
            </ul>

            <h2 data-i18n="categories_from"><?php echo $translations['Search By Category'] ?></h2>

            <ul class="category-list">
            <li class="category-item">
                <a href="../tops.php" data-i18n="tops"><?php echo $translations['Tops'] ?></a>
                <ul class="sub-category">
                    <li><a href="../tops/tshirt-cutsew.php" data-i18n="Tshirt-cutsew"><?php echo $translations['Tshirt Cutsew'] ?></a></li>
                    <li><a href="../tops/shirt.php" data-i18n="shirt-blouse"><?php echo $translations['Shirt Blouse'] ?></a></li>
                    <li><a href="../tops/poloshirt.php" data-i18n="poloshirt"><?php echo $translations['Polo Shirt'] ?></a></li>
                    <li><a href="../tops/knit-sweater.php" data-i18n="knit/sweater"><?php echo $translations['Knit Sweater'] ?></a></li>
                    <li><a href="../tops/vest.php" data-i18n="vast"><?php echo $translations['Vest'] ?></a></li>
                    <li><a href="../tops/parka.php" data-i18n="parka"><?php echo $translations['Parka'] ?></a></li>
                    <li><a href="../tops/sweat.php" data-i18n="sweat"><?php echo $translations['Sweat'] ?></a></li>
                    <li><a href="../tops/cardigan.php" data-i18n="cardigan"><?php echo $translations['Cardigan'] ?></a></li>
                    <li><a href="../tops/ensemble.php" data-i18n="ensemble"><?php echo $translations['Ensemble'] ?></a></li>
                    <li><a href="../tops/jersey.php" data-i18n="jersey"><?php echo $translations['Jersey'] ?></a></li>
                    <li><a href="../tops/tanktop.php" data-i18n="tanktop"><?php echo $translations['Tanktop'] ?></a></li>
                    <li><a href="../tops/camisole.php" data-i18n="camisole"><?php echo $translations['Camisole'] ?></a></li>
                    <li><a href="../tops/tubetop.php" data-i18n="tubetops"><?php echo $translations['Tubetop'] ?></a></li>
                    <li><a href="../tops/other-tops.php" data-i18n="other-tops"><?php echo $translations['Other Tops'] ?></a></li>
                </ul>
            </li>
                <li class="category-item">
                    <a href="../jacket-outerwear.php" data-i18n="jacket/outer"><?php echo $translations['Outerwear'] ?></a>
                    <ul class="sub-category">
                        <li><a href="collarless-coat.php" data-i18n="collarless-coat"><?php echo $translations['Collarless Coat'] ?></a></li>
                        <li><a href="collarless-jacket.php" data-i18n="collarless-jacket"><?php echo $translations['Collarless Jacket'] ?></a></li>
                        <li><a href="denim-jacket.php" data-i18n="denim-jacket"><?php echo $translations['Denim Jacket'] ?></a></li>
                        <li><a href="down-jacket.php" data-i18n="down-jacket"><?php echo $translations['Down Jacket'] ?></a></li>
                        <li><a href="down-vest.php" data-i18n="down-vest"><?php echo $translations['Down Vest'] ?></a></li>
                        <li><a href="duffle-coat.php" data-i18n="duffle-coat"><?php echo $translations['Duffle Coat'] ?></a></li>
                        <li><a href="jacket.php" data-i18n="jacket"><?php echo $translations['Blouson'] ?></a></li>
                        <li><a href="military-jacket.php" data-i18n="millitary-jacket"><?php echo $translations['Military Jacket'] ?></a></li>
                        <li><a href="mods-coat.php" data-i18n="mods-coat"><?php echo $translations['Mods Coat'] ?></a></li>
                        <li><a href="nylon-jacket.php" data-i18n="nylon-jacket"><?php echo $translations['Nylon Jacket'] ?></a></li>
                        <li><a href="riders-jacket.php" data-i18n="riders-jacket"><?php echo $translations['Riders Jacket'] ?></a></li>
                        <li><a href="tailored-jacket.php" data-i18n="tailored-jacket"><?php echo $translations['Tailored Jacket'] ?></a></li>
                        <li><a href="trench-coat.php" data-i18n="trench-coat"><?php echo $translations['Trench Coat'] ?></a></li>
                        <li><a href="other-jacket.php" data-i18n="other-jacket"><?php echo $translations['Other Outerwear'] ?></a></li>
                    </ul>
                </li>
                <li class="category-item">
                    <a href="../pants.php" data-i18n="pants"><?php echo $translations['Pants'] ?></a>
                    <ul class="sub-category">
                        <li><a href="../pants/cargo-pants.php" data-i18n="cargo-pants"><?php echo $translations['Cargo Pants'] ?></a></li>
                        <li><a href="../pants/chino-pants.php" data-i18n="chino-pants"><?php echo $translations['Chino Pants'] ?></a></li>
                        <li><a href="../pants/denim-pants.php" data-i18n="denim-pants"><?php echo $translations['Denim Pants'] ?></a></li>
                        <li><a href="../pants/slacks.php" data-i18n="slacks"><?php echo $translations['Slacks'] ?></a></li>
                        <li><a href="../pants/sweat-pants.php" data-i18n="sweat-pants"><?php echo $translations['Sweat Pants'] ?></a></li>
                        <li><a href="../pants/other-pants.php" data-i18n="other-pants"><?php echo $translations['Other Pants'] ?></a></li>
                    </ul>
                </li>
                <li class="category-item">
                    <a href="../skirt.php" data-i18n="skirt"><?php echo $translations['Skirt'] ?></a>
                    <ul class="sub-category">
                        <li><a href="../skirt/mini-skirt.php" data-i18n="mini-skirt"><?php echo $translations['Mini Skirt'] ?></a></li>
                        <li><a href="../skirt/midi-skirt.php" data-i18n="midi-skirt"><?php echo $translations['Midi Skirt'] ?></a></li>
                        <li><a href="../skirt/long-skirt.php" data-i18n="long-skirt"><?php echo $translations['Long Skirt'] ?></a></li>
                        <li><a href="../skirt/denim-skirt.php" data-i18n="denim-skirt"><?php echo $translations['Denim Skirt'] ?></a></li>
                    </ul>
                </li>
                <li class="category-item">
                    <a href="../onepiece.php" data-i18n="onepiece"><?php echo $translations['Onepiece'] ?></a>
                    <ul class="sub-category">
                        <li><a href="../onepiece/dress.php" data-i18n="dress"><?php echo $translations['Dress'] ?></a></li>
                        <li><a href="../onepiece/jumper-skirt.php" data-i18n="jumper-skirt"><?php echo $translations['Jumper Skirt'] ?></a></li>
                        <li><a href="../onepiece/onepiece-dress.php" data-i18n="onepiece-dress"><?php echo $translations['Onepiece'] ?></a></li>
                        <li><a href="../onepiece/pants-dress.php" data-i18n="pants-dress"><?php echo $translations['Pants Dress'] ?></a></li>
                        <li><a href="../onepiece/shirts-onepiece.php" data-i18n="shirts-onepiece"><?php echo $translations['Shirt Onepiece'] ?></a></li>
                        <li><a href="../onepiece/tunic.php" data-i18n="tunic"><?php echo $translations['Tunic'] ?></a></li>
                    </ul>
                </li>
            </ul>
        </aside>
        <div class="products-section">

            <form method="get" class="sort-form">
                <select name="sort" id="sort" onchange="this.form.submit()">
                    <option value="sale" <?php echo $sort === 'sale' ? 'selected' : ''; ?>><?php echo $translations['Recommendation'] ?></option>
                    <option value="favorite" <?php echo $sort === 'favorite' ? 'selected' : ''; ?>><?php echo $translations['Popular'] ?></option>
                    <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>><?php echo $translations['Cheap'] ?></option>
                    <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>><?php echo $translations['Expensive'] ?></option>
                    <option value="new_arrivals" <?php echo $sort === 'new_arrivals' ? 'selected' : ''; ?>><?php echo $translations['New Items'] ?></option>
                </select>

                <select name="brand" id="brand" onchange="this.form.submit()">
                    <option value=""><?php echo $translations['All'] ?></option> <!-- デフォルトで「すべて」選択肢を表示 -->
                    <?php
                    // ブランドを表示
                    foreach ($brands as $brand_option) {
                        $brand_id = htmlspecialchars($brand_option['brand_id']);
                        $brand_name = htmlspecialchars($brand_option['brand_name']);

                        // 選択されているブランドを保持
                        $selected = (isset($_GET['brand']) && $_GET['brand'] === $brand_id) ? 'selected' : '';
                        echo "<option value=\"$brand_id\" $selected>$brand_name</option>";
                    }
                    ?>
                </select>
                <select name="gender" id="gender" onchange="this.form.submit()">
                    <?php
                    foreach ($genders as $gender_option) {
                        $gender_id = htmlspecialchars($gender_option['gender_id']);
                        $gender_name = htmlspecialchars($gender_option['gender']);

                        $selected = (isset($_GET['gender']) && $_GET['gender'] === $gender_id) ? 'selected' : '';
                        echo "<option value=\"$gender_id\" $selected>$gender_name</option>";
                    }
                    ?>
                </select>
            </form>
            <h1><?php echo $translations['Down Vest'] ?></h1>
             <div class="products-container">
                <ul>
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                            <li>
                                <?php
                                $imgBlob = $product['thumbnail'];
                                $mimeType = 'image/png,image/jpg,image/svg'; // MIMEタイプはデフォルトを設定（例としてPNG）

                                // MIMEタイプを動的に取得
                                $finfo = new finfo(FILEINFO_MIME_TYPE);
                                $mimeType = $finfo->buffer($imgBlob); // BLOBデータからMIMEタイプを取得

                                // Base64にエンコード
                                $encodedImg = base64_encode($imgBlob);
                                ?>
                                <!-- 商品の詳細ページへのリンク -->
                                <a href="../../goods.php?shop_id=<?php echo htmlspecialchars($product['shop_id'], ENT_QUOTES, 'UTF-8'); ?>&shop_group=<?php echo htmlspecialchars($product['shop_group']) ?>">
                                    <img src="data:<?php echo $mimeType; ?>;base64,<?php echo $encodedImg; ?>" alt="goods img" class="product-image">

                                    <div>
                                        <strong><?php echo htmlspecialchars($product['goods'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                    </div>
                                    <div><?php echo $translations['Discounted Price'] ?>: ¥<?php echo htmlspecialchars(number_format($product['price']), ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div><?php echo $translations['Price'] ?>: ¥<?php echo htmlspecialchars(number_format($product['original_price']), ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div><?php echo $translations['Sale'] ?>: <?php echo htmlspecialchars($product['sale'], ENT_QUOTES, 'UTF-8'); ?>%</div>
                                    <div><?php echo $translations['Brand'] ?>: <?php echo htmlspecialchars($product['brand_id'], ENT_QUOTES, 'UTF-8'); ?></div>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>商品がありません。</p>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</body>

</html>