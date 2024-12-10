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
            <h2 data-i18n="search"><?php echo $translations['search'] ?></h2>
            <ul>
                <li><a href="brand.php" data-i18n="search_by_brand"><?php echo $translations['search_by_brand'] ?></a></li>
                <li><a href="category/category.php" data-i18n="search_by_category"><?php echo $translations['search_by_category'] ?></a></li>
                <li><a href="ranking.php" data-i18n="search_by_ranking"><?php echo $translations['search_by_ranking'] ?></a></li>
                <li><a href="sale.php" data-i18n="search_by_sale"><?php echo $translations['search_by_sale'] ?></a></li>
                <li><a href="diagnosis.php" data-i18n="search_by_diagnosis"><?php echo $translations['search_by_diagnosis'] ?></a></li>
                <li><a href="advanced_search.php" data-i18n="advanced_search"><?php echo $translations['advanced_search'] ?></a></li>
            </ul>

            <h2 data-i18n="categories_from"><?php echo $translations['search_by_category'] ?></h2>

            <ul class="category-list">
                <li class="category-item">
                    <a href="./category/tops.php" data-i18n="tops"><?php echo $translations['tops'] ?></a>
                    <ul class="sub-category">
                        <li><a href="./category/tops/tshirt-cutsew.php" data-i18n="Tshirt-cutsew"><?php echo $translations['tshirt-cutsew'] ?></a></li>
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
                    <a href="./category/jacket.php" data-i18n="jacket/outer"><?php echo $translations['outerwear'] ?></a>
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
                    <a href="./category/pants.php" data-i18n="pants"><?php echo $translations['pants'] ?></a>
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
                    <a href="./category/skirt.php" data-i18n="skirt"><?php echo $translations['skirt'] ?></a>
                    <ul class="sub-category">
                        <li><a href="./category/skirt/mini-skirt.php" data-i18n="mini-skirt">ミニスカート</a></li>
                        <li><a href="./category/skirt/midi-skirt.php" data-i18n="midi-skirt">ミディスカート</a></li>
                        <li><a href="./category/skirt/long-skirt.php" data-i18n="long-skirt">ロングカート</a></li>
                        <li><a href="./category/skirt/denim-skirt.php" data-i18n="denim-skirt">デニムスカート</a></li>
                    </ul>
                </li>
                <li class="category-item">
                    <a href="./category/onepiece.php" data-i18n="onepiece"><?php echo $translations['onepiece'] ?></a>
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
        <div class="products-wrapper">
            <h1>人気商品ランキング</h1>
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
                                <p class="brand-name">ブランド: <?= htmlspecialchars($item['brand_name']) ?></p>
                                <p class="price">価格: <?= (int)$item['original_price'] ?> 円</p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>ランキングデータがありません。</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>