<?php
// 未読メール数を取得
// $unreadCount = include('get_unread_count.php');
include './../../db_open.php';
$stmt = $dbh->prepare("SELECT COUNT(*) AS count FROM cart_detail WHERE trade_situation = 2");
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$orderCount = $result['count'];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>管理者画面 - ECサイト</title>
  <link rel="stylesheet" href="admin_toppage.css">
</head>
<body>

<div class="admin-container">
  <h1>ECサイト管理者画面</h1>
  <a href="order_management.php" class="admin-button">
    受注管理
    <span class="notification-badge" id="orderBadge">
      <?php echo htmlspecialchars($orderCount, ENT_QUOTES, 'UTF-8'); ?>
    </span>
  </a>
  <a href="https://mail.google.com/mail/u/1/#inbox" class="admin-button">
    メール管理
   
  </a>
  <a href="inventory_management.php" class="admin-button">
    在庫管理
  </a>
  <a href="goods_info.php" class="admin-button">
    商品情報管理
  </a>
  <a href="logout.php" class="admin-button">
    ログアウト
  </a>
</div>

</body>
</html>
