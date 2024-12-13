<?php
include "../../db_open.php";
include "../header.php";
include "../head.php";
// ログインしていないときにエラーが出ない処理
if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];
} else {
    $userId = null;
}
$user_name = isset($_SESSION['login']) ? $_SESSION['display_name'] : 'ゲスト';
// var_dump($_SESSION['login']);
// var_dump($_SESSION['display_name']);
// var_dump($user_name);
// var_dump($userId);
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ja';

// // 言語ファイルのパスを設定
$lang_file = __DIR__ . "/{$lang}.php";

// // 言語ファイルを読み込み
if (file_exists($lang_file)) {
    include($lang_file);
} else {
    die("Error: Language file not found.");
}
?>

<!DOCTYPE html>
<link rel="stylesheet" href="toppage.css">
<link rel="stylesheet" href="header.css">

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
    <div class="products-wrapper">
        <!-- 新しい順 -->

        <h2><?php echo $translations['New Items'] ?></h2>
        <div class="sale-products-container">
            <div class="sale-product-scroll">
                <?php
                $sql = "SELECT  shop.*, 
                brand.brand_name, 
                sale.sale_id
        FROM shop
        LEFT OUTER JOIN brand ON brand.brand_id = shop.brand_id
        LEFT OUTER JOIN sale ON sale.sale_id = shop.sale_id
        ORDER BY shop.arrival DESC  -- arrivalが新しい順に並べ替え
        LIMIT :limit OFFSET :offset";

                $stmt = $dbh->prepare($sql);
                $stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
                $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    while ($rec = $stmt->fetch()) {
                        // BLOB型の画像データをBase64エンコードして表示
                        $imgBlob = $rec['thumbnail'];
                        $mimeType = 'image/png'; // MIMEタイプはデフォルトを設定（例としてPNG）

                        // MIMEタイプを動的に取得
                        $finfo = new finfo(FILEINFO_MIME_TYPE);
                        $mimeType = $finfo->buffer($imgBlob); // BLOBデータからMIMEタイプを取得

                        // Base64にエンコード
                        $encodedImg = base64_encode($imgBlob);

                        // 商品詳細ページへのリンク生成
                        $productLink = "goods.php?shop_id={$rec['shop_id']}";

                        // 商品情報を全体リンクで表示
                        echo "<a href='{$productLink}' style='text-decoration: none; color: inherit;'>";
                        echo "<div class='sale-product-item'>";
                        echo "<div class='product-image-block'>";

                        // 画像表示
                        echo "<img src='data:{$mimeType};base64,{$encodedImg}' alt='goods img' class='sale-product-image'>";
                        echo "</div>";
                        echo "<div class='product-info'>";

                        // ブランド名
                        echo "<p class='sale-product-brand' data-i18n='brand'>" . $translations['Brand'] . "： {$rec['brand_name']}</p>";

                        // 商品名
                        echo "<p class='sale-product-name' data-i18n='goods_name'>" . $translations['Product Name'] . " ：{$rec['goods']}</p>";

                        // 価格
                        echo "<p class='sale-product-price' data-i18n='price'>" . $translations['Price'] . "：{$rec['original_price']}円</p>";

                        // 割引計算と表示
                        if ($rec['sale_id']) {
                            $sale_id = $rec['sale_id'];
                            $sql_sale = "SELECT sale, sale_id FROM sale WHERE sale_id = :sale_id";
                            $stmt_sale = $dbh->prepare($sql_sale);
                            $stmt_sale->bindParam(':sale_id', $sale_id);
                            $stmt_sale->execute();
                            $sale = $stmt_sale->fetch(PDO::FETCH_ASSOC);

                            // 割引情報が取得でき、割引率が10ではない場合のみ処理
                            if ($sale && isset($rec['original_price']) && $sale['sale_id'] != 10) {
                                $discounted_price = ceil($rec['original_price'] * (1 - $sale['sale'] / 100)); // 小数点切り上げ
                                echo "<p class='product-discount' data-i18n='discounted_price'>" . $translations['Discounted Price'] . "：{$discounted_price}円</p>";
                            }
                        }
                        echo "</div>";
                        echo "</div>";
                        echo "</a>";
                    }
                } else {
                    echo "<p>商品が見つかりません。</p>";
                }
                ?>
            </div>
        </div>
        <!-- <div class="product-sale"> -->
        <!-- セール商品のみ -->
        <h2><?php echo $translations['Sale Items'] ?></h2>
        <div class="sale-products-container">
            <div class="sale-product-scroll">
                <?php
                $sql = "SELECT shop.*, 
               brand.brand_name, 
               sale.sale_id
       FROM shop
       LEFT OUTER JOIN brand ON brand.brand_id = shop.brand_id
       LEFT OUTER JOIN sale ON sale.sale_id = shop.sale_id
       WHERE shop.sale_id != 0  -- saleカラムが0以外の商品を表示
       GROUP BY shop.shop_id
       LIMIT :limit OFFSET :offset";

                $stmt = $dbh->prepare($sql);
                $stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
                $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    while ($rec = $stmt->fetch()) {
                        // BLOB型の画像データをBase64エンコードして表示
                        $imgBlob = $rec['thumbnail'];
                        $mimeType = 'image/png'; // MIMEタイプはデフォルトを設定（例としてPNG）

                        // MIMEタイプを動的に取得
                        $finfo = new finfo(FILEINFO_MIME_TYPE);
                        $mimeType = $finfo->buffer($imgBlob); // BLOBデータからMIMEタイプを取得

                        // Base64にエンコード
                        $encodedImg = base64_encode($imgBlob);

                        // 商品詳細ページへのリンク生成
                        $productLink = "goods.php?shop_id={$rec['shop_id']}";

                        // 商品情報を全体リンクで表示
                        echo "<a href='{$productLink}' style='text-decoration: none; color: inherit;'>";
                        echo "<div class='sale-product-item'>";
                        echo "<div class='product-image-block'>";

                        // 画像表示
                        echo "<img src='data:{$mimeType};base64,{$encodedImg}' alt='goods img' class='sale-product-image'>";
                        echo "</div>";
                        echo "<div class='product-info'>";

                        // ブランド名
                        echo "<p class='sale-product-brand' data-i18n='brand'>" . $translations['Brand'] . "： {$rec['brand_name']}</p>";

                        // 商品名
                        echo "<p class='sale-product-name' data-i18n='goods_name'>" . $translations['Product Name'] . " ：{$rec['goods']}</p>";

                        // 価格
                        echo "<p class='sale-product-price' data-i18n='price'>" . $translations['Price'] . "：{$rec['original_price']}円</p>";

                        // 割引計算と表示
                        if ($rec['sale_id']) {
                            $sale_id = $rec['sale_id'];
                            $sql_sale = "SELECT sale, sale_id FROM sale WHERE sale_id = :sale_id";
                            $stmt_sale = $dbh->prepare($sql_sale);
                            $stmt_sale->bindParam(':sale_id', $sale_id);
                            $stmt_sale->execute();
                            $sale = $stmt_sale->fetch(PDO::FETCH_ASSOC);

                            // 割引情報が取得でき、割引率が10ではない場合のみ処理
                            if ($sale && isset($rec['original_price']) && $sale['sale_id'] != 10) {
                                $discounted_price = ceil($rec['original_price'] * (1 - $sale['sale'] / 100)); // 小数点切り上げ
                                echo "<p class='product-discount' data-i18n='discounted_price'>" . $translations['Discounted Price'] . "：{$discounted_price}円</p>";
                            }
                        }
                        echo "</div>";
                        echo "</div>";
                        echo "</a>";
                    }
                } else {
                    echo "<p>商品が見つかりません。</p>";
                }
                ?>
            </div>
        </div>

        <!-- ランキング順 -->

        <h2><?php echo $translations['Popular Ranking'] ?></h2>
        <div class="sale-products-container">
            <div class="sale-product-scroll">
                <?php
                $sql = "SELECT shop.*, 
                brand.brand_name, 
                sale.sale_id, 
                shop.thumbnail
        FROM shop
        LEFT OUTER JOIN brand ON brand.brand_id = shop.brand_id
        LEFT OUTER JOIN sale ON sale.sale_id = shop.sale_id
        ORDER BY shop.buy DESC  -- buyが多い順に並べ替え
        LIMIT :limit OFFSET :offset";

                $stmt = $dbh->prepare($sql);
                $stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
                $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    while ($rec = $stmt->fetch()) {
                        // BLOB型の画像データをBase64エンコードして表示
                        $imgBlob = $rec['thumbnail'];
                        $mimeType = 'image/png'; // MIMEタイプはデフォルトを設定（例としてPNG）

                        // MIMEタイプを動的に取得
                        $finfo = new finfo(FILEINFO_MIME_TYPE);
                        $mimeType = $finfo->buffer($imgBlob); // BLOBデータからMIMEタイプを取得

                        // Base64にエンコード
                        $encodedImg = base64_encode($imgBlob);

                        // 商品詳細ページへのリンク生成
                        $productLink = "goods.php?shop_id={$rec['shop_id']}";

                        // 商品情報を全体リンクで表示
                        echo "<a href='{$productLink}' style='text-decoration: none; color: inherit;'>";
                        echo "<div class='sale-product-item'>";
                        echo "<div class='product-image-block'>";

                        // 画像表示
                        echo "<img src='data:{$mimeType};base64,{$encodedImg}' alt='goods img' class='sale-product-image'>";
                        echo "</div>";
                        echo "<div class='product-info'>";

                        // ブランド名
                        echo "<p class='sale-product-brand' data-i18n='brand'>" . $translations['Brand'] . "： {$rec['brand_name']}</p>";

                        // 商品名
                        echo "<p class='sale-product-name' data-i18n='goods_name'>" . $translations['Product Name'] . " ：{$rec['goods']}</p>";

                        // 価格
                        echo "<p class='sale-product-price' data-i18n='price'>" . $translations['Price'] . "：{$rec['original_price']}円</p>";

                        // 割引計算と表示
                        if ($rec['sale_id']) {
                            $sale_id = $rec['sale_id'];
                            $sql_sale = "SELECT sale, sale_id FROM sale WHERE sale_id = :sale_id";
                            $stmt_sale = $dbh->prepare($sql_sale);
                            $stmt_sale->bindParam(':sale_id', $sale_id);
                            $stmt_sale->execute();
                            $sale = $stmt_sale->fetch(PDO::FETCH_ASSOC);

                            // 割引情報が取得でき、割引率が10ではない場合のみ処理
                            if ($sale && isset($rec['original_price']) && $sale['sale_id'] != 10) {
                                $discounted_price = ceil($rec['original_price'] * (1 - $sale['sale'] / 100)); // 小数点切り上げ
                                echo "<p class='product-discount' data-i18n='discounted_price'>" . $translations['Discounted Price'] . "：{$discounted_price}円</p>";
                            }
                        }
                        echo "</div>";
                        echo "</div>";
                        echo "</a>";
                    }
                } else {
                    echo "<p>商品が見つかりません。</p>";
                }
                ?>
            </div>
        </div>
    </div>

    <!-- </div> -->
</div>
<div class="pagination">
    <?php if ($totalPages > 1): // ページ数が1ページより多い場合のみ表示 
    ?>
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>" data-i18n="previous">前へ</a>
        <?php endif; ?>

        <!-- 最初のページ -->
        <?php if ($page > 3): ?>
            <a href="?page=1">1</a>
            <span>...</span> <!-- 省略 -->
        <?php endif; ?>

        <!-- ページ番号のリンクを表示 -->
        <?php for ($i = max(1, $page - 1); $i <= min($totalPages, $page + 1); $i++): ?>
            <?php if ($i == $page): ?>
                <span><?php echo $i; ?></span> <!-- 現在のページ -->
            <?php else: ?>
                <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a> <!-- 他のページ -->
            <?php endif; ?>
        <?php endfor; ?>

        <!-- 最後のページ -->
        <?php if ($page < $totalPages - 2): ?>
            <span>...</span> <!-- 省略 -->
            <a href="?page=<?php echo $totalPages; ?>"><?php echo $totalPages; ?></a>
        <?php endif; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?php echo $page + 1; ?>" data-i18n="next">次へ</a>
        <?php endif; ?>
    <?php endif; ?>
</div>

</html>