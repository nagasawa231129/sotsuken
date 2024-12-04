<?php
include "../../../db_open.php"; // PDO接続のファイルをインクルード
include "../../head.php";
include "../../header.php";
echo "<link rel='stylesheet' href='../header.css'>";
// echo "<link rel='stylesheet' href='category.css'>";
echo "<link rel='stylesheet' href='tops.css'>";

$sort = isset($_GET['sort']) ? $_GET['sort'] : 'default';


$sql = "SELECT shop.*, sale.*
        FROM shop 
        LEFT OUTER JOIN sale ON sale.sale_id = shop.sale_id
        WHERE shop.category_id = '2'";

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
        //セール商品
        $sql .= " ORDER BY shop.sale_id ASC";
        break;
}

$stmt = $dbh->query($sql);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<title>その他トップス商品一覧</title>

<body>
    <div class="main-content">
        <aside class="sidebar">
            <h2>探す</h2>
            <ul>
                <li><a href="../brand.php">ブランドで探す</a></li>
                <li><a href="category.php">カテゴリ―で探す</a></li>
                <li><a href="../ranking.php">ランキングで探す</a></li>
                <li><a href="../sale.php">セール対象で探す</a></li>
                <li><a href="../diagnosis.php">診断から探す</a></li>
                <li><a href="../advanced_search.php">詳細検索</a></li>
            </ul>

            <h2>カテゴリーから探す</h2>

            <ul class="category-list">
                <li class="category-item">
                    <a href="..y/tops.php">トップス</a>
                    <ul class="sub-category">
                        <li><a href="tshirt-cutsew.php">Tシャツ/カットソー</a></li>
                        <li><a href="shirt.php">シャツ/ブラウス</a></li>
                        <li><a href="poloshirt.php">ポロシャツ</a></li>
                        <li><a href="knit-sweater.php">ニット/セーター</a></li>
                        <li><a href="vest.php">ベスト</a></li>
                        <li><a href="parka.php">パーカー</a></li>
                        <li><a href="sweat.php">スウェット</a></li>
                        <li><a href="cardigan.php">カーディガン</a></li>
                        <li><a href="ensemble.php">アンサンブル</a></li>
                        <li><a href="jersey.php">ジャージ</a></li>
                        <li><a href="tanktop.php">タンクトップ</a></li>
                        <li><a href="camisole.php">キャミソール</a></li>
                        <li><a href="tubetops.php">チューブトップス</a></li>
                        <li><a href="auter-tops.php">その他トップス</a></li>
                    </ul>
                </li>
                <li class="category-item">
                    <a href="jacket-outerwear.php">ジャケット/アウター</a>
                    <ul class="sub-category">
                        <li><a href="jacket-outerwear/collarless-coat.php">ノーカラーコート</a></li>
                        <li><a href="jacket-outerwear/collarless-jacket.php">ノーカラージャケット</a></li>
                        <li><a href="jacket-outerwear/denim-jacket.php">デニムジャケット</a></li>
                        <li><a href="jacket-outerwear/down-jacket.php">ダウンジャケット</a></li>
                        <li><a href="jacket-outerwear/down-vest.php">ダウンベスト</a></li>
                        <li><a href="jacket-outerwear/duffle-coat.php">ダッフルコート</a></li>
                        <li><a href="jacket-outerwear/jacket.php">ブルゾン</a></li>
                        <li><a href="jacket-outerwear/military-jacket.php">ミリタリージャケット</a></li>
                        <li><a href="jacket-outerwear/mods-coat.php">モッズコート</a></li>
                        <li><a href="jacket-outerwear/nylon-jacket.php">ナイロンジャケット</a></li>
                        <li><a href="jacket-outerwear/riders-jacket.php">ライダースジャケット</a></li>
                        <li><a href="jacket-outerwear/tailored-jacket.php">テーラードジャケット</a></li>
                        <li><a href="jacket-outerwear/trench-coat.php">トレンチコート</a></li>
                        <li><a href="jacket-outerwear/auter-jacket.php">その他アウター</a></li>
                    </ul>
                </li>
                <li class="category-item">
                    <a href="pants.php">パンツ</a>
                    <ul class="sub-category">
                        <li><a href="pants/cargo-pants.php">カーゴパンツ</a></li>
                        <li><a href="pants/chino-pants.php">チノパン</a></li>
                        <li><a href="pants/denim-pants.php">デニムパンツ</a></li>
                        <li><a href="pants/slacks.php">スラックス</a></li>
                        <li><a href="pants/sweat-pants.php">スウェットパンツ</a></li>
                        <li><a href="pants/auter-pants.php">その他パンツ</a></li>
                    </ul>
                </li>
                <li class="category-item">
                    <a href="skirt.php">スカート</a>
                    <ul class="sub-category">
                        <li><a href="skirt/mini-skirt.php">ミニスカート</a></li>
                        <li><a href="skirt/midi-skirt.php">ミディスカート</a></li>
                        <li><a href="skirt/long-skirt.php">ロングカート</a></li>
                        <li><a href="skirt/denim-skirt.php">デニムスカート</a></li>
                    </ul>
                </li>
                <li class="category-item">
                    <a href="onepiece.php">ワンピース</a>
                    <ul class="sub-category">
                        <li><a href="onepiece/dress.php">ドレス</a></li>
                        <li><a href="onepiece/jumper-skirt.php">ジャンパースカート</a></li>
                        <li><a href="onepiece/onepiece-dress.php">ワンピース</a></li>
                        <li><a href="onepiece/pants-dress.php">パンツドレス</a></li>
                        <li><a href="onepiece/shirts-onepiece.php">シャツワンピース</a></li>
                        <li><a href="onepiece/tunic.php">チュニック</a></li>
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
            </form>

            <h2>ジャケット/アウター 商品一覧</h2>
            <div class="products-container">
                <ul>
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                            <li>
                                <!-- 商品の詳細ページへのリンク -->
                                <a href="../goods.php?shop_id=<?php echo htmlspecialchars($product['shop_id'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <div>
                                        <strong><?php echo htmlspecialchars($product['goods'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                    </div>
                                    <div>新着: <?php echo htmlspecialchars($product['arrival'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div>価格: ¥<?php echo htmlspecialchars(number_format($product['price']), ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div>元値: ¥<?php echo htmlspecialchars(number_format($product['original_price']), ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div>セール: <?php echo htmlspecialchars($product['sale'], ENT_QUOTES, 'UTF-8'); ?>%</div>
                                    <div>人気: <?php echo htmlspecialchars($product['buy'], ENT_QUOTES, 'UTF-8'); ?></div>
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