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
    <div id="imageModal" class="modal" style="display: none;">
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
WHERE cart.trade_situation IN (1, 2)
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
        }
        $imgBlob = $row['thumb']; // サムネイルのBLOBデータ
        $shopId = $row['shop_id']; // shop_idを取得
        $userMail = $row['mail']; // ユーザーのメールアドレスを取得
        $encodedImg = base64_encode($imgBlob); // Base64エンコード

        echo '<div class="product-data">';
        echo "<img src='data:image/jpeg;base64,$encodedImg' alt='サムネイル' width='100' class='thumbnail' data-shop-id='$shopId' />";
        echo '<p><span class="data-label">ブランド:</span> <span class="data-value">' . $row['brand'] . '</span></p>';
        echo '<p><span class="data-label">商品名:</span> <span class="data-value">' . $row['goods'] . '</span></p>';
        echo '<p><span class="data-label">色:</span> <span class="data-value">' . $row['color'] . '</span></p>';
        echo '<p><span class="data-label">サイズ:</span> <span class="data-value">' . $row['size'] . '</span></p>';
        echo '<p><span class="data-label">個数:</span> <span class="data-value">' . $row['quantity'] . '</span></p>';

        // チェックボックスが選択された時にhiddenフィールドとしてメールアドレスを渡す
        if ($row['trade_situation'] == 2) {
            echo '<label><input type="checkbox" name="selected_items[]" value="' . $row['cart_id'] . '" data-user-mail="' . $userMail . '"> 完了</label>';
        }

        echo '</div>';

        // メールアドレスをhiddenフィールドとしてフォームに追加
        echo '<input type="hidden" name="user_mail[]" value="' . $userMail . '">';
        echo '<input type="hidden" name="name[]" value="' .  $row['u_sei'] . ' ' . $row['u_mei'] . '">';
        echo '<input type="hidden" name="goods[]" value="' . $row['goods'] . '">';
        echo '<input type="hidden" name="brand[]" value="' . $row['brand'] . '">';
        echo '<input type="hidden" name="size[]" value="' . $row['size'] . '">';
        echo '<input type="hidden" name="color[]" value="' . $row['color'] . '">';
        echo '<input type="hidden" name="quantity[]" value="' . $row['quantity'] . '">';

        // 新たにhidden項目を追加
        echo '<input type="hidden" name="user_address[]" value="' . $row['senadd'] . '">'; // 住所の追加
        echo '<input type="hidden" name="user_phone[]" value="' . $row['tel'] . '">'; // 電話番号の追加

        $last_user_id = $row['user_id'];
        $last_order_time = $row['order_time'];
    }

    if ($last_user_id !== null) {
        echo '</div></form>';
    }
    ?>


    <script>
        // 送信ボタンを取得
        const submitButtons = document.querySelectorAll('form input[type="submit"]');

        // 各送信ボタンにクリックイベントを設定
        submitButtons.forEach(button => {
            button.addEventListener("click", (event) => {
                const form = button.closest('form');
                const checkboxes = form.querySelectorAll('input[type="checkbox"]');
                const checkedBoxes = form.querySelectorAll('input[type="checkbox"]:checked');

                // チェックされている個数を取得
                const checkedCount = checkedBoxes.length;

                // チェックされていないチェックボックスがあるか確認
                const uncheckedCount = checkboxes.length - checkedCount;
                // チェックボックスが一つでも選択されていない場合
                if (checkedBoxes.length === 0) {
                    alert("選択されていない商品があります。");
                    event.preventDefault(); // フォーム送信を停止
                    return;
                }
                // チェックされていない項目がある場合
                if (uncheckedCount > 0) {
                    alert(`チェックされていない項目が${uncheckedCount}件あります。`);
                    event.preventDefault(); // 送信処理をキャンセル
                    return;
                }
                const confirmation = confirm(`${checkedBoxes.length}件送信しますか？`);
                if (!confirmation) {
                    event.preventDefault(); // 送信を中止
                }
            });
        });
    </script>