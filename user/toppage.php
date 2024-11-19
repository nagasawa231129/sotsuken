<?php
include "../../db_open.php";

// ログインしていないときにエラーが出ない処理
if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];
} else {
    $userId = null;
}
?>

<!DOCTYPE html>
<?php
include "../header.php";
include "../head.php";
?>
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



<div class="sidebar">
    <h2>探す</h2>
    <ul>
        <li><a href="brand.php">ブランドで探す</a></li>
        <li><a href="ranking.php">ランキングで探す</a></li>
        <li><a href="sale.php">セール対象で探す</a></li>
        <li><a href="advanced_search.php">詳細検索</a></li>
    </ul>

    <h2>カテゴリーから探す</h2>
    <ul>
        <li class="category-item">
            <a href="./category/tops.php">トップス</a>
            <ul class="subcategory-list">
                <li><a href="./category/tops/tshirt-cutsew.php">Tシャツ/カットソー</a></li>
                <li><a href="./category/tops/shirt.php">シャツ/ブラウス</a></li>
                <li><a href="./category/tops/poloshirt.php">ポロシャツ</a></li>
                <li><a href="./category/tops/knit-sweater.php">ニット/セーター</a></li>
                <li><a href="./category/tops/vest.php">ベスト</a></li>
                <li><a href="./category/tops/parka.php">パーカー</a></li>
                <li><a href="./category/tops/sweat.php">スウェット</a></li>
                <li><a href="./category/tops/cardigan.php">カーディガン</a></li>
                <li><a href="./category/tops/ensemble.php">アンサンブル</a></li>
                <li><a href="./category/tops/jersey.php">ジャージ</a></li>
                <li><a href="./category/tops/tanktop.php">タンクトップ</a></li>
                <li><a href="./category/tops/camisole.php">キャミソール</a></li>
                <li><a href="./category/tops/tubetops.php">チューブトップス</a></li>
                <li><a href="./category/tops/auter-tops.php">その他トップス</a></li>
            </ul>
        </li>
        <li class="category-item">
            <a href="./category/skirt.php">ジャケット/アウター</a>
            <ul class="subcategory-list">
                <li><a href="./category/jacket-outerwear/collarless-coat.php">ノーカラーコート</a></li>
                <li><a href="./category/jacket-outerwear/collarless-jacket.php">ノーカラージャケット</a></li>
                <li><a href="./category/jacket-outerwear/denim-jacket.php">デニムジャケット</a></li>
                <li><a href="./category/jacket-outerwear/down-jacket.php">ダウンジャケット</a></li>
                <li><a href="./category/jacket-outerwear/down-vest.php">ダウンベスト</a></li>
                <li><a href="./category/jacket-outerwear/duffle-coat.php">ダッフルコート</a></li>
                <li><a href="./category/jacket-outerwear/jacket.php">ブルゾン</a></li>
                <li><a href="./category/jacket-outerwear/military-jacket.php">ミリタリージャケット</a></li>
                <li><a href="./category/jacket-outerwear/mods-coat.php">モッズコート</a></li>
                <li><a href="./category/jacket-outerwear/nylon-jacket.php">ナイロンジャケット</a></li>
                <li><a href="./category/jacket-outerwear/riders-jacket.php">ライダースジャケット</a></li>
                <li><a href="./category/jacket-outerwear/tailored-jacket.php">テーラードジャケット</a></li>
                <li><a href="./category/jacket-outerwear/trench-coat.php">トレンチコート</a></li>
                <li><a href="./category/jacket-outerwear/auter-jacket.php">その他アウター</a></li>
            </ul>
        </li>
        <li class="category-item">
            <a href="./category/pants.php">パンツ</a>
            <ul class="subcategory-list">
                <li><a href="./category/pants/cargo-pants.php">カーゴパンツ</a></li>
                <li><a href="./category/pants/chino-pants.php">チノパン</a></li>
                <li><a href="./category/pants/denim-pants.php">デニムパンツ</a></li>
                <li><a href="./category/pants/slacks.php">スラックス</a></li>
                <li><a href="./category/pants/sweat-pants.php">スウェットパンツ</a></li>
                <li><a href="./category/pants/auter-pants.php">その他パンツ</a></li>
            </ul>
        </li>
        <li class="category-item">
            <a href="./category/skirt.php">スカート</a>
            <ul class="subcategory-list">
                <li><a href="./category/skirt/mini-skirt.php">ミニスカート</a></li>
                <li><a href="./category/skirt/midi-skirt.php">ミディスカート</a></li>
                <li><a href="./category/skirt/long-skirt.php">ロングカート</a></li>
                <li><a href="./category/skirt/denim-skirt.php">デニムスカート</a></li>
            </ul>
        </li>
        <li class="category-item">
            <a href="./category/onepiece.php">ワンピース</a>
            <ul class="subcategory-list">
                <li><a href="./category/onepiece/dress.php">ドレス</a></li>
                <li><a href="./category/onepiece/jumper-skirt.php">ジャンパースカート</a></li>
                <li><a href="./category/onepiece/onepiece-dress.php">ワンピース</a></li>
                <li><a href="./category/onepiece/pants-dress.php">パンツドレス</a></li>
                <li><a href="./category/onepiece/shirts-onepiece.php">シャツワンピース</a></li>
                <li><a href="./category/onepiece/tunic.php">チュニック</a></li>
            </ul>
        </li>
    </ul>
</div>

<?php
// brandテーブルからbrand_nameを取得するSQLクエリ
// $sql = "SELECT shop.*, brand.brand_name, image.img
//         FROM shop 
//         LEFT outer JOIN brand ON brand.brand_id = shop.brand_id 
//         LEFT OUTER JOIN image on image.img_id = shop.img_id
//         LIMIT :limit OFFSET :offset";

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
        echo "<p style='text-align: center;'>ブランド：{$rec['brand_name']}</p>";

        // 商品名
        echo "<p style='text-align: center; font-weight: bold;'>商品名：{$rec['goods']}</p>";

        // 価格
        echo "<p style='text-align: center;'>値段：{$rec['price']}円</p>";

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
                echo "<p style='text-align: center; color: red;'>割引後価格：{$discounted_price}円</p>";
            }
        }

        // 商品説明
        echo "<p style='text-align: center;'>商品説明：{$rec['explanation']}</p>";

        echo "</div>";
        echo "</a>";
    }
} else {
    echo "<p>商品が見つかりません。</p>";
}


?>

<div class="pagination">
    <?php if ($totalPages > 1): // ページ数が1ページより多い場合のみ表示 
    ?>
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>">前へ</a>
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
            <a href="?page=<?php echo $page + 1; ?>">次へ</a>
        <?php endif; ?>
    <?php endif; ?>
</div>

</html>