<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="style.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ショッピングカート</title>
        
    </head>
    <body>
    <div class="container">
        <h1>カートの中身</h1>

        <div class="cart-items">
            <?php
            include '../../db_open.php';

            session_start();
            if (isset($_SESSION['id'])) {
                $userId = $_SESSION['id'];
            } else {
                $userId = null;
            }

            $sumPrice = 0;
            $cartItems = [];

            if($dbh){
                // カート内の商品の情報を取得
                $sql = "SELECT shop_id, quantity FROM cart WHERE user_id = :user_id";
                $stmt = $dbh->prepare($sql);
                $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt;
                if($result === false){
                    $errorInfo = $dbh->errorInfo();
                    echo 'クエリ失敗: ' . $errorInfo[2];
                } else {
                    // カート内の商品情報を連想配列にまとめる
                    while($row = $result->fetch(PDO::FETCH_ASSOC)){
                        $shopId = $row['shop_id'];
                        $quantity = $row['quantity'];

                        // すでに同じshop_idの情報がある場合、quantityを加算
                        if(isset($cartItems[$shopId])){
                            $cartItems[$shopId]['quantity'] += $quantity;
                        } else {
                            $cartItems[$shopId] = ['quantity' => $quantity];
                        }                                                    
                    }

                    // 各商品情報を取得して表示
                    foreach ($cartItems as $shopId => $item) {
                        // shopテーブルから商品情報を取得
                        $sqlGoods = "SELECT goods, price, size, color,material,thumbnail FROM shop WHERE shop_id = :shop_id";
                        $stmt = $dbh->prepare($sqlGoods);
                        $stmt->bindParam(':shop_id', $shopId, PDO::PARAM_INT);
                        $stmt->execute();

                        if($stmt->rowCount() > 0){
                            $goodsRow = $stmt->fetch(PDO::FETCH_ASSOC);
                            $goods = $goodsRow['goods'];
                            $price = $goodsRow['price'];
                            $size = $goodsRow['size'];
                            $color = $goodsRow['color'];
                            $quantity = $goodsRow['material'];

                            $totalPrice = $price * $item['quantity'];
                            $sumPrice += $totalPrice;

                            // 商品情報を表示
                            echo "<div class='cart-item'>";
                            echo "<p>商品名: <span class='info-text'>" . htmlspecialchars($goods, ENT_QUOTES, 'UTF-8') . "</span><br>";
                            echo "価格: <span class='info-text'>" . htmlspecialchars($price, ENT_QUOTES, 'UTF-8') . "円</span><br>";
                            echo "サイズ: <span class='info-text'>" . htmlspecialchars($size, ENT_QUOTES, 'UTF-8') . "</span><br>";
                            echo "カラー: <span class='info-text'>" . htmlspecialchars($color, ENT_QUOTES, 'UTF-8') . "</span><br>";
                            echo "<div class='item-actions'>";
                            echo "<button id='decreaseBtn_$shopId' class='decrease-btn' data-shop-id='$shopId' data-price='$price' data-quantity='$quantity' onClick='updateQuantityHandler(this)'>-</button>";
                            echo " <span id='quantity_$shopId'>" . $item['quantity'] .  "</span> ";
                            echo "<button id='increaseBtn_$shopId' class='increase-btn' data-shop-id='$shopId' data-price='$price' data-quantity='$quantity' onClick='updateQuantityHandler(this)'>+</button>";
                            echo "<br> <span id='totalAmount_$shopId'>" . $totalPrice . "円</span>";
                            echo "</div>";
                            echo "</div>";
                        } else {
                            echo "<p>shop_id: $shopId に該当する商品はありません</p>";
                        }
                    }
                }
            } else {
                echo '接続失敗';
            }

            $allInStock = true;
            foreach ($cartItems as $shopId => $item) {
                $sqlStock = "SELECT material FROM shop WHERE shop_id = :shop_id";
                $stmtStock = $dbh->prepare($sqlStock);
                $stmtStock->bindParam(':shop_id', $shopId, PDO::PARAM_INT);
                $stmtStock->execute();
    
                if ($stmtStock->rowCount() > 0) {
                    $stockRow = $stmtStock->fetch(PDO::FETCH_ASSOC);
                    $availableStock = $stockRow['material'];
    
                    if ($availableStock < $item['quantity']) {
                        echo "<p style='color: red;'>商品ID $shopId の在庫が不足しています。在庫: $availableStock, 必要数: " . $item['quantity'] . "</p>";
                        $allInStock = false;
                    }
                } else {
                    echo "<p style='color: red;'>商品ID $shopId の情報が見つかりません。</p>";
                    $allInStock = false;
                }
            }


            $dbh = null;
            ?>
        </div>

        <p>合計金額: <span id="totalSum" class="info-text"><?php echo htmlspecialchars($sumPrice, ENT_QUOTES, 'UTF-8'); ?>円</span></p>
        <?php $cartItemCount = count($cartItems);?>
        <form action="register.php" method="post">
            <div class="submit-wrapper">
                <?php
                
                ?>
                <input type="submit" id="submit"  value="決済画面へ"<?php echo ($cartItemCount > 0 && $allInStock) ? '' : 'disabled'; ?>>
            </div>
        </form>
    </div>
    <script src="cart_script.js" defer></script>
    </body>
</html>
