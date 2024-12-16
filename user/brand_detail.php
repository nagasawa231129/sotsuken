<?php
include "../../db_open.php"; // PDO接続のファイルをインクルード
include "../head.php";
include "../header.php";
echo "<link rel='stylesheet' href='header.css'>";
echo "<link rel='stylesheet' href='brand_detail.css'>";

if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];
} else {
    $userId = null;
}

$brand = isset($_GET['brand']) ? $_GET['brand'] : null;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'default';

// SQL文の初期設定
$sql = "SELECT shop.*, sale.*,brand.* FROM shop LEFT OUTER JOIN sale ON sale.sale_id = shop.sale_id 
    LEFT OUTER JOIN brand ON brand.brand_id = shop.brand_id
WHERE 1";

// ブランドフィルタがある場合の条件追加
$params = [];
if ($brand !== null) {
    $sql .= " AND shop.brand_id = :brand_id";
    $params[':brand_id'] = $brand;
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
        // デフォルトの並び順
        $sql .= " ORDER BY shop.sale_id DESC";
        break;
}

// SQL実行
$stmt = $dbh->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ブランドリストの取得
$sql = "SELECT * FROM brand";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$brands = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<title>ブランド詳細</title>

<body>
    <?php
    // ユーザーがログインしているかを確認
    if ($userId) {
        // ブランドごとにお気に入りをチェック
        $brandIds = array(); // 既に表示したブランドIDを記録するための配列

        // 商品ごとに処理
        foreach ($products as $product) {
            // ブランドIDがまだ表示されていない場合
            if (!in_array($product['brand_id'], $brandIds)) {
                // ブランドごとにお気に入りをチェック
                $sql_favorite_check = "SELECT * FROM favorite WHERE user_id = :user_id AND brand_id = :brand_id";
                $stmt_favorite_check = $dbh->prepare($sql_favorite_check);
                $stmt_favorite_check->bindParam(':user_id', $userId);
                $stmt_favorite_check->bindParam(':brand_id', $product['brand_id']);
                $stmt_favorite_check->execute();
                $is_favorite = $stmt_favorite_check->rowCount() > 0; // 既にお気に入りに追加されているかどうか

                // ブランドIDを記録して、次回のブランドに対して同じ処理をしないようにする
                $brandIds[] = $product['brand_id'];

                // ブランド名とお気に入りボタンの表示（ブランド名がボタンの左に表示されるように）
                echo "<div class='favorite-container'>";
                echo "<span class='brand-name'>{$product['brand_name']}</span>"; // ブランド名を表示
                if ($is_favorite) {
                    echo "<button class='favorite-button filled' data-brand-id='{$product['brand_id']}' data-user-id='{$userId}' title='お気に入り済み'>❤️</button>";
                } else {
                    echo "<button class='favorite-button' data-brand-id='{$product['brand_id']}' data-user-id='{$userId}' title='お気に入りに追加'>♡</button>";
                }
                echo "</div>"; // お気に入りボタンとブランド名を囲む
            }
        }
    } else {
        // ログインしていない場合はボタンを無効化
        echo "<button class='favorite-button' disabled>♡</button>"; // ログインしていない場合
    }
    ?>


    <div class="main-content">
        <aside class="sidebar">
            <h2 data-i18n="search"><?php echo $translations['Search'] ?></h2>
            <ul>
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
            </form>

            <h2><?php echo $translations['Product List'] ?></h2>
            <div class="products-container">
                <ul>
                    <?php if (count($products) > 0): ?>
                        <?php foreach ($products as $product): ?>
                            <li>
                                <a href="goods.php?shop_id=<?php echo htmlspecialchars($product['shop_id'], ENT_QUOTES, 'UTF-8'); ?>">
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
                                    <img src="data:<?php echo $mimeType; ?>;base64,<?php echo $encodedImg; ?>" alt="goods img" class="product-image">
                                    <div><?php echo htmlspecialchars($product['goods'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div><?php echo $translations['Price'] ?>: ¥<?php echo number_format($product['price']); ?></div>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p><?php echo $translations['No products from this brand are currently registered'] ?></p>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</body>

</html>




<script>
    // お気に入りボタンのクリックイベントを設定
    document.querySelectorAll('.favorite-button').forEach(button => {
        button.addEventListener('click', function() {
            const brandId = this.getAttribute('data-brand-id');
            const userId = this.getAttribute('data-user-id');

            // AJAXリクエストを作成
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'toggle_favorite.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            // リクエストの送信
            xhr.send(`user_id=${userId}&brand_id=${brandId}`);

            // リクエストが成功した場合の処理
            xhr.onload = function() {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        // ボタンの状態を更新
                        if (response.action === 'added') {
                            button.classList.add('filled');
                            button.innerHTML = '❤️'; // お気に入り追加
                            button.title = 'お気に入りから削除'; // ツールチップを更新
                        } else {
                            button.classList.remove('filled');
                            button.innerHTML = '♡'; // お気に入り削除
                            button.title = 'お気に入りに追加'; // ツールチップを更新
                        }
                    } else {
                        alert('エラーが発生しました。');
                    }
                }
            };
        });
    });
</script>