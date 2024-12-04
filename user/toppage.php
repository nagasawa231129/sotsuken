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
var_dump($userId);
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ja';

// // 言語ファイルのパスを設定
$lang_file = __DIR__ . "/{$lang}.php";

// // 言語ファイルを読み込み
if (file_exists($lang_file)) {
    include($lang_file);
} else {
    die("Error: Language file not found.");
}
var_dump("$lang");
var_dump("$lang_file");
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
        <h2 data-i18n="search"><?php echo $translations['search']?></h2>
        <ul>
        <li><a href="brand.php" data-i18n="search_by_brand"><?php echo $translations['search_by_brand']?></a></li>
            <li><a href="category/category.php" data-i18n="search_by_category"><?php echo $translations['search_by_category']?></a></li>
            <li><a href="ranking.php" data-i18n="search_by_ranking"><?php echo $translations['search_by_ranking']?></a></li>
            <li><a href="sale.php" data-i18n="search_by_sale"><?php echo $translations['search_by_sale']?></a></li>
            <li><a href="diagnosis.php" data-i18n="search_by_diagnosis"><?php echo $translations['search_by_diagnosis']?></a></li>
            <li><a href="advanced_search.php" data-i18n="advanced_search"><?php echo $translations['advanced_search']?></a></li>
        </ul>

        <h2 data-i18n="categories_from"><?php echo $translations['search_by_category']?></h2>

        <ul class="category-list">
            <li class="category-item">
                <a href="./category/tops.php" data-i18n="tops"><?php echo $translations['tops']?></a>
                <ul class="sub-category">
                    <li><a href="./category/tops/tshirt-cutsew.php" data-i18n="Tshirt-cutsew"><?php echo $translations['tshirt-cutsew']?></a></li>
                    <li><a href="./category/tops/shirt.php" data-i18n="shirt-blouse">シャツ/ブラウス</a></li>
                    <li><a href="./category/tops/poloshirt.php" data-i18n="poloshirt">ポロシャツ</a></li>
                    <li><a href="./category/tops/knit-sweater.php" data-i18n="knit/sweater">ニット/セーター</a></li>
                    <li><a href="./category/tops/vest.php" data-i18n="vast">ベスト</a></li>
                    <li><a href="./category/tops/parka.php" data-i18n="parka">パーカー</a></li>
                    <li><a href="./category/tops/sweat.php" data-i18n="sweat">スウェット</a></li>
                    <li><a href="./category/tops/cardigan.php" data-i18n="cardigan">カーディガン</a></li>
                    <li><a href="./category/tops/ensemble.php" data-i18n="ensemble">アンサンブル</a></li>
                    <li><a href="./category/tops/jersey.php" data-i18n="jersey">ジャージ</a></li>
                    <li><a href="./category/tops/tanktop.php" data-i18n="tanktop">タンクトップ</a></li>
                    <li><a href="./category/tops/camisole.php" data-i18n="camisole">キャミソール</a></li>
                    <li><a href="./category/tops/tubetops.php" data-i18n="tubetops">チューブトップス</a></li>
                    <li><a href="./category/tops/auter-tops.php" data-i18n="auter-tops">その他トップス</a></li>
                </ul>
            </li>
            <li class="category-item">
                <a href="./category/jacket.php" data-i18n="jacket/outer"><?php echo $translations['outerwear']?></a>
                <ul class="sub-category">
                    <li><a href="./category/jacket-outerwear/collarless-coat.php" data-i18n="collarless-coat">ノーカラーコート</a></li>
                    <li><a href="./category/jacket-outerwear/collarless-jacket.php" data-i18n="collarless-jacket">ノーカラージャケット</a></li>
                    <li><a href="./category/jacket-outerwear/denim-jacket.php" data-i18n="denim-jacket">デニムジャケット</a></li>
                    <li><a href="./category/jacket-outerwear/down-jacket.php" data-i18n="down-jacket">ダウンジャケット</a></li>
                    <li><a href="./category/jacket-outerwear/down-vest.php" data-i18n="down-vest">ダウンベスト</a></li>
                    <li><a href="./category/jacket-outerwear/duffle-coat.php" data-i18n="duffle-coat">ダッフルコート</a></li>
                    <li><a href="./category/jacket-outerwear/jacket.php" data-i18n="jacket">ブルゾン</a></li>
                    <li><a href="./category/jacket-outerwear/military-jacket.php" data-i18n="millitary-jacket">ミリタリージャケット</a></li>
                    <li><a href="./category/jacket-outerwear/mods-coat.php" data-i18n="mods-coat">モッズコート</a></li>
                    <li><a href="./category/jacket-outerwear/nylon-jacket.php" data-i18n="nylon-jacket">ナイロンジャケット</a></li>
                    <li><a href="./category/jacket-outerwear/riders-jacket.php" data-i18n="riders-jacket">ライダースジャケット</a></li>
                    <li><a href="./category/jacket-outerwear/tailored-jacket.php" data-i18n="tailored-jacket">テーラードジャケット</a></li>
                    <li><a href="./category/jacket-outerwear/trench-coat.php" data-i18n="trench-coat">トレンチコート</a></li>
                    <li><a href="./category/jacket-outerwear/auter-jacket.php" data-i18n="auter-jacket">その他アウター</a></li>
                </ul>
            </li>
            <li class="category-item">
                <a href="./category/pants.php" data-i18n="pants"><?php echo $translations['pants']?></a>
                <ul class="sub-category">
                    <li><a href="./category/pants/cargo-pants.php" data-i18n="cargo-pants">カーゴパンツ</a></li>
                    <li><a href="./category/pants/chino-pants.php" data-i18n="chino-pants">チノパン</a></li>
                    <li><a href="./category/pants/denim-pants.php" data-i18n="denim-pants">デニムパンツ</a></li>
                    <li><a href="./category/pants/slacks.php" data-i18n="slacks">スラックス</a></li>
                    <li><a href="./category/pants/sweat-pants.php" data-i18n="sweat-pants">スウェットパンツ</a></li>
                    <li><a href="./category/pants/auter-pants.php" data-i18n="auter-pants">その他パンツ</a></li>
                </ul>
            </li>
            <li class="category-item">
                <a href="./category/skirt.php" data-i18n="skirt"><?php echo $translations['skirt']?></a>
                <ul class="sub-category">
                    <li><a href="./category/skirt/mini-skirt.php" data-i18n="mini-skirt">ミニスカート</a></li>
                    <li><a href="./category/skirt/midi-skirt.php" data-i18n="midi-skirt">ミディスカート</a></li>
                    <li><a href="./category/skirt/long-skirt.php" data-i18n="long-skirt">ロングカート</a></li>
                    <li><a href="./category/skirt/denim-skirt.php" data-i18n="denim-skirt">デニムスカート</a></li>
                </ul>
            </li>
            <li class="category-item">
                <a href="./category/onepiece.php" data-i18n="onepiece"><?php echo $translations['onepiece']?></a>
                <ul class="sub-category">
                    <li><a href="./category/onepiece/dress.php" data-i18n="dress">ドレス</a></li>
                    <li><a href="./category/onepiece/jumper-skirt.php" data-i18n="jumper-skirt">ジャンパースカート</a></li>
                    <li><a href="./category/onepiece/onepiece-dress.php" data-i18n="onepiece-dress">ワンピース</a></li>
                    <li><a href="./category/onepiece/pants-dress.php" data-i18n="pants-dress">パンツドレス</a></li>
                    <li><a href="./category/onepiece/shirts-onepiece.php" data-i18n="shirts-onepiece">シャツワンピース</a></li>
                    <li><a href="./category/onepiece/tunic.php" data-i18n="tunic">チュニック</a></li>
                </ul>
            </li>
        </ul>
    </aside>
    <div class="products-container">
        <?php
        $sql = "SELECT shop.*, 
               brand.brand_name, 
               sale.sale, 
               image.img
        FROM shop
        LEFT OUTER JOIN brand ON brand.brand_id = shop.brand_id
        LEFT OUTER JOIN image ON image.shop_id = shop.shop_id
        LEFT OUTER JOIN sale ON sale.sale_id = shop.sale_id
        GROUP BY shop.shop_id
        LIMIT :limit OFFSET :offset";



        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            while ($rec = $stmt->fetch()) {
                // BLOB型の画像データをBase64エンコードして表示
                $imgBlob = $rec['img'];
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
                echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px; max-width: 300px;'>";

                // 画像表示
                echo "<img src='data:{$mimeType};base64,{$encodedImg}' alt='goods img' style='height: 100px; width: 100px; object-fit: cover; display: block; margin: 0 auto;'>";

                // ブランド名
                echo "<p style='text-align: center;' data-i18n='brand'>" . $translations['brand'] . "： {$rec['brand_name']}</p>";

                // 商品名
                echo "<p style='text-align: center; font-weight: bold;' data-i18n='goods_name'>". $translations['product_name'] ." ：{$rec['goods']}</p>";

                // 価格
                echo "<p style='text-align: center;' data-i18n='price'>". $translations['price'] ."：{$rec['price']}円</p>";

                // 割引計算と表示
                if ($rec['sale_id']) {
                    $sale_id = $rec['sale_id'];
                    $sql_sale = "SELECT sale FROM sale WHERE sale_id = :sale_id";
                    $stmt_sale = $dbh->prepare($sql_sale);
                    $stmt_sale->bindParam(':sale_id', $sale_id);
                    $stmt_sale->execute();
                    $sale = $stmt_sale->fetch(PDO::FETCH_ASSOC);

                    if ($sale) {
                        $discounted_price = $rec['price'] * (1 - $sale['sale'] / 100);
                        echo "<p style='text-align: center; color: red;' data-i18n='discounted_price'>" . $translations['discounted_price'] ."：{$discounted_price}円</p>";
                    }
                }

                // 商品説明
                echo "<p style='text-align: center;' data-i18n='description'>" . $translations['description'] ."：{$rec['explanation']}</p>";

                echo "</div>";
                echo "</a>";
            }
        } else {
            echo "<p>商品が見つかりません。</p>";
        }

        ?>
    </div>
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