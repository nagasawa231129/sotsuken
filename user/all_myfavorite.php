<?php
include "../../db_open.php";
include "../header.php";
include "../head.php";
echo "<link rel='stylesheet' href='header.css'>";
echo "<link rel='stylesheet' href='all_item.css'>";

?>
<div class="main-content">
    <aside class="sidebar">
        <h2 data-i18n="search"><?php echo $translations['Search'] ?></h2>
        <ul>
            <li><a href="brand.php" data-i18n="Search_By_brand"><?php echo $translations['Search By Brand'] ?></a></li>
            <li><a href="category/category.php?gender=ALL" data-i18n="Search_By_category"><?php echo $translations['Search By Category'] ?></a></li>
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
        <h2><?php echo $translations['Your Favorite Brand']; ?></h2>
        <?php
        $favoriteBrandName = '';
        $products = []; // 初期化

        if ($userId) {
            // 1. ユーザーが最も購入したブランドIDを取得するクエリ
            $sql = "
            SELECT brand.brand_id, brand.brand_name, SUM(cd.quantity) AS total_quantity
            FROM cart_detail AS cd
            INNER JOIN shop ON cd.shop_id = shop.shop_id
            INNER JOIN brand ON shop.brand_id = brand.brand_id
            WHERE cd.user_id = :user_id
            GROUP BY brand.brand_id
            ORDER BY total_quantity DESC
            LIMIT 1
            ";

            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $topBrand = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($topBrand) {
                $topBrandId = $topBrand['brand_id'];
                $favoriteBrandName = htmlspecialchars($topBrand['brand_name']);

                // 2. ブランドIDの商品を取得するクエリ
                $sql_products = "
                SELECT shop.*, brand.brand_name, sale.sale_id, sale.sale, `group`.shop_group
                FROM shop
                LEFT JOIN brand ON brand.brand_id = shop.brand_id
                LEFT JOIN `group` ON `group`.shop_id = shop.shop_id
                LEFT JOIN sale ON sale.sale_id = shop.sale_id
                WHERE shop.brand_id = :brand_id";


                $stmt_products = $dbh->prepare($sql_products);
                $stmt_products->bindValue(':brand_id', $topBrandId, PDO::PARAM_INT);
                $stmt_products->execute();
                $products = $stmt_products->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $favoriteBrandName = "お気に入りのブランドはまだありません。";
            }
        } else {
            $favoriteBrandName = "ログインしてください。";
        }
        ?>
        <p>あなたのお気に入りブランド：<?php echo $favoriteBrandName; ?></p>
        <div class="products-container">
            <?php
            if (!empty($products)) {
                foreach ($products as $rec) {
                    // 商品表示のコード
                    $imgBlob = $rec['thumbnail'];
                    $mimeType = $rec['thumbnail_mime'] ?? 'image/png'; // MIMEタイプのデフォルト値
                    $encodedImg = base64_encode($imgBlob);
                    $productLink = "goods.php?shop_id={$rec['shop_id']}&shop_group={$rec['shop_group']}";

                    echo "<a href='{$productLink}' style='text-decoration: none; color: inherit;'>";
                    echo "<div class='sale-product-item'>";
                    echo "<div class='product-image-block'>";
                    echo "<img src='data:{$mimeType};base64,{$encodedImg}' alt='goods img' class='sale-product-image'>";
                    echo "</div>";
                    echo "<div class='product-info'>";
                    echo "<p class='sale-product-brand' data-i18n='brand'>" . $translations['Brand'] . "： {$rec['brand_name']}</p>";
                    echo "<p class='sale-product-name' data-i18n='goods_name'>" . $translations['Product Name'] . " ：{$rec['goods']}</p>";
                    echo "<p class='sale-product-price' data-i18n='price'>" . $translations['Price'] . "：{$rec['original_price']}円</p>";

                    // 割引計算と表示
                    if ($rec['sale_id'] && isset($rec['original_price'])) {
                        $discounted_price = ceil($rec['original_price'] * (1 - $rec['sale'] / 100));
                        echo "<p class='product-discount' data-i18n='discounted_price'>" . $translations['Discounted Price'] . "：{$discounted_price}円</p>";
                    }

                    echo "</div>";
                    echo "</div>";
                    echo "</a>";
                }
            } else {
                echo "<p>このブランドの商品の情報は見つかりませんでした。</p>";
            }
            ?>
        </div>
    </div>

</div>