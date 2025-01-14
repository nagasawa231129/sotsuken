<?php
include "../../db_open.php"; // PDO接続のファイルをインクルード
include "../head.php";
include "../header.php";
echo "<link rel='stylesheet' href='header.css'>";
echo "<link rel='stylesheet' href='./category/category.css'>";

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

    $sql = "SELECT shop.*, brand.brand_name, sale.sale, `group`.* FROM shop
    LEFT OUTER JOIN brand ON brand.brand_id = shop.brand_id
    LEFT OUTER JOIN sale ON sale.sale_id = shop.sale_id
    LEFT OUTER JOIN `group` ON `group`.shop_id = shop.shop_id
    WHERE shop.shop_id = :shop_id";

    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':shop_id', $shop_id);
    $stmt->execute();
    $goodsResult = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($goodsResult) {
        // var_dump($goodsResult['shop_group']);
        // 商品情報を表示
        echo "<h1>{$goodsResult['goods']}</h1>";

        // ブランド名の表示
        $brand_url = "brand_detail.php?brand=" . $goodsResult['brand_id'];
        echo "<a href='$brand_url' class='brand-link' {$goodsResult['brand_id']}'>
                    <h2>{$goodsResult['brand_name']}</h2>
                </a>";

        // 商品情報の他の部分を表示
        echo "<p>" . $translations['Price'] . "：{$goodsResult['original_price']}</p>";

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
                echo "<p>" . $translations['Discounted Price'] . "：{$sale['price']}円</p>";
            }
        }

?>
        <div class="main-content">

            <aside class="button-container">
                <?php
                // 商品のサイズ・カラー情報を取得
                $sql_variations = "
    SELECT shop.*, size.size, color.color, g.shop_group
    FROM shop
    INNER JOIN size ON size.size_id = shop.size
    INNER JOIN color ON color.color_id = shop.color
    LEFT OUTER JOIN `group` AS g ON g.shop_id = shop.shop_id
    WHERE g.shop_group = :shop_group
