<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="style.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ショッピングカート</title>
        <script src="cart_script.js"></script>
    </head>
    <body>
        <div class="container">
            <h1>カートの中身</h1>
        
            <?php
include '../../db_open.php';

$sumPrice = 0;

if($dbh){
    $sql = "SELECT DISTINCT shop_id,quantity FROM cart";
    $result = $dbh->prepare($sql);
    $result->execute();
    if($result === false){
        $errorInfo = $dbh->errorInfo();
        echo 'クエリ失敗: ' . $errorInfo[2];
    }else{
        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            $shopId = $row['shop_id'];
            $quantity = $row['quantity'];
            // shopテーブルから商品情報を取得
            $sqlGoods = "SELECT goods, price FROM shop WHERE shop_id = :shop_id";
            $stmt = $dbh->prepare($sqlGoods);
            $stmt->bindParam(':shop_id', $shopId, PDO::PARAM_INT);
            $stmt->execute();

            if($stmt->rowCount() > 0){
                $goodsRow = $stmt->fetch(PDO::FETCH_ASSOC);
                $goods = $goodsRow['goods'];
                $price = $goodsRow['price'];
                
                $sumPrice = $sumPrice + ($price * $quantity);

                // 商品情報を表示
                echo "<div class='cart-item'>";
                echo "<p>商品名: <span class='info-text'>" . htmlspecialchars($goods, ENT_QUOTES, 'UTF-8') . "</span><br>";
                echo "価格: <span class='info-text'>" . htmlspecialchars($price, ENT_QUOTES, 'UTF-8') . "円</span><br>";
                echo "数量: <span id='quantity_$shopId'>" . $quantity . "</span> 個<br>";
                echo "合計: <span id='totalAmount_$shopId'>" . ($price * $quantity) . "円</span><br>";
                echo "<button class='decrease-btn' data-shop-id='$shopId' data-price='$price' data-quantity='$quantity' onClick='updateQuantityHandler(this)'>-</button>";
                echo "<button class='increase-btn' data-shop-id='$shopId' data-price='$price' data-quantity='$quantity' onClick='updateQuantityHandler(this)'>+</button>";
                echo "</div>";
            } else {
                echo "<p>shop_id: $shopId に該当する商品はありません</p>";
            }
        }
    }
}else {
    echo '接続失敗';  // 接続が失敗した場合
}

$dbh = null;
?>
        <p>合計金額: <span id="totalSum" class="info-text"><?php echo htmlspecialchars($sumPrice, ENT_QUOTES, 'UTF-8'); ?>円</span></p>
        <form action="register.php" method="post">
            <div class="submit-wrapper">
                <input type="submit" id="submit" value="決済画面へ">
            </div>
        </form>
        </div>
    </body>
</html>




