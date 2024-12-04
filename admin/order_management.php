<!DOCTYPE html>
<html lang="ja">

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="order_management.css">
<title>購入状況管理</title>
<a href="admin_toppage.php">トップページ</a>

<body>
    <?php
    include './../../db_open.php';
    $stmt = $dbh->prepare("
SELECT 
    DATE_FORMAT(cart.order_date, '%Y-%m-%d %H:%i:%s') AS order_time,
    cart.user_id,
    cart.cart_id,
    cart.shop_id, 
    shop.goods, 
    user.sei AS u_sei,
    user.mei AS u_mei,
    user.kanasei AS k_sei,
    user.kanamei AS k_mei,
    cart.quantity,
    cart.trade_situation,
    cart.send_address
FROM cart_detail cart 
LEFT JOIN user user ON cart.user_id = user.user_id
LEFT JOIN shop shop ON cart.shop_id = shop.shop_id
ORDER BY cart.order_date, cart.user_id, cart.cart_id;
    ");

    $stmt->execute();

    // 結果をフェッチ
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $last_user_id = null; // 最後に処理したユーザーID
    $last_order_time = null; // 最後に処理した注文時間

    foreach ($results as $row) {
        // 注文時間とユーザーIDが異なる場合、カナと宛名を表示
        if ($last_user_id !== $row['user_id'] || $last_order_time !== $row['order_time']) {
            // 新しい注文を開始
            if ($last_user_id !== null) {
                echo '</div>'; // 前回の注文の閉じタグ
            }
            echo '<div class="order-data">';
            echo '<h2>Order Time: ' . $row['order_time'] . '</h2>';
            echo '<p><span class="data-label">カナ:</span> <span class="data-value">' . $row['k_sei'] . ' ' . $row['k_mei'] . '</span></p>';
            echo '<p><span class="data-label">宛名:</span> <span class="data-value">' . $row['u_sei'] . ' ' . $row['u_mei'] . '</span></p>';
        }

        // 商品情報の枠を作成
        echo '<div class="product-data">';
        echo '<p><span class="data-label">Shop ID:</span> <span class="data-value">' . $row['shop_id'] . '</span></p>';
        echo '<p><span class="data-label">Goods:</span> <span class="data-value">' . $row['goods'] . '</span></p>';
        echo '<p><span class="data-label">Quantity:</span> <span class="data-value">' . $row['quantity'] . '</span></p>';
        
        // 取引状況
        if ($row['trade_situation'] == 1) {
            echo '<p>取引状況:入金待ち</p>';
        } elseif ($row['trade_situation'] == 2) {
            echo '<p>取引状況:発送待ち</p>';
        }

        echo '</div>'; // .product-data

        // 最後に処理したユーザーIDと注文時間を更新
        $last_user_id = $row['user_id'];
        $last_order_time = $row['order_time'];
    }

    // 最後の注文の閉じタグ
    echo '</div>'; 
    ?>
</body>

</html>
