<!DOCTYPE html>
<html lang="ja">
<link rel="stylesheet" href="order_management.css">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>購入状況管理</title>
    <style>

    </style>
</head>

<body>
    <div class="tabs">
    <a href="admin_toppage.php" class="tab">トップページ</a>
        <a href="order_management.php" class="tab active">全て表示</a>
        <a href="waiting_for_payment.php" class="tab">入金待ち</a>
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
ORDER BY cart.order_date, cart.user_id, cart.cart_id");

    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $last_user_id = null;
    $last_order_time = null;

    foreach ($results as $row) {
        if ($last_user_id !== $row['user_id'] || $last_order_time !== $row['order_time']) {
            if ($last_user_id !== null) {
                echo '</div></form>';
            }

            echo '<form method="POST" action="send_act.php.php">';
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

        if ($row['trade_situation'] == 2) {
            echo '<label><input type="checkbox" name="selected_items[]" value="' . $row['cart_id'] . '"> 完了</label>';
        }

        echo '</div>';

        $last_user_id = $row['user_id'];
        $last_order_time = $row['order_time'];
    }

    if ($last_user_id !== null) {
        echo '</div></form>';
    }
    ?>

  <script>
    // モーダルに関する既存のコードは省略しています

    // 送信ボタンを取得
    const submitButtons = document.querySelectorAll('form input[type="submit"]');

    // 各送信ボタンにクリックイベントを設定
    submitButtons.forEach(button => {
        button.addEventListener("click", (event) => {
            // フォーム内のチェックボックスを取得
            const form = button.closest('form'); // ボタンが属するフォームを取得
            const checkboxes = form.querySelectorAll('input[type="checkbox"]:checked');

            // チェックされた個数を取得
            const checkedCount = checkboxes.length;

            // チェックが 0 件の場合は何もしない
            if (checkedCount === 0) {
                alert("送信する商品が選択されていません。");
                event.preventDefault(); // 送信処理をキャンセル
                return;
            }

            // 確認アラートを表示
            const confirmation = confirm(`${checkedCount}件送信しますか？`);
            if (!confirmation) {
                event.preventDefault(); // キャンセルが選択された場合、送信を中止
            }
        });
    });
    </script>
</body>


</html>