";

                $stmt_variations = $dbh->prepare($sql_variations);
                $stmt_variations->bindParam(':shop_group', $goodsResult['shop_group'], PDO::PARAM_STR);
                $stmt_variations->execute();
                $variations = $stmt_variations->fetchAll(PDO::FETCH_ASSOC);

                // var_dump($variations);

                // サイズとカラーごとに表示
                foreach ($variations as $variation) {
                    $size = $variation['size'];
                    $color = $variation['color'];
                    $shop_id = $variation['shop_id'];
                    $shop_group = $variation['shop_group'];

                    // お気に入り状態を確認
                    $is_favorite = false;
                    if ($userId) {
                        $sql_favorite_check = "
                        SELECT favorite.* FROM favorite
                        WHERE user_id = :user_id AND shop_id = :shop_id AND size = :size AND color = :color
                        ";
                        $stmt_favorite_check = $dbh->prepare($sql_favorite_check);
                        $stmt_favorite_check->bindParam(':user_id', $userId);
                        $stmt_favorite_check->bindParam(':shop_id', $shop_id);
                        $stmt_favorite_check->bindParam(':size', $size);
                        $stmt_favorite_check->bindParam(':color', $color);
                        $stmt_favorite_check->execute();
                        $is_favorite = $stmt_favorite_check->rowCount() > 0;
                    }

                    // サイズとカラーごとの「カートに入れる」ボタンと「お気に入り」ボタンを表示
                    echo "<div class='button-row'>";
                    echo "<p>{$size}サイズの{$color}</p>";

                    // カートに入れるボタン
                    echo "
                    <form action='add_to_cart.php' method='POST'>
                    <input type='hidden' name='shop_id' value='{$shop_id}'>
                    <input type='hidden' name='user_id' value='{$userId}'>
                    <button class='cart-button' type='submit'>カートに入れる</button>
                    </form>
                    ";

                    if ($userId) {
                        if ($is_favorite) {
                            echo "<button class='favorite-button filled' data-shop-id='{$shop_id}' data-user-id='{$userId}' data-size='{$size}' data-color='{$color}' title='お気に入り済み'>❤️</button>";
                        } else {
                            echo "<button class='favorite-button' data-shop-id='{$shop_id}' data-user-id='{$userId}' data-size='{$size}' data-color='{$color}' title='お気に入りに追加'>♡</button>";
                        }
                    } else {
                        echo "<button class='favorite-button' disabled>♡</button>";
                    }
                    echo "</div>";
                }
                ?>
            </aside>

            <script>
                // お気に入りボタンのクリックイベントを設定
                document.querySelectorAll('.favorite-button').forEach(button => {
                    button.addEventListener('click', function() {
                        const shopId = this.getAttribute('data-shop-id');
                        const userId = this.getAttribute('data-user-id');
                        const size = this.getAttribute('data-size');
                        const color = this.getAttribute('data-color');

                        // AJAXリクエストを作成
                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', 'toggle_favorite.php', true);
                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                        // リクエストの送信
                        xhr.send(`user_id=${userId}&shop_id=${shopId}&size=${size}&color=${color}`);

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
                            } else {
                                alert('リクエストに失敗しました。');
                            }
                        };
                    });
                });
            </script>
    <?php
        // 商品IDとグループIDの取得
        $shop_id = isset($_GET['shop_id']) ? $_GET['shop_id'] : '';
        $shop_group = isset($_GET['shop_group']) ? $_GET['shop_group'] : '';
        if ($shop_id && $shop_group) {
            // SQLクエリを定義
            $sql_thumbnail = "
            SELECT s.thumbnail 
            FROM shop AS s
            LEFT JOIN `group` AS g ON g.shop_id = s.shop_id
            WHERE s.shop_id = :shop_id AND g.shop_group = :shop_group
        ";
            if (empty($sql_thumbnail)) {
                echo "<p>SQLクエリが空です。</p>";
            }

            // サムネイル画像をshopテーブルから取得
            $stmt_thumbnail = $dbh->prepare($sql_thumbnail);
            $stmt_thumbnail->bindParam(':shop_id', $shop_id);
            $stmt_thumbnail->bindParam(':shop_group', $shop_group);
            $stmt_thumbnail->execute();
            $thumbnail_result = $stmt_thumbnail->fetch(PDO::FETCH_ASSOC);

            // サムネイル画像が取得できた場合
            if ($thumbnail_result) {
                $thumbnailImgBlob = $thumbnail_result['thumbnail'];

                // finfoをインスタンス化
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mimeTypeThumbnail = $finfo->buffer($thumbnailImgBlob);  // BLOBデータを解析

                // Base64にエンコードして表示
                $encodedThumbnailImg = base64_encode($thumbnailImgBlob);
                echo "<img src='data:{$mimeTypeThumbnail};base64,{$encodedThumbnailImg}' alt='サムネイル画像' id='main-thumbnail' class='main-thumbnail'>";
            } else {
                echo "<p>サムネイル画像が見つかりません。</p>";
            }
        } else {
            echo "<p>商品IDまたはグループIDが指定されていません。</p>";
        }



        // サムネイル画像（shopテーブル）の追加取得
        $sql_shop_images = "
        SELECT DISTINCT shop.thumbnail
        FROM shop
        LEFT JOIN `group` ON `group`.shop_id = shop.shop_id
        WHERE `group`.shop_group = :shop_group
    ";


        $stmt_shop_images = $dbh->prepare($sql_shop_images);
        // $stmt_shop_images->bindParam(':shop_id', $shop_id);
        $stmt_shop_images->bindParam(':shop_group', $shop_group);
        // $stmt_shop_images->bindParam(':color', $color);
        $stmt_shop_images->execute();

        // サムネイル画像を追加表示（もしあれば）
        if ($stmt_shop_images->rowCount() > 0) {
            echo "<div class='sub-images'>";
            while ($shop_image = $stmt_shop_images->fetch(PDO::FETCH_ASSOC)) {
                $shopImgBlob = $shop_image['thumbnail'];

                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mimeTypeShop = $finfo->buffer($shopImgBlob);  // サムネイルのMIMEタイプを解析

                $encodedShopImg = base64_encode($shopImgBlob);
                echo "<img src='data:{$mimeTypeShop};base64,{$encodedShopImg}' alt='サムネイル画像' class='sub-thumbnail' onclick='changeThumbnail(this)'>";
            }
            echo "</div>";
        } else {
            echo "<p>サムネイル画像はありません。</p>";
        }


        // 特定の shop_id を取得
        $sql_shop_id = "
    SELECT s.shop_id
    FROM shop AS s
    LEFT OUTER JOIN `group` AS g ON g.shop_id = s.shop_id
    LEFT JOIN color AS c ON s.color = c.color_id
    WHERE g.shop_group = :shop_group AND LOWER(c.color) = LOWER(:color)
    LIMIT 1
