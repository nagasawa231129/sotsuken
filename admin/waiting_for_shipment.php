<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>購入状況管理</title>
    <link rel="stylesheet" href="order_management.css">
</head>

<body>
    <div class="tabs">
        <a href="admin_toppage.php" class="tab">トップページ</a>
        <a href="order_management.php" class="tab">全て表示</a>
        <a href="waiting_for_payment.php" class="tab">入金待ち</a>
        <a href="waiting_for_shipment.php" class="tab active">発送待ち </a>
        <a href="send_shipped.php" class="tab">発送済み</a>
    </div>

    <div id="imageModal" class="modal" style="display: none;">
        <span class="close">&times;</span>
        <img class="modal-content" id="modalImage">
    </div>

    <?php
    include './../../db_open.php';

    // 1ページあたりの表示件数
    $itemsPerPage = 5;

    // 現在のページ番号を取得（デフォルトは1）
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $itemsPerPage;

    // データを取得するクエリにLIMITを追加
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
        user.mail as mail,
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
    WHERE cart.trade_situation = 2
    ORDER BY cart.order_date, cart.user_id, cart.cart_id
    LIMIT :offset, :limit");

    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $itemsPerPage, PDO::PARAM_INT);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 総件数を取得してページ数を計算
    $countStmt = $dbh->prepare("SELECT COUNT(*) FROM cart_detail WHERE trade_situation = 2");
    $countStmt->execute();
    $totalItems = $countStmt->fetchColumn();
    $totalPages = ceil($totalItems / $itemsPerPage);

    // 以下は表示部分
    $last_user_id = null;
    $last_order_time = null;
    foreach ($results as $row) {
        if ($last_user_id !== $row['user_id'] || $last_order_time !== $row['order_time']) {
            if ($last_user_id !== null) {
                echo '</div></form>';
            }

            echo '<form method="POST" action="send_act.php">';
            echo '<div class="order-data">';
            echo '<h2>受注時間: ' . $row['order_time'] . '</h2>';
            echo '<p><span class="data-label">カナ:</span> <span class="data-value">' . $row['k_sei'] . ' ' . $row['k_mei'] . '</span></p>';
            echo '<p><span class="data-label">宛名:</span> <span class="data-value">' . $row['u_sei'] . ' ' . $row['u_mei'] . '</span></p>';
            echo '<p><span class="data-label">電話番号:</span> <span class="data-value">' . $row['tel'] . '</span></p>';
            echo '<p><span class="data-label">送り先住所:</span> <span class="data-value">' . $row['senadd'] . '</span></p>';

            if ($row['trade_situation'] == 1) {
                echo '<p><span class="data-label">取引状況: 入金待ち</span></p>';
            } elseif ($row['trade_situation'] == 2) {
                echo '<p><span class="data-label">取引状況: 発送待ち</span></p>';
                echo '<input type="submit" value="送信">';
            }

            echo '<button type="button" class="payment-button" onclick="confirmPayment(' . $row['cart_id'] . ', \'' . $row['mail'] . '\')">入金未確認</button>';
        }

        $imgBlob = $row['thumb'];
        $shopId = $row['shop_id'];
        $userMail = $row['mail'];
        $encodedImg = base64_encode($imgBlob);

        echo '<div class="product-data">';
        echo "<img src='data:image/jpeg;base64,$encodedImg' alt='サムネイル' width='100' class='thumbnail' data-shop-id='$shopId' />";
        echo '<p><span class="data-label">ブランド:</span> <span class="data-value">' . $row['brand'] . '</span></p>';
        echo '<p><span class="data-label">商品名:</span> <span class="data-value">' . $row['goods'] . '</span></p>';
        echo '<p><span class="data-label">色:</span> <span class="data-value">' . $row['color'] . '</span></p>';
        echo '<p><span class="data-label">サイズ:</span> <span class="data-value">' . $row['size'] . '</span></p>';
        echo '<p><span class="data-label">個数:</span> <span class="data-value">' . $row['quantity'] . '</span></p>';

        if ($row['trade_situation'] == 2) {
            echo '<label><input type="checkbox" name="selected_items[]" value="' . $row['cart_id'] . '" data-user-mail="' . $userMail . '"> 発送準備完了</label>';
        }

        echo '</div>';

        echo '<input type="hidden" name="user_mail[]" value="' . $userMail . '">';
        echo '<input type="hidden" name="cart_ids[]" value="' . $row['cart_id'] . '">';
        echo '<input type="hidden" name="goods[]" value="' . $row['goods'] . '">';
        echo '<input type="hidden" name="size[]" value="' . $row['size'] . '">';
        echo '<input type="hidden" name="brand[]" value="' . $row['brand'] . '">';
        echo '<input type="hidden" name="color[]" value="' . $row['color'] . '">';
        echo '<input type="hidden" name="quantity[]" value="' . $row['quantity'] . '">';

        $last_user_id = $row['user_id'];
        $last_order_time = $row['order_time'];
    }

    if ($last_user_id !== null) {
        echo '</div></form>';
    }

    // ページングリンクを表示
    echo '<div class="pagination">';
    for ($i = 1; $i <= $totalPages; $i++) {
        echo '<a href="?page=' . $i . '" class="page-link">' . $i . '</a>';
    }
    echo '</div>';
    ?>
</body>
