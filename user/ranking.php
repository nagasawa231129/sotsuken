<?php
include "../../db_open.php"; // PDO接続のファイルをインクルード
include "../head.php"; // 必要なヘッダーを読み込む
include "../header.php";
echo "<link rel='stylesheet' href='ranking.css'>"; // 必要に応じてCSSを作成
echo "<link rel='stylesheet' href='header.css'>"; // 必要に応じてCSSを作成
echo "<link rel='stylesheet' href='toppage.css'>"; // 必要に応じてCSSを作成


// ランキングデータを取得
try {
    $sql = "SELECT shop.*, brand.brand_name 
            FROM shop 
            LEFT JOIN brand ON shop.brand_id = brand.brand_id 
            ORDER BY shop.buy DESC 
            LIMIT 100"; // TOP 10のランキング
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $rankingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "<p>エラーが発生しました: {$e->getMessage()}</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品ランキング</title>
</head>

<body>
    <div class="main-content">
        <aside class="sidebar">
            <h2 data-i18n="search"><?php echo $translations['Search'] ?></h2>
            <ul>
                <li><a href="brand.php" data-i18n="Search By_brand"><?php echo $translations['Search By Brand'] ?></a></li>
                <li><a href="category/category.php?gender=ALL" data-i18n="Search By_category"><?php echo $translations['Search By Category'] ?></a></li>
                <li><a href="ranking.php" data-i18n="Search By_ranking"><?php echo $translations['Search By Ranking'] ?></a></li>
                <li><a href="sale.php" data-i18n="Search By_sale"><?php echo $translations['Search By Sale'] ?></a></li>
                <li><a href="diagnosis.php" data-i18n="Search By_diagnosis"><?php echo $translations['Search By Diagnosis'] ?></a></li>
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
                        <li><a href="./category/tops/tubetop.php" data-i18n="tubetops"><?php echo $translations['Tubetop'] ?></a></li>
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
        <div class="products-wrapper">
            <h1><?php echo $translations['Popular Ranking'] ?></h1>
            <?php if ($rankingData && count($rankingData) > 0): ?>
                <div class="ranking-grid">
                    <?php foreach ($rankingData as $index => $item): ?>
                        <a href="goods.php?shop_id=<?= (int)$item['shop_id'] ?>" class="ranking-item">
                            <div class="ranking-label">#<?= $index + 1 ?></div>
                            <?php if (!empty($item['thumbnail'])): ?>
                                <?php
                                $thumbnailBlob = $item['thumbnail'];
                                $finfo = new finfo(FILEINFO_MIME_TYPE);
                                $mimeType = $finfo->buffer($thumbnailBlob);
                                $encodedImg = base64_encode($thumbnailBlob);
                                ?>
                                <img src="data:<?= $mimeType ?>;base64,<?= $encodedImg ?>" alt="商品画像" class="product-image">
                            <?php else: ?>
                                <img src="no-image.jpg" alt="画像なし" class="product-image">
                            <?php endif; ?>
                            <div class="item-details">
                                <h2 class="item-title"><?= htmlspecialchars($item['goods']) ?></h2>
                                <p class="brand-name"><?php echo $translations['Brand'] ?>: <?= htmlspecialchars($item['brand_name']) ?></p>
                                <p class="price"><?php echo $translations['Price'] ?>: <?= (int)$item['original_price'] ?> 円</p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p><?php echo $translations['No ranking data available'] ?></p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>