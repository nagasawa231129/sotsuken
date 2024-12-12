<?php
include "../../db_open.php"; // PDO接続のファイルをインクルード
include "../head.php";
include "../header.php";
echo "<link rel='stylesheet' href='header.css'>";
echo "<link rel='stylesheet' href='./category.css'>";

// ログインしていないときにエラーが出ない処理
if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];
} else {
    $userId = null;
}
?>

<!DOCTYPE html>
<?php
include "../head.php";
?>
<link rel="stylesheet" href="goods.css">
<?php
$shop_id = isset($_GET['shop_id']) ? $_GET['shop_id'] : '';
if ($shop_id) {
    $sql_update_look = "UPDATE shop SET look = look + 1 WHERE shop_id = :shop_id";
    $stmt_update_look = $dbh->prepare($sql_update_look);
    $stmt_update_look->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
    $stmt_update_look->execute();

    $sql = "SELECT shop.*, brand.brand_name, sale.sale FROM shop
    LEFT OUTER JOIN brand ON brand.brand_id = shop.brand_id
    LEFT OUTER JOIN sale ON sale.sale_id = shop.sale_id
    WHERE shop.shop_id = :shop_id";

    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':shop_id', $shop_id);
    $stmt->execute();
    $goodsResult = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($goodsResult) {
        // 商品情報を表示
        echo "<h1>{$goodsResult['goods']}</h1>";

        // ブランド名の表示
        echo "<p>".$translations['Brand']."：{$goodsResult['brand_name']}</p>";

        // 商品情報の他の部分を表示
        echo "<p>".$translations['Price']."：{$goodsResult['original_price']}</p>";

        // 商品価格がセール中の場合、セール価格を計算
        if ($goodsResult['sale_id']) {
            $sale_id = $goodsResult['sale_id'];
            // saleテーブルから割引率を取得
            $sql_sale = "SELECT sale.*, shop.* FROM sale LEFT OUTER JOIN shop ON shop.sale_id = sale.sale_id WHERE sale.sale_id = :sale_id";
            $stmt_sale = $dbh->prepare($sql_sale);
            $stmt_sale->bindParam(':sale_id', $sale_id);
            $stmt_sale->execute();
            $sale = $stmt_sale->fetch(PDO::FETCH_ASSOC);

            if ($sale) {
                // $discounted_price = ceil($goodsResult['original_price'] * (1 - $sale['sale'] / 100)); // 小数点切り上げ
                echo "<p>".$translations['Discounted Price']."：{$sale['price']}円</p>";
            }
        }

?>
        <div class="button-container">
            <form action="add_to_cart.php" method="POST">
                <input type="hidden" name="shop_id" value="<?php echo $goodsResult['shop_id']; ?>">
                <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
                <button type="submit"><?php echo $translations['Add to Cart'] ?></button>
            </form>

            <?php
            // お気に入りボタンを表示する前に、ユーザーがその商品をお気に入りに登録しているかをチェック
            if ($userId) {
                // 商品がすでにお気に入りに追加されているかを確認
                $sql_favorite_check = "SELECT * FROM favorite WHERE user_id = :user_id AND shop_id = :shop_id";
                $stmt_favorite_check = $dbh->prepare($sql_favorite_check);
                $stmt_favorite_check->bindParam(':user_id', $userId);
                $stmt_favorite_check->bindParam(':shop_id', $goodsResult['shop_id']);
                $stmt_favorite_check->execute();
                $is_favorite = $stmt_favorite_check->rowCount() > 0; // 既にお気に入りに追加されているかどうか

                // お気に入りボタンの表示
                if ($is_favorite) {
                    echo "<button class='favorite-button filled' data-shop-id='{$goodsResult['shop_id']}' data-user-id='{$userId}' title='お気に入り済み'>❤️</button>";
                } else {
                    echo "<button class='favorite-button' data-shop-id='{$goodsResult['shop_id']}' data-user-id='{$userId}' title='お気に入りに追加'>♡</button>";
                }
            } else {
                echo "<button class='favorite-button' disabled>♡</button>"; // ログインしていない場合の表示
            }

            ?>
        </div>

        <script>
            // お気に入りボタンのクリックイベントを設定
            document.querySelectorAll('.favorite-button').forEach(button => {
                button.addEventListener('click', function() {
                    const shopId = this.getAttribute('data-shop-id');
                    const userId = this.getAttribute('data-user-id');

                    // AJAXリクエストを作成
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', 'toggle_favorite.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                    // リクエストの送信
                    xhr.send(`user_id=${userId}&shop_id=${shopId}`);

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

<?php
        // 商品IDの取得
        $shop_id = isset($_GET['shop_id']) ? $_GET['shop_id'] : '';

        // サムネイル画像をshopテーブルから取得
        $sql_thumbnail = "SELECT thumbnail FROM shop WHERE shop_id = :shop_id";
        $stmt_thumbnail = $dbh->prepare($sql_thumbnail);
        $stmt_thumbnail->bindParam(':shop_id', $shop_id);
        $stmt_thumbnail->execute();
        $thumbnail_result = $stmt_thumbnail->fetch(PDO::FETCH_ASSOC);

        // サムネイル画像が取得できた場合
        if ($thumbnail_result) {
            $thumbnailImgBlob = $thumbnail_result['thumbnail'];

            // BLOBデータからMIMEタイプを取得
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeTypeThumbnail = $finfo->buffer($thumbnailImgBlob);  // BLOBデータを解析

            // Base64にエンコードして表示
            $encodedThumbnailImg = base64_encode($thumbnailImgBlob);
            echo "<img src='data:{$mimeTypeThumbnail};base64,{$encodedThumbnailImg}' alt='サムネイル画像' id='main-thumbnail' class='main-thumbnail'>";
        } else {
            echo "<p>サムネイル画像が見つかりません。</p>";
        }

        // サムネイル画像（shopテーブル）の追加取得
        $sql_shop_images = "SELECT thumbnail FROM shop WHERE shop_id = :shop_id";
        $stmt_shop_images = $dbh->prepare($sql_shop_images);
        $stmt_shop_images->bindParam(':shop_id', $shop_id);
        $stmt_shop_images->execute();

        // サムネイル画像を追加表示（もしあれば）
        if ($stmt_shop_images->rowCount() > 0) {
            echo "<div class='sub-images'>";
            while ($shop_image = $stmt_shop_images->fetch(PDO::FETCH_ASSOC)) {
                $shopImgBlob = $shop_image['thumbnail'];

                // BLOBデータからMIMEタイプを取得
                $mimeTypeShop = $finfo->buffer($shopImgBlob);  // サムネイルのMIMEタイプを解析

                // Base64にエンコードして表示
                $encodedShopImg = base64_encode($shopImgBlob);
                echo "<img src='data:{$mimeTypeShop};base64,{$encodedShopImg}' alt='サムネイル画像' class='sub-thumbnail' onclick='changeThumbnail(this)'>";
            }
        } else {
            echo "<p>サムネイル画像はありません。</p>";
        }

        // サブ画像をimageテーブルから取得
        $sql_sub_images = "SELECT img FROM image WHERE shop_id = :shop_id";
        $stmt_sub_images = $dbh->prepare($sql_sub_images);
        $stmt_sub_images->bindParam(':shop_id', $shop_id);
        $stmt_sub_images->execute();

        // サブ画像があれば表示
        if ($stmt_sub_images->rowCount() > 0) {
            while ($sub_image = $stmt_sub_images->fetch(PDO::FETCH_ASSOC)) {
                $subImgBlob = $sub_image['img'];

                // BLOBデータからMIMEタイプを取得
                $mimeTypeSub = $finfo->buffer($subImgBlob);  // サブ画像のMIMEタイプを解析

                // Base64にエンコードして表示
                $encodedSubImg = base64_encode($subImgBlob);
                echo "<img src='data:{$mimeTypeSub};base64,{$encodedSubImg}' alt='サブ画像' class='sub-thumbnail' onclick='changeThumbnail(this)'>";
            }
            echo "</div>";
        } else {
            echo "<p>サブ画像はありません。</p>";
        }
        echo "</div>";
    } else {
        echo "<p>該当する商品が見つかりません。</p>";
    }
} else {
    echo "<p>商品IDが指定されていません。</p>";
}
?>
<script>
    // サムネイルクリックでメイン画像を変更
    function changeThumbnail(subImgElement) {
        var mainThumbnail = document.getElementById('main-thumbnail');
        mainThumbnail.src = subImgElement.src; // メイン画像をサブ画像に変更
    }
</script>

<body>
    <nav class="tabs">
        <div class="tab-button active" data-target="info"><?php echo $translations['Description'] ?></div>
        <div class="tab-button" data-target="size"><?php echo $translations['Size'] ?></div>
        <div class="tab-button" data-target="review"><?php echo $translations['Review'] ?></div>
    </nav>

    <div id="info" class="tab-content active-tab">
        <?php
        if (isset($goodsResult['exp'])) {
            echo "<p>{$goodsResult['exp']}</p>";
        } else {
            echo "<p>商品説明はまだありません。</p>";
        }
        ?>
    </div>
    <div id="size" class="tab-content">
        <?php
        // 商品IDを取得
        if ($shop_id) {
            // サイズ情報を取得するクエリ
            $sql = "SELECT size.size FROM shop LEFT OUTER JOIN size ON size.size_id = shop.size WHERE shop_id = :shop_id";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
            $stmt->execute();
            $sizes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $sizes = [];
        }
        ?>

        <?php if (!empty($sizes)): ?>
            <ul>
                <?php foreach ($sizes as $size): ?>
                    <li>
                        <?php echo $translations['Size'] ?>: <?php echo htmlspecialchars($size['size'], ENT_QUOTES, 'UTF-8'); ?>
                        <?php if (!empty($size['description'])): ?>
                            （説明: <?php echo htmlspecialchars($size['description'], ENT_QUOTES, 'UTF-8'); ?>）
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>この店舗にはサイズ情報が登録されていません。</p>
        <?php endif; ?>
    </div>

    <div id="review" class="tab-content">
        <?php
        // 商品ページに関連するレビューを表示する部分
        if ($shop_id) {
            // reviewsテーブルからshop_idに関連するレビューを取得
            $sql_reviews = "SELECT reviews.*, user.display_name 
            FROM reviews 
            LEFT JOIN user ON reviews.user_id = user.user_id 
            WHERE reviews.shop_id = :shop_id";
            $stmt_reviews = $dbh->prepare($sql_reviews);
            $stmt_reviews->bindParam(':shop_id', $shop_id);
            $stmt_reviews->execute();

            // レビューの平均評価を計算
            $sql_avg_rating = "SELECT AVG(rate) AS average_rating FROM reviews WHERE shop_id = :shop_id";
            $stmt_avg_rating = $dbh->prepare($sql_avg_rating);
            $stmt_avg_rating->bindParam(':shop_id', $shop_id);
            $stmt_avg_rating->execute();
            $avg_rating = $stmt_avg_rating->fetch(PDO::FETCH_ASSOC);
            $average_rating = $avg_rating['average_rating']; // 小数点以下もそのまま使用

            // 平均評価の表示（☆で表示）
            echo "<h3>全体の評価: </h3>";
            echo "<div class='star-rating'>";
            $full_stars = floor($average_rating); // 完全な星の数
            $half_star = ($average_rating - $full_stars) >= 0.5 ? 1 : 0; // 半分の星が必要かどうか
            $empty_stars = 5 - $full_stars - $half_star; // 空の星の数

            // 完全な星の表示
            for ($i = 0; $i < $full_stars; $i++) {
                echo "<span class='star selected'>&#9733;</span>";
            }

            // 半分の星の表示
            if ($half_star) {
                echo "<span class='star half-selected'>&#9733;</span>";
            }

            // 空の星の表示
            for ($i = 0; $i < $empty_stars; $i++) {
                echo "<span class='star'>&#9733;</span>";
            }
            echo "</div>";

            // レビューが存在する場合
            if ($stmt_reviews->rowCount() > 0) {
                echo "<h3>レビュー</h3>";
                while ($review = $stmt_reviews->fetch(PDO::FETCH_ASSOC)) {
                    $reviewer_name = $review['display_name']; // レビュアーの名前
                    $rating = $review['rate']; // レビューの評価 (1～5など)
                    $comment = $review['review_content']; // レビュー内容

                    // レビューの表示
                    echo "<div class='review-card'>";
                    echo "<p><strong>{$reviewer_name}</strong>さんの評価: ";

                    // 個別の評価も星で表示
                    echo "<div class='star-rating'>";
                    $full_stars_review = floor($rating); // 完全な星の数
                    $half_star_review = ($rating - $full_stars_review) >= 0.5 ? 1 : 0; // 半分の星が必要かどうか
                    $empty_stars_review = 5 - $full_stars_review - $half_star_review; // 空の星の数

                    // 完全な星の表示
                    for ($i = 0; $i < $full_stars_review; $i++) {
                        echo "<span class='star selected'>&#9733;</span>";
                    }

                    // 半分の星の表示
                    if ($half_star_review) {
                        echo "<span class='star half-selected'>&#9733;</span>";
                    }

                    // 空の星の表示
                    for ($i = 0; $i < $empty_stars_review; $i++) {
                        echo "<span class='star'>&#9733;</span>";
                    }
                    echo "</div>";

                    echo "</p>";
                    echo "<p>{$comment}</p>";
                    echo "</div>";
                }
            } else {
                echo "<p>まだレビューはありません。</p>";
            }
        }
        ?>
    </div>



    </div>

    <script>
        // タブボタンのクリックイベント
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', () => {
                // すべてのタブコンテンツとタブボタンのactiveクラスをリセット
                document.querySelectorAll('.tab-content').forEach(tab => {
                    tab.classList.remove('active-tab');
                });
                document.querySelectorAll('.tab-button').forEach(tabButton => {
                    tabButton.classList.remove('active');
                });

                // クリックされたタブボタンと対応するタブコンテンツにactiveクラスを追加
                const targetId = button.getAttribute('data-target');
                document.getElementById(targetId).classList.add('active-tab');
                button.classList.add('active');
            });
        });
    </script>
</body>

</html>