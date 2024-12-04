<!DOCTYPE html>
<html lang="ja">

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="add_goods.css">
<title>商品追加フォーム</title>
<a href="admin_toppage.php">トップページ</a>

<body>
    <?php
    include './../../db_open.php';
    $sql = "
    SELECT 
        user.sei, 
        user.mei, 
        user.tel, 
        user.mail, 
        user.send_postcode, 
        user.send_address, 
        cart.cart_id, 
        cart.shop_id, 
        cart.quantity, 
        cart.trade_situation, 
        cart.order_date
    FROM 
        user
    JOIN 
        cart 
    ON 
        user.user_id = cart.user_id
";

    // クエリを準備して実行
    $stmt = $dbh->query($sql);

    // データを取得して表示
    echo "<table border='1'>";
    echo "<tr>
        <th>姓</th>
        <th>名</th>
        <th>電話番号</th>
        <th>メールアドレス</th>
        <th>送付先郵便番号</th>
        <th>送付先住所</th>
        <th>カートID</th>
        <th>ショップID</th>
        <th>数量</th>
        <th>取引状況</th>
        <th>注文日</th>
      </tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['sei']) . "</td>";
        echo "<td>" . htmlspecialchars($row['mei']) . "</td>";
        echo "<td>" . htmlspecialchars($row['tel']) . "</td>";
        echo "<td>" . htmlspecialchars($row['mail']) . "</td>";
        echo "<td>" . htmlspecialchars($row['send_postcode']) . "</td>";
        echo "<td>" . htmlspecialchars($row['send_address']) . "</td>";
        echo "<td>" . htmlspecialchars($row['cart_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['shop_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
        echo "<td>" . htmlspecialchars($row['trade_situation']) . "</td>";
        echo "<td>" . htmlspecialchars($row['order_date']) . "</td>";
        echo "</tr>";
    }

    ?>

</body>

</html>