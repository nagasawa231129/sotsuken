

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="style.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ショッピングカート</title>
        <script>
            // AJAXで個数の増減を処理する関数
            function updateQuantity(shopId, newQuantity) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'update_quantity.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        // サーバーからの応答を受け取った後の処理
                        if (xhr.responseText === 'success') {
                            // 新しい個数を表示（成功した場合）
                            document.getElementById('quantity_' + shopId).innerText = newQuantity;
                        } else {
                            alert('個数の更新に失敗しました。' + xhr.responseText);
                            echo 
                        }
                    }
                };
                xhr.send('shop_id=' + shopId + '&quantity=' + newQuantity);
            }

            // 増加ボタンをクリックした時の処理
            function increaseQuantity(shopId, currentQuantity) {
                var newQuantity = currentQuantity + 1;
                console.log('Sending shopId: ' + shopId + ' and newQuantity: ' + newQuantity);
                updateQuantity(shopId, newQuantity);
            }

            // 減少ボタンをクリックした時の処理
            function decreaseQuantity(shopId, currentQuantity) {
                if (currentQuantity > 1) { // 個数が1以下にはならないように制限
                    var newQuantity = currentQuantity - 1;
                    updateQuantity(shopId, newQuantity);
                }
            }
        </script>
    </head>
    <body>
        <div class="container">
            <h1>カートの中身</h1>
        
            <?php
include '../../db_open.php';

$sumPrice = 0;

if($conn){
    $sql = "SELECT DISTINCT shop_id FROM cart";
    $result = $conn->query($sql);

    if($result === false){
        $errorInfo = $conn->errorInfo();
        $shopId = 'クエリ失敗' . $errorInfo[2];
    }else{
        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            $shopId = $row['shop_id'];

            // 3. shopテーブルから対応するgoodsとpriceを取得
            $sqlGoods = "SELECT goods, price, quantity FROM shop WHERE shop_id = :shop_id";
            $stmt = $conn->prepare($sqlGoods);
            $stmt->bindParam(':shop_id', $shopId, PDO::PARAM_INT);
            $stmt->execute();

            if($stmt->rowCount() > 0){
                $goodsRow = $stmt->fetch(PDO::FETCH_ASSOC);
                $goods = $goodsRow['goods'];
                $price = $goodsRow['price'];
                $quantity = $goodsRow['quantity'];
                $sumPrice = $sumPrice + $price;
                // 商品情報を表示
                echo "<p>shop_id: $shopId 商品名: <span class='info-text'>" . htmlspecialchars($goods, ENT_QUOTES, 'UTF-8') . "</span><br>価格: <span class='info-text'>" . htmlspecialchars($price, ENT_QUOTES, 'UTF-8') . "円</span></p>";
                echo "<button onClick='decreaseQuantity($shopId,$quantity)'>-</button>";
                echo "<button onClick='increaseQuantity($shopId,$quantity)'>+</button>";
                
            } else {
                // 商品が見つからなかった場合
                echo "<p>shop_id: $shopId に該当する商品はありません</p>";
            }
        }
    
    }
}else {
    $shopId = '接続失敗';  // 接続が失敗した場合
}

$conn = null;
?>
        <p>合計金額: <span class="info-text"><?php echo htmlspecialchars($sumPrice, ENT_QUOTES, 'UTF-8'); ?>円</span></p>
        </div>
    </body>
</html>




