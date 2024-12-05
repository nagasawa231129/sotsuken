<!DOCTYPE html>
<html lang="ja">
<link rel="stylesheet" href="order_management.css">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>購入状況管理</title>
    
</head>

<body>
    <div class="tabs">
    <a href="admin_toppage.php" class="tab">トップページ</a>
        <a href="order_management.php" class="tab">全て表示</a>
        <a href="waiting_for_payment.php" class="tab active">入金待ち</a>
        <a href="waiting_for_shipment.php" class="tab">発送待ち</a>
        <a href="send_shipped.php" class="tab">発送済み</a>
    </div>
  <!-- モーダル -->
  <div id="imageModal" class="modal" style ="display: none;">
        <span class="close">&times;</span>
        <img class="modal-content" id="modalImage">
    </div>

    <?php
    include './../../db_open.php';

    // SQL修正: WHERE句の位置を正しく修正
    $stmt = $dbh->prepare("SELECT 
    DATE_FORMAT(cart.order_date, '%Y-%m-%d %H:%i') AS order_time,
    cart.user_id,
cart.cart_id,
cart.shop_id, 
shop.goods, 
shop.thumbnail as thumb,
b.brand_name AS brand,
c.color as color,
s.size as size,
user.sei AS u_sei,
user.mei AS u_mei,
user.kanasei AS k_sei,
user.kanamei AS k_mei,
user.phone as tel,
cart.send_address as senadd,
cart.quantity,
cart.trade_situation,
cart.send_address
FROM cart_detail cart 
LEFT JOIN shop shop ON cart.shop_id = shop.shop_id
LEFT JOIN brand b ON shop.brand_id = b.brand_id
LEFT JOIN size s ON shop.size = s.size_id
LEFT JOIN color c ON shop.color = c.color_id
LEFT JOIN user user ON cart.user_id = user.user_id
WHERE cart.trade_situation = 1
ORDER BY cart.order_date, cart.user_id, cart.cart_id");

    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $last_user_id = null;
    $last_order_time = null;

    foreach ($results as $row) {
        // 新しい受注の開始を検出
        if ($last_user_id !== $row['user_id'] || $last_order_time !== $row['order_time']) {
            if ($last_user_id !== null) {
                echo '</div></form>'; // 前の受注を閉じる
            }

            // 新しい受注ブロックの開始
            echo '<form method="POST" action="next_page.php">';
            echo '<div class="order-data">';
            echo '<h2>受注時間: ' . htmlspecialchars($row['order_time']) . '</h2>';
            echo '<p><span class="data-label">カナ:</span> <span class="data-value">' . $row['k_sei'] . ' ' . $row['k_mei'] . '</span></p>';
            echo '<p><span class="data-label">宛名:</span> <span class="data-value">' . $row['u_sei'] . ' ' . $row['u_mei'] . '</span></p>';
            echo '<p><span class="data-label">電話番号:</span> <span class="data-value">' . $row['tel'] . '</span></p>';
            echo '<p><span class="data-label">送り先住所:</span> <span class="data-value">' . $row['senadd'] . '</span></p>';
           echo '<p><span class="data-label">取引状況:</span> 入金待ち</p>';
        }
        $imgBlob = $row['thumb']; // サムネイルのBLOBデータ
        $shopId = $row['shop_id'];    // shop_idを取得
        $encodedImg = base64_encode($imgBlob); // Base64エンコード
          
            
        
        echo '<div class="product-data">';
        echo "<img src='data:image/jpeg;base64,$encodedImg' alt='サムネイル' width='100' class='thumbnail' data-shop-id='$shopId' />";    
        echo '<p><span class="data-label">ブランド:</span> <span class="data-value">' . $row['brand'] . '</span></p>';
        echo '<p><span class="data-label">商品名:</span> <span class="data-value">' . $row['goods'] . '</span></p>';
        echo '<p><span class="data-label">色:</span> <span class="data-value">' . $row['color'] . '</span></p>';
        echo '<p><span class="data-label">サイズ:</span> <span class="data-value">' . $row['size'] . '</span></p>';
        echo '<p><span class="data-label">個数:</span> <span class="data-value">' . $row['quantity'] . '</span></p>';
        echo '</div>';

        $last_user_id = $row['user_id'];
        $last_order_time = $row['order_time'];
    }

    // 最後の注文の閉じ
    if ($last_user_id !== null) {
        echo '</div></form>';
    }
    ?>
     <script>
        // モーダルを取得
        const modal = document.getElementById("imageModal");
        const modalImg = document.getElementById("modalImage");
        const closeBtn = document.querySelector(".close");

        // サムネイル画像をクリックしたときの処理
        document.querySelectorAll(".thumbnail").forEach(img => {
            img.addEventListener("click", () => {
                modal.style.display = "flex"; // モーダルを中央に表示
                modalImg.src = img.src; // サムネイル画像のソースをモーダル画像に設定
            });
        });

        // モーダルの閉じるボタンをクリックしたときの処理
        closeBtn.addEventListener("click", () => {
            modal.style.display = "none";
        });

        // モーダルの背景をクリックしたときの処理
        modal.addEventListener("click", (event) => {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });
    </script>
</body>

</html>
