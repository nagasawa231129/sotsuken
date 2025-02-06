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
        <a href="waiting_for_shipment.php" class="tab">発送待ち
            
        </a>
        <a href="send_shipped.php" class="tab">発送済み</a>
    </div>
  <!-- モーダル -->
  <div id="imageModal" class="modal" style ="display: none;">
        <span class="close">&times;</span>
        <img class="modal-content" id="modalImage">
    </div>

    <?php
    include './../../db_open.php';
    include './../../db_open.php';
    $stmt = $dbh->prepare("SELECT COUNT(*) AS count FROM cart_detail WHERE trade_situation = 2");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $orderCount = $result['count'];


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
cart.quantity as quantity,
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

    // 現在のページを取得（デフォルトは1ページ目）
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$items_per_page = 5; // 1ページあたりの商品数
$offset = ($page - 1) * $items_per_page; // データの取得開始位置

// 受注ごとにまとめるため、まずは $grouped_orders を作成
$grouped_orders = [];
foreach ($results as $row) {
    $key = $row['user_id'] . '_' . $row['order_time']; // 受注ごとのキー
    if (!isset($grouped_orders[$key])) {
        $grouped_orders[$key] = [];
    }
    $grouped_orders[$key][] = $row;
}

// 全ての受注データを取得し、ページごとに分割
$all_orders = array_values($grouped_orders); // 配列のインデックスを振り直す
$total_pages = ceil(count($all_orders) / $items_per_page); // 総ページ数
$orders_to_display = array_slice($all_orders, $offset, $items_per_page); // 現在のページに表示する受注のみ取得

// 受注の表示
foreach ($orders_to_display as $orders) {
    $first = $orders[0]; // 受注の最初のデータを取得

    // 受注情報（order-data）
    echo '<form method="POST" action="next_page.php">';
    echo '<div class="order-data">';
    echo '<h2>受注時間: ' . htmlspecialchars($first['order_time']) . '</h2>';
    echo '<p><span class="data-label">カナ:</span> <span class="data-value">' . $first['k_sei'] . ' ' . $first['k_mei'] . '</span></p>';
    echo '<p><span class="data-label">宛名:</span> <span class="data-value">' . $first['u_sei'] . ' ' . $first['u_mei'] . '</span></p>';
    echo '<p><span class="data-label">電話番号:</span> <span class="data-value">' . $first['tel'] . '</span></p>';
    echo '<p><span class="data-label">送り先住所:</span> <span class="data-value">' . $first['senadd'] . '</span></p>';
    echo '<p><span class="data-label">取引状況:</span> 入金待ち</p>';
    echo '<div class="product-group">';

    // 商品情報
    foreach ($orders as $row) {
        $imgBlob = $row['thumb']; // サムネイルのBLOBデータ
        $shopId = $row['shop_id']; // shop_idを取得
        $encodedImg = base64_encode($imgBlob); // Base64エンコード

        echo '<div class="product-data">';
        echo "<img src='data:image/jpeg;base64,$encodedImg' alt='サムネイル' width='100' class='thumbnail' data-shop-id='$shopId' />";
        echo '<p><span class="data-label">ブランド:</span> <span class="data-value">' . $row['brand'] . '</span></p>';
        echo '<p><span class="data-label">商品名:</span> <span class="data-value">' . $row['goods'] . '</span></p>';
        echo '<p><span class="data-label">色:</span> <span class="data-value">' . $row['color'] . '</span></p>';
        echo '<p><span class="data-label">サイズ:</span> <span class="data-value">' . $row['size'] . '</span></p>';
        echo '<p><span class="data-label">個数:</span> <span class="data-value">' . $row['quantity'] . '</span></p>';
        echo '</div>';
    }

    echo '</div>'; // 商品グループ終了
    echo '</div></form>'; // 受注終了
}

echo '<div class="pagination">';
for ($i = 1; $i <= $total_pages; $i++) {
    if ($i == $page) {
        echo '<span class="current-page">' . $i . '</span>'; // 現在のページはハイライト
    } else {
        echo '<a href="?page=' . $i . '">' . $i . '</a>'; // 各ページのリンクを生成
    }
}
echo '</div>';

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
