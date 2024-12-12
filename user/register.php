<?php
include '../../db_open.php';  // db_open.phpをインクルードして、$

session_start();
if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];
} else {
    header('Location: login.php');
    $userId = null;
}

$sumPrice = 0;
$cartItemsExist = false;

// データベース接続が成功しているか確認（デバッグ用）
if ($dbh) {
    // カートからデータを取得
    $sql = "SELECT * FROM cart WHERE user_id = :user_id";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt; // 正しく取

    // クエリが失敗した場合
    if ($result === false) {
        $errorInfo = $dbh->errorInfo();  // PDO::errorInfo()で詳細エラーを取得
        echo 'クエリ失敗: ' . $errorInfo[2];  // エラーメッセージを表示
    } else {
        echo "<h1>内容をお確かめください</h1>";

        // カートのデータを処理
        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            $cart_id = $row['cart_id'];
            $user_id = $row['user_id'];
            $shop_id = $row['shop_id'];
            $quantity = $row['quantity'];
            $trade_situation = $row['trade_situation'];
            $order_date = $row['order_date'];

            // shopテーブルから商品情報を取得
            $sqlGoods = "SELECT goods, price FROM shop WHERE shop_id = :shop_id";
            $stmt = $dbh->prepare($sqlGoods);
            $stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
            $stmt->execute();

            // 商品情報が正しく取得できたか確認
            $goodsRow = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($goodsRow) {
                // 商品情報が取得できた場合
                $goods = $goodsRow['goods'];
                $price = $goodsRow['price'];

                // 合計金額の計算
                $sumPrice += ($price * $quantity);

                // 商品情報を表示
                echo "<div class='cart-item'>";
                echo "<p>商品名: <span class='info-text'>" . htmlspecialchars($goods, ENT_QUOTES, 'UTF-8') . "</span><br>"; 
                echo "価格: <span class='info-text'>" . htmlspecialchars($price, ENT_QUOTES, 'UTF-8') . "円</span><br>";
                echo "数量: <span id='quantity_$shop_id'>" . $quantity . "</span> 個<br>";
                echo "合計: <span id='totalAmount_$shop_id'>" . ($price * $quantity) . "円</span><br>";
                echo "</div>";
                $cartItemsExist = true;
            } else {
                echo "<p>shop_id: $shop_id に該当する商品はありません</p>";
            }

            $addressSql = "SELECT address, address2, address3 FROM user WHERE user_id = :user_id";
            $stmt = $dbh->prepare($addressSql);
            $stmt->bindParam(':user_id',$user_id, PDO::PARAM_INT);
            $stmt->execute();
            
            // 住所情報が正しく取得できているか確認
// echo "<pre>";
// var_dump($addressRow);  // 取得した住所情報を表示
// echo "</pre>";

            $addressRow = $stmt->fetch(PDO::FETCH_ASSOC);
            $address = $addressRow['address'] ?? '住所情報がありません';  // 住所がない場合はデフォルトメッセージ
            $address2 = $addressRow['address2'] ?? '';  // 追加住所
            $address3 = $addressRow['address3'] ?? '';  // その他住所

        }
    }

} else {
    echo 'データベース接続に失敗しました。';  // 接続失敗時のエラーメッセージ
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="register_style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>決済完了</title>
</head>
<body>
    <p>合計金額: <span id="totalSum" class="info-text"><?php echo htmlspecialchars($sumPrice, ENT_QUOTES, 'UTF-8'); ?>円</span></p>

    <h3>お届け先住所を指定</h3>
    <p id="address"><?php echo htmlspecialchars($address, ENT_QUOTES, 'UTF-8'); ?></p>
    <button id="openModalButton">住所変更</button>

    <!-- モーダル -->
    <div id="modal" class="modal" style="display:none">
        <div class="modal-content">
            <span class="close-btn" id="closeModalBtn">&times;</span>

            <h3>登録されている住所一覧</h3>
            
                <!-- 住所1 -->
                
                    <input type="radio" name="selected_address" value="<?php echo htmlspecialchars($address, ENT_QUOTES, 'UTF-8'); ?>" class="address-checkbox">
                    <?php echo htmlspecialchars($address, ENT_QUOTES, 'UTF-8'); ?></br>
                

                <!-- 住所2 -->
                <?php if ($address2): ?>
                    
                        <input type="radio" name="selected_address" value="<?php echo htmlspecialchars($address2, ENT_QUOTES, 'UTF-8'); ?>" class="address-checkbox">
                        <?php echo htmlspecialchars($address2, ENT_QUOTES, 'UTF-8'); ?></br>
                    
                <?php endif; ?>

                <!-- 住所3 -->
                <?php if ($address3): ?>
                    
                        <input type="radio" name="selected_address" value="<?php echo htmlspecialchars($address3, ENT_QUOTES, 'UTF-8'); ?>" class="address-checkbox">
                        <?php echo htmlspecialchars($address3, ENT_QUOTES, 'UTF-8'); ?>
                <?php endif; ?>
            

            <button id="addNewAddressButton">住所の追加</button>

            <div id="newAddressFormContainer" >
                <p>新しい住所を追加(３つまで)</p>
                <form action="add_address.php" method="POST">
                    <input type="text" id="new_address" name="new_address" value="<?php echo htmlspecialchars($address,ENT_QUOTES,'UTF-8')?>" required>
                    <input type="submit" value="追加する">
                </form>
            </div>
        </div>
    </div>

    <div class="container">
        <h1>決済完了</h1>

        <?php if($cartItemsExist): ?>
        <form action="payment_complete.php" method="post" id="paymentForm">
            <input type="hidden" name="selected_address" id="selectedAddressInput">
            <button id="paymentCompleteButton">決済完了</button>
        </form>
        <?php else: ?>
            <p>カートに商品がありません。</p>
        <?php endif; ?>
    </div>

    <script>
         const addressCheckboxes = document.querySelectorAll('.address-checkbox');
    const addressElement = document.getElementById('address');
    const selectedAddressInput = document.getElementById('selectedAddressInput'); // 隠しフィールドを取得

    addressCheckboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            // チェックされた住所を取得して、ページに反映
            let selectedAddress = '';
            addressCheckboxes.forEach(function(cb) {
                if (cb.checked) {
                    selectedAddress = cb.value;
                }
            });

            if (selectedAddress) {
                addressElement.innerText = selectedAddress; // 住所を更新
                selectedAddressInput.value = selectedAddress; // 隠しフィールドに住所をセット
            }
        });
    });

        // モーダルの表示
        const modal = document.getElementById('modal');
        const openModalButton = document.getElementById('openModalButton');
        const closeModalButton = document.getElementById('closeModalBtn');

        openModalButton.onclick = function() {
            modal.style.display = 'block';
        };

        // モーダルの閉じるボタン
        closeModalButton.onclick = function() {
            modal.style.display = 'none';
        };

        // モーダル外をクリックして閉じる
        window.onclick = function(event) {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>

