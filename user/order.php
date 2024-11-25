<?php
include "../../db_open.php";
include "../header.php";
include "../head.php";
echo "<link rel='stylesheet' href='header.css'";
echo "<link rel='stylesheet' href='order.css'";
// ユーザーIDをセッションから取得
// $user_id = $_SESSION['user_id']; 
if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];
} else {
    $userId = null;
}

// 購入履歴を取得
$sql = "SELECT c.cart_id, s.goods, c.quantity, s.price FROM cart c JOIN shop s ON c.shop_id = s.shop_id WHERE c.user_id = :user_id";

$stmt = $dbh->prepare($sql);
$stmt->bindParam(1, $user_id, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<link rel="stylesheet" href="header.css">
<body>
    <h1>購入履歴</h1>

    <?php
   if (count($result) > 0) {
    foreach ($result as $row) {
        echo "<div class='order-item'>";
        echo "<p>商品名: {$row['goods']}</p>";
        echo "<p>購入日: {$row['order_date']}</p>";

        if (!$row['review_id']) {
            // レビューがまだ書かれていない場合、レビューを書くボタンを表示
            echo "<a href='review.php?order_id={$row['id']}'>レビューを書く</a>";
        } else {
            // レビューが書かれている場合
            echo "<p>レビュー済み</p>";
        }

        echo "</div><hr>";
    }
} else {
    echo "<p>購入履歴はありません。</p>";
}

    ?>

</body>

</html>