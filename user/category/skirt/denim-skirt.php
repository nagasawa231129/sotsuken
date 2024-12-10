<?php
include "../../../../db_open.php"; // PDO接続のファイルをインクルード
include "../../../head.php";
include "../../../header.php";
echo "<link rel='stylesheet' href='../../header.css'>";
echo "<link rel='stylesheet' href='../tops.css'>";
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'default';
$brand = isset($_GET['brand']) && $_GET['brand'] !== '' ? $_GET['brand'] : null;


// ブランドフィルタがある場合の条件追加
if ($brand !== null) {
    $sql .= " AND shop.brand_id = ?";
}
// SQLクエリ修正：必要なカラムを明示的に指定
$sql = "
SELECT * FROM shop
JOIN subcategory ON shop.subcategory_id = subcategory.subcategory_id
JOIN brand ON shop.brand_id = brand.brand_id
JOIN sale ON shop.sale_id = sale.sale_id
WHERE subcategory.subcategory_name =  'デニムスカート'";

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
        // セール商品
        $sql .= " ORDER BY shop.sale_id DESC";
        break;
}

$params = [];
if ($brand !== null) {
    $params[] = $brand;
}

$stmt = $dbh->query($sql);
$stmt->execute($params);

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT * FROM brand";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$brands = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            <h2>探す</h2>
            <ul>
                <li><a href="../../brand.php">ブランドで探す</a></li>
                <li><a href="../category.php">カテゴリ―で探す</a></li>
                <li><a href="../../ranking.php">ランキングで探す</a></li>
                <li><a href="../../sale.php">セール対象で探す</a></li>
                <li><a href="../../diagnosis.php">診断から探す</a></li>
                <li><a href="../../advanced_search.php">詳細検索</a></li>
            </ul>

            <h2>カテゴリーから探す</h2>

            <ul class="category-list">
                <li class="category-item">
                    <a href="../tops.php">トップス</a>
                    <ul class="sub-category">
                        <li><a href="../tops/tshirt-cutsew.php">Tシャツ/カットソー</a></li>
                        <li><a href="../tops/shirt.php">シャツ/ブラウス</a></li>
                        <li><a href="../tops/poloshirt.php">ポロシャツ</a></li>
                        <li><a href="../tops/knit-sweater.php">ニット/セーター</a></li>
                        <li><a href="../tops/vest.php">ベスト</a></li>
                        <li><a href="../tops/parka.php">パーカー</a></li>
                        <li><a href="../tops/sweat.php">スウェット</a></li>
                        <li><a href="../tops/cardigan.php">カーディガン</a></li>
                        <li><a href="../tops/ensemble.php">アンサンブル</a></li>
                        <li><a href="../tops/jersey.php">ジャージ</a></li>
                        <li><a href="../tops/tanktop.php">タンクトップ</a></li>
                        <li><a href="../tops/camisole.php">キャミソール</a></li>
                        <li><a href="../tops/tubetops.php">チューブトップス</a></li>
                        <li><a href="../tops/auter-tops.php">その他トップス</a></li>
                    </ul>
                </li>
                <li class="category-item">
                    <a href="../jacket-outerwear.php">ジャケット/アウター</a>
                    <ul class="sub-category">
                        <li><a href="../jacket-outerwear/collarless-coat.php">ノーカラーコート</a></li>
                        <li><a href="../jacket-outerwear/collarless-jacket.php">ノーカラージャケット</a></li>
                        <li><a href="../jacket-outerwear/denim-jacket.php">デニムジャケット</a></li>
                        <li><a href="../jacket-outerwear/down-jacket.php">ダウンジャケット</a></li>
                        <li><a href="../jacket-outerwear/down-vest.php">ダウンベスト</a></li>
                        <li><a href="../jacket-outerwear/duffle-coat.php">ダッフルコート</a></li>
                        <li><a href="../jacket-outerwear/jacket.php">ブルゾン</a></li>
                        <li><a href="../jacket-outerwear/military-jacket.php">ミリタリージャケット</a></li>
                        <li><a href="../jacket-outerwear/mods-coat.php">モッズコート</a></li>
                        <li><a href="../jacket-outerwear/nylon-jacket.php">ナイロンジャケット</a></li>
                        <li><a href="../jacket-outerwear/riders-jacket.php">ライダースジャケット</a></li>
                        <li><a href="../jacket-outerwear/tailored-jacket.php">テーラードジャケット</a></li>
                        <li><a href="../jacket-outerwear/trench-coat.php">トレンチコート</a></li>
                        <li><a href="../jacket-outerwear/auter-jacket.php">その他アウター</a></li>
                    </ul>
                </li>
                <li class="category-item">
                    <a href="../pants.php">パンツ</a>
                    <ul class="sub-category">
                        <li><a href="../pants/cargo-pants.php">カーゴパンツ</a></li>
                        <li><a href="../pants/chino-pants.php">チノパン</a></li>
                        <li><a href="../pants/denim-pants.php">デニムパンツ</a></li>
                        <li><a href="../pants/slacks.php">スラックス</a></li>
                        <li><a href="../pants/sweat-pants.php">スウェットパンツ</a></li>
                        <li><a href="../pants/auter-pants.php">その他パンツ</a></li>
                    </ul>
                </li>
                <li class="category-item">
                    <a href="../skirt.php">スカート</a>
                    <ul class="sub-category">
                        <li><a href="mini-skirt.php">ミニスカート</a></li>
                        <li><a href="midi-skirt.php">ミディスカート</a></li>
                        <li><a href="long-skirt.php">ロングスカート</a></li>
                        <li><a href="denim-skirt.php">デニムスカート</a></li>
                    </ul>
                </li>
                <li class="category-item">
                    <a href="../onepiece.php">ワンピース</a>
                    <ul class="sub-category">
                        <li><a href="../onepiece/dress.php">ドレス</a></li>
                        <li><a href="../onepiece/jumper-skirt.php">ジャンパースカート</a></li>
                        <li><a href="../onepiece/onepiece-dress.php">ワンピース</a></li>
                        <li><a href="../onepiece/pants-dress.php">パンツドレス</a></li>
                        <li><a href="../onepiece/shirts-onepiece.php">シャツワンピース</a></li>
                        <li><a href="../onepiece/tunic.php">チュニック</a></li>
                    </ul>
                </li>
            </ul>
        </aside>
        <div class="products-section">

            <form method="get" class="sort-form">
                <select name="sort" id="sort" onchange="this.form.submit()">
                    <option value="sale" <?php echo isset($_GET['sort']) && $_GET['sort'] === 'sale' ? 'selected' : ''; ?>>おすすめ順</option>
                    <option value="favorite" <?php echo isset($_GET['sort']) && $_GET['sort'] === 'favorite' ? 'selected' : ''; ?>>人気順</option>
                    <option value="price_asc" <?php echo isset($_GET['sort']) && $_GET['sort'] === 'price_asc' ? 'selected' : ''; ?>>価格の安い順</option>
                    <option value="price_desc" <?php echo isset($_GET['sort']) && $_GET['sort'] === 'price_desc' ? 'selected' : ''; ?>>価格の高い順</option>
                    <option value="new_arrivals" <?php echo isset($_GET['sort']) && $_GET['sort'] === 'new_arrivals' ? 'selected' : ''; ?>>新着順</option>
                </select>

                <select name="brand" id="brand" onchange="this.form.submit()">
                    <option value="">すべて</option> <!-- デフォルトで「すべて」選択肢を表示 -->
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
            </form>
            <h1>デニムスカート 商品一覧</h1>
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
                                <a href="../../goods.php?shop_id=<?php echo htmlspecialchars($product['shop_id'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <img src="data:<?php echo $mimeType; ?>;base64,<?php echo $encodedImg; ?>" alt="goods img" class="product-image">

                                    <div>
                                        <strong><?php echo htmlspecialchars($product['goods'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                    </div>
                                    <div>新着: <?php echo htmlspecialchars($product['arrival'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div>価格: ¥<?php echo htmlspecialchars(number_format($product['price']), ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div>元値: ¥<?php echo htmlspecialchars(number_format($product['original_price']), ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div>セール: <?php echo htmlspecialchars($product['sale'], ENT_QUOTES, 'UTF-8'); ?>%</div>
                                    <div>人気: <?php echo htmlspecialchars($product['buy'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div>ブランド: <?php echo htmlspecialchars($product['brand_id'], ENT_QUOTES, 'UTF-8'); ?></div>
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