";

        $stmt_shop_id = $dbh->prepare($sql_shop_id);
        $stmt_shop_id->bindParam(':shop_group', $shop_group, PDO::PARAM_INT);
        $stmt_shop_id->bindParam(':color', $color, PDO::PARAM_STR);
        $stmt_shop_id->execute();


        // shop_id を取得
        $shop_id = $stmt_shop_id->fetchColumn();

        if ($shop_id) {
            // サブ画像を取得
            $sql_sub_images = "
            SELECT i.img
            FROM image AS i
            LEFT JOIN `group` AS g ON g.shop_id = i.shop_id
            WHERE g.shop_group = :shop_group
        ";

            $stmt_sub_images = $dbh->prepare($sql_sub_images);
            $stmt_sub_images->bindParam(':shop_group', $shop_group, PDO::PARAM_INT);
            $stmt_sub_images->execute();

            // $sub_images = $stmt_sub_images->fetchAll(PDO::FETCH_ASSOC);


            // サブ画像があれば表示
            if ($stmt_sub_images->rowCount() > 0) {
                echo "<div class='sub-images'>";
                while ($sub_image = $stmt_sub_images->fetch(PDO::FETCH_ASSOC)) {
                    $subImgBlob = $sub_image['img'];

                    // MIMEタイプを取得
                    $finfo = new finfo(FILEINFO_MIME_TYPE);
                    $mimeTypeSub = $finfo->buffer($subImgBlob);

                    // Base64にエンコードして画像を表示
                    $encodedSubImg = base64_encode($subImgBlob);
                    echo "<img src='data:{$mimeTypeSub};base64,{$encodedSubImg}' alt='サブ画像' class='sub-thumbnail' onclick='changeThumbnail(this)'>";
                }
                echo "</div>";
            } else {
                echo "<p>サブ画像はありません。</p>";
            }
        } else {
            echo "<p>該当する商品が見つかりません。</p>";
        }
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
        </div>

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
                if ($shop_id && $shop_group) {
                    // カラーとサイズ情報を取得するクエリ
                    $sql = "SELECT shop.color, size.size, color.ja_color
                FROM shop
                LEFT OUTER JOIN size ON size.size_id = shop.size
                LEFT OUTER JOIN color ON color.color_id = shop.color
                LEFT OUTER JOIN `group` ON `group`.shop_id = shop.shop_id
                WHERE `group`.shop_group = :shop_group
                ORDER BY shop.color";

                    // クエリの準備と実行
                    $stmt = $dbh->prepare($sql);
                    $stmt->bindParam(':shop_group', $shop_group, PDO::PARAM_INT);
                    $stmt->execute();

                    // 結果を取得
                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if ($results) {
                        // カラーごとにグループ化
                        $groupedColors = [];
                        foreach ($results as $row) {
                            $color = $row['ja_color'];
                            $size = $row['size'];

                            if (!isset($groupedColors[$color])) {
                                $groupedColors[$color] = [];
                            }
                            $groupedColors[$color][] = $size;
                        }

                        // カラーとサイズを表示
                        foreach ($groupedColors as $color => $sizes) {
                            echo "<h3>" . htmlspecialchars($translations['Color'] ?? 'Color', ENT_QUOTES, 'UTF-8') . ": " . htmlspecialchars($color, ENT_QUOTES, 'UTF-8') . "</h3>";
                            echo "<ul>";
                            foreach ($sizes as $size) {
                                echo "<li>" . htmlspecialchars($translations['Size'] ?? 'Size', ENT_QUOTES, 'UTF-8') . ": " . htmlspecialchars($size, ENT_QUOTES, 'UTF-8') . "</li>";
                            }
                            echo "</ul>";
                        }
                    } else {
                        echo "<p>この商品のカラーとサイズ情報は見つかりませんでした。</p>";
                    }
                } else {
                    echo "<p>商品情報が無効です。</p>";
                }
                ?>
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
            <script>
                document.querySelectorAll('.tab-button').forEach(button => {
                    button.addEventListener('click', () => {
                        // すべてのタブボタンからactiveクラスを削除
                        document.querySelectorAll('.tab-button').forEach(tabButton => {
                            tabButton.classList.remove('active');
                        });

                        // すべてのタブコンテンツからactive-tabクラスを削除
                        document.querySelectorAll('.tab-content').forEach(tab => {
                            tab.classList.remove('active-tab');
                        });

                        // クリックされたタブボタンにactiveクラスを追加
                        button.classList.add('active');

                        // 対応するタブコンテンツにactive-tabクラスを追加
                        const targetId = button.getAttribute('data-target');
                        const targetTab = document.getElementById(targetId);
                        if (targetTab) {
                            targetTab.classList.add('active-tab');
                        }
                    });
                });
            </script>
        </body>

        </html>