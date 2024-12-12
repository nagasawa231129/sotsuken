<?php
include "../../db_open.php"; // PDO接続のファイルをインクルード
include "../head.php";
include "../header.php";
echo "<link rel='stylesheet' href='header.css'>";
echo "<link rel='stylesheet' href='favorite.css'>";

if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];
} else {
    header("Location: login.php"); // 再読み込みして設定完了
    exit;
}
?>

<h2><?php echo $translations['Favorites Items']; ?></h2>

<!-- タブメニュー -->
<div class="tab-container">
    <button class="tab" onclick="openTab(event, 'productFavorites')"><?php echo $translations['Favorites Items']; ?></button>
    <button class="tab" onclick="openTab(event, 'brandFavorites')"><?php echo $translations['Favorites Brands']; ?></button>
</div>

<!-- 商品のお気に入り -->
<div id="productFavorites" class="tab-content">
    <?php
    if (isset($_SESSION['id'])) {
        $userId = $_SESSION['id'];
    } else {
        header("Location: login.php"); // 再読み込みして設定完了
        exit;
    }

    // 商品のお気に入りを取得
    $sql_favorite = "SELECT DISTINCT shop.shop_id, shop.goods, shop.original_price, shop.price, brand.brand_name, shop.thumbnail
                 FROM favorite
                 INNER JOIN shop ON favorite.shop_id = shop.shop_id 
                 LEFT JOIN brand ON shop.brand_id = brand.brand_id
                 WHERE favorite.user_id = :user_id";


    $stmt_favorite = $dbh->prepare($sql_favorite);
    $stmt_favorite->bindParam(':user_id', $userId);
    $stmt_favorite->execute();

    if ($stmt_favorite->rowCount() > 0) {
        echo "<div class='favorite-products'>";
        while ($favorite = $stmt_favorite->fetch(PDO::FETCH_ASSOC)) {
            $imgBlob = $favorite['thumbnail'] ?? null;
            $imgSrc = !empty($imgBlob) ? "data:image/jpeg;base64," . base64_encode($imgBlob) : "default_image.jpg"; // 画像がない場合はデフォルト画像

            echo "
            <div class='favorite-card'>
                <a href='goods.php?shop_id={$favorite['shop_id']}'>
                    <img src='{$imgSrc}' alt='{$favorite['goods']}' class='favorite-image'>
                    <div class='favorite-info'>
                        <h2>{$favorite['goods']}</h2>
                        <p>" . $translations['Brand'] . ": {$favorite['brand_name']}</p>
                        <p>" . $translations['Discounted Price'] . ": {$favorite['original_price']}円</p>
                        <p>" . $translations['Price'] . ": {$favorite['price']}円</p>
                    </div>
                </a>
            </div>";
        }
        echo "</div>";
    } else {
        echo "<p>" . $translations['No favorite items available'] . "</p>";
    }
    ?>
</div>

<div id="brandFavorites" class="tab-content">
    <?php
    // ブランドのお気に入りを取得
    $sql_brand_favorite = "SELECT DISTINCT brand.brand_name, brand.brand_id, favorite.*
                           FROM favorite
                           LEFT OUTER JOIN brand ON brand.brand_id = favorite.brand_id
                           WHERE favorite.user_id = :user_id AND favorite.shop_id = 0"; // 商品IDがNULLで、ブランドIDがあるレコードを取得

    $stmt_brand_favorite = $dbh->prepare($sql_brand_favorite);
    $stmt_brand_favorite->bindParam(':user_id', $userId);
    $stmt_brand_favorite->execute();

    if ($stmt_brand_favorite->rowCount() > 0) {
        echo "<div class='favorite-brands'>";
        while ($favorite_brand = $stmt_brand_favorite->fetch(PDO::FETCH_ASSOC)) {
            $brand_url = "brand_detail.php?brand=" . $favorite_brand['brand_id'];
            echo "
            <div class='favorite-card'>
                <a href='$brand_url' class='brand-link' {$favorite_brand['brand_id']}'>
                    <h2>{$favorite_brand['brand_name']}</h2>
                </a>
            </div>";
        }
        echo "</div>";
    } else {
        echo "<p>".$translations['No favorite brands available']."</p>";
    }
    ?>
</div>


<script>
    // タブの切り替え
    function openTab(evt, tabName) {
        // 全てのタブコンテンツを非表示
        var i, tabContent, tabLinks;
        tabContent = document.getElementsByClassName("tab-content");
        for (i = 0; i < tabContent.length; i++) {
            tabContent[i].style.display = "none";
        }

        // 全てのタブリンクのアクティブ状態を削除
        tabLinks = document.getElementsByClassName("tab");
        for (i = 0; i < tabLinks.length; i++) {
            tabLinks[i].className = tabLinks[i].className.replace(" active", "");
        }

        // 選択されたタブを表示
        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.className += " active";
    }

    // 最初に表示するタブを設定
    document.getElementsByClassName("tab")[0].click();
</script>