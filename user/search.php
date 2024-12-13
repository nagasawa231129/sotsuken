<?php
include "../../db_open.php";
include "../header.php";
include "../head.php";
if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];
} else {
    $userId = null;
}
$user_name = isset($_SESSION['login']) ? $_SESSION['display_name'] : 'ゲスト';

$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ja';

// // 言語ファイルのパスを設定
$lang_file = __DIR__ . "/{$lang}.php";

// // 言語ファイルを読み込み
if (file_exists($lang_file)) {
    include($lang_file);
} else {
    die("Error: Language file not found.");
}
// var_dump($_GET['query']);
$searchQuery = $_GET['query'] ?? ''; // クエリパラメータ 'query' を取得
$searchQuery = trim($searchQuery); // 前後の空白を削除
// var_dump($searchQuery);
// 初期化
$results = [];
// var_dump($results);
if (!empty($searchQuery)) {
    // SQLクエリで部分一致検索
// SQLクエリで部分一致検索
$sql = "SELECT shop.*, 
               brand.brand_name, 
               sale.sale_id, 
               shop.thumbnail
        FROM shop
        LEFT OUTER JOIN brand ON brand.brand_id = shop.brand_id
        LEFT OUTER JOIN sale ON sale.sale_id = shop.sale_id
        WHERE shop.goods LIKE :searchQueryGoods  
           OR brand.brand_name LIKE :searchQueryBrand  
        ORDER BY shop.buy DESC";  // 並べ替え条件は適宜変更

// 準備とバインド
$stmt = $dbh->prepare($sql);

// 2つのパラメータをそれぞれにバインド
$stmt->bindValue(':searchQueryGoods', '%' . $searchQuery . '%', PDO::PARAM_STR);  // 商品名部分一致
$stmt->bindValue(':searchQueryBrand', '%' . $searchQuery . '%', PDO::PARAM_STR);  // ブランド名部分一致

// クエリ実行
$stmt->execute();

    
    // 結果を取得
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<!-- <link rel="stylesheet" href="search.css"> -->
<link rel="stylesheet" href="header.css">
<link rel="stylesheet" href="toppage.css">
<link rel="stylesheet" href="querysearch.css">

<?php
$itemsPerPage = 85;

// 現在のページ番号を取得（デフォルトは1）
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// ページ番号が1より小さい場合は1に設定
if ($page < 1) {
    $page = 1;
}

// OFFSETを計算
$offset = ($page - 1) * $itemsPerPage;

// 総企業数を取得
$totalQuery = "SELECT COUNT(*) FROM shop";
$totalResult = $dbh->query($totalQuery);
$totalItems = $totalResult->fetchColumn();

// 総ページ数を計算
$totalPages = ceil($totalItems / $itemsPerPage);
?>


<div class="main-content">
    <aside class="sidebar">
        <h2 data-i18n="search"><?php echo $translations['Search'] ?></h2>
        <ul>
            <li><a href="brand.php" data-i18n="Search_By_brand"><?php echo $translations['Search By Brand'] ?></a></li>
            <li><a href="category/category.php" data-i18n="Search_By_category"><?php echo $translations['Search By Category'] ?></a></li>
            <li><a href="ranking.php" data-i18n="Search_By_ranking"><?php echo $translations['Search By Ranking'] ?></a></li>
            <li><a href="sale.php" data-i18n="Search_By_sale"><?php echo $translations['Search By Sale'] ?></a></li>
            <li><a href="diagnosis.php" data-i18n="Search_By_diagnosis"><?php echo $translations['Search By Diagnosis'] ?></a></li>
            <li><a href="advanced_search.php" data-i18n="advanced_search"><?php echo $translations['Advanced Search'] ?></a></li>
        </ul>

        <h2 data-i18n="categories_from"><?php echo $translations['Search By Category'] ?></h2>

        <ul class="category-list">
            <li class="category-item">
                <a href="./category/tops.php" data-i18n="tops"><?php echo $translations['Tops'] ?></a>
                <ul class="sub-category">
                    <li><a href="./category/tops/tshirt-cutsew.php" data-i18n="Tshirt-cutsew"><?php echo $translations['Tshirt Cutsew'] ?></a></li>
                    <li><a href="./category/tops/shirt.php" data-i18n="shirt-blouse"><?php echo $translations['Shirt Blouse'] ?></a></li>
                    <li><a href="./category/tops/poloshirt.php" data-i18n="poloshirt"><?php echo $translations['Polo Shirt'] ?></a></li>
                    <li><a href="./category/tops/knit-sweater.php" data-i18n="knit/sweater"><?php echo $translations['Knit Sweater'] ?></a></li>
                    <li><a href="./category/tops/vest.php" data-i18n="vast"><?php echo $translations['Vest'] ?></a></li>
                    <li><a href="./category/tops/parka.php" data-i18n="parka"><?php echo $translations['Parka'] ?></a></li>
                    <li><a href="./category/tops/sweat.php" data-i18n="sweat"><?php echo $translations['Sweat'] ?></a></li>
                    <li><a href="./category/tops/cardigan.php" data-i18n="cardigan"><?php echo $translations['Cardigan'] ?></a></li>
                    <li><a href="./category/tops/ensemble.php" data-i18n="ensemble"><?php echo $translations['Ensemble'] ?></a></li>
                    <li><a href="./category/tops/jersey.php" data-i18n="jersey"><?php echo $translations['Jersey'] ?></a></li>
                    <li><a href="./category/tops/tanktop.php" data-i18n="tanktop"><?php echo $translations['Tanktop'] ?></a></li>
                    <li><a href="./category/tops/camisole.php" data-i18n="camisole"><?php echo $translations['Camisole'] ?></a></li>
                    <li><a href="./category/tops/tubetops.php" data-i18n="tubetops"><?php echo $translations['Tubetop'] ?></a></li>
                    <li><a href="./category/tops/other-tops.php" data-i18n="other-tops"><?php echo $translations['Other Tops'] ?></a></li>
                </ul>
            </li>
            <li class="category-item">
                <a href="./category/jacket.php" data-i18n="jacket/outer"><?php echo $translations['Outerwear'] ?></a>
                <ul class="sub-category">
                    <li><a href="./category/jacket-outerwear/collarless-coat.php" data-i18n="collarless-coat"><?php echo $translations['Collarless Coat'] ?></a></li>
                    <li><a href="./category/jacket-outerwear/collarless-jacket.php" data-i18n="collarless-jacket"><?php echo $translations['Collarless Jacket'] ?></a></li>
                    <li><a href="./category/jacket-outerwear/denim-jacket.php" data-i18n="denim-jacket"><?php echo $translations['Denim Jacket'] ?></a></li>
                    <li><a href="./category/jacket-outerwear/down-jacket.php" data-i18n="down-jacket"><?php echo $translations['Down Jacket'] ?></a></li>
                    <li><a href="./category/jacket-outerwear/down-vest.php" data-i18n="down-vest"><?php echo $translations['Down Vest'] ?></a></li>
                    <li><a href="./category/jacket-outerwear/duffle-coat.php" data-i18n="duffle-coat"><?php echo $translations['Duffle Coat'] ?></a></li>
                    <li><a href="./category/jacket-outerwear/jacket.php" data-i18n="jacket"><?php echo $translations['Blouson'] ?></a></li>
                    <li><a href="./category/jacket-outerwear/military-jacket.php" data-i18n="millitary-jacket"><?php echo $translations['Military Jacket'] ?></a></li>
                    <li><a href="./category/jacket-outerwear/mods-coat.php" data-i18n="mods-coat"><?php echo $translations['Mods Coat'] ?></a></li>
                    <li><a href="./category/jacket-outerwear/nylon-jacket.php" data-i18n="nylon-jacket"><?php echo $translations['Nylon Jacket'] ?></a></li>
                    <li><a href="./category/jacket-outerwear/riders-jacket.php" data-i18n="riders-jacket"><?php echo $translations['Riders Jacket'] ?></a></li>
                    <li><a href="./category/jacket-outerwear/tailored-jacket.php" data-i18n="tailored-jacket"><?php echo $translations['Tailored Jacket'] ?></a></li>
                    <li><a href="./category/jacket-outerwear/trench-coat.php" data-i18n="trench-coat"><?php echo $translations['Trench Coat'] ?></a></li>
                    <li><a href="./category/jacket-outerwear/other-jacket.php" data-i18n="other-jacket"><?php echo $translations['Other Outerwear'] ?></a></li>
                </ul>
            </li>
            <li class="category-item">
                <a href="./category/pants.php" data-i18n="pants"><?php echo $translations['Pants'] ?></a>
                <ul class="sub-category">
                    <li><a href="./category/pants/cargo-pants.php" data-i18n="cargo-pants"><?php echo $translations['Cargo Pants'] ?></a></li>
                    <li><a href="./category/pants/chino-pants.php" data-i18n="chino-pants"><?php echo $translations['Chino Pants'] ?></a></li>
                    <li><a href="./category/pants/denim-pants.php" data-i18n="denim-pants"><?php echo $translations['Denim Pants'] ?></a></li>
                    <li><a href="./category/pants/slacks.php" data-i18n="slacks"><?php echo $translations['Slacks'] ?></a></li>
                    <li><a href="./category/pants/sweat-pants.php" data-i18n="sweat-pants"><?php echo $translations['Sweat Pants'] ?></a></li>
                    <li><a href="./category/pants/other-pants.php" data-i18n="other-pants"><?php echo $translations['Other Pants'] ?></a></li>
                </ul>
            </li>
            <li class="category-item">
                <a href="./category/skirt.php" data-i18n="skirt"><?php echo $translations['Skirt'] ?></a>
                <ul class="sub-category">
                    <li><a href="./category/skirt/mini-skirt.php" data-i18n="mini-skirt"><?php echo $translations['Mini Skirt'] ?></a></li>
                    <li><a href="./category/skirt/midi-skirt.php" data-i18n="midi-skirt"><?php echo $translations['Midi Skirt'] ?></a></li>
                    <li><a href="./category/skirt/long-skirt.php" data-i18n="long-skirt"><?php echo $translations['Long Skirt'] ?></a></li>
                    <li><a href="./category/skirt/denim-skirt.php" data-i18n="denim-skirt"><?php echo $translations['Denim Skirt'] ?></a></li>
                </ul>
            </li>
            <li class="category-item">
                <a href="./category/onepiece.php" data-i18n="onepiece"><?php echo $translations['Onepiece'] ?></a>
                <ul class="sub-category">
                    <li><a href="./category/onepiece/dress.php" data-i18n="dress"><?php echo $translations['Dress'] ?></a></li>
                    <li><a href="./category/onepiece/jumper-skirt.php" data-i18n="jumper-skirt"><?php echo $translations['Jumper Skirt'] ?></a></li>
                    <li><a href="./category/onepiece/onepiece-dress.php" data-i18n="onepiece-dress"><?php echo $translations['Onepiece'] ?></a></li>
                    <li><a href="./category/onepiece/pants-dress.php" data-i18n="pants-dress"><?php echo $translations['Pants Dress'] ?></a></li>
                    <li><a href="./category/onepiece/shirts-onepiece.php" data-i18n="shirts-onepiece"><?php echo $translations['Shirt Onepiece'] ?></a></li>
                    <li><a href="./category/onepiece/tunic.php" data-i18n="tunic"><?php echo $translations['Tunic'] ?></a></li>
                </ul>
            </li>
        </ul>
    </aside>

    <body>

    <div class="main-content">
    <div class="products-wrapper">
        <h1><?php echo $translations['Search Results'] ?></h1>

        <?php if (empty($results)): ?>
            <p>該当する商品が見つかりませんでした。</p>
        <?php else: ?>
            <ul class="products-list">
                <?php foreach ($results as $item): ?>
                    <li class="product-item">
                        <?php
                        // サムネイル画像の取得
                        if (isset($item['thumbnail'])) {
                            $imgBlob = $item['thumbnail'];
                            $finfo = new finfo(FILEINFO_MIME_TYPE);
                            $mimeType = $finfo->buffer($imgBlob);
                            $encodedImg = base64_encode($imgBlob);
                        } else {
                            // サムネイルがない場合の処理
                            $encodedImg = null;
                        }
                        ?>
                        <a href='goods.php?shop_id=<?php echo $item['shop_id']; ?>'>
                            <?php if ($encodedImg): ?>
                                <img src="data:<?php echo $mimeType; ?>;base64,<?php echo $encodedImg; ?>" alt="goods img" class="sale-product-image">
                            <?php else: ?>
                                <img src="default-thumbnail.jpg" alt="default img" class="sale-product-image"> <!-- デフォルト画像 -->
                            <?php endif; ?>
                            <h2><?php echo htmlspecialchars($item['goods']); ?></h2>
                            <p><?php echo $translations['Brand'] ?>: <?php echo htmlspecialchars($item['brand_name']); ?></p>
                            <div class="product-prices">
                                <span class="product-price"><?php echo "￥" . number_format($item['price']); ?></span>
                            </div>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

</body>
</html>