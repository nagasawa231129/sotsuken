<?php
// 未読メール数を取得
// $unreadCount = include('get_unread_count.php');
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>管理者画面 - ECサイト</title>

</head>
<link rel="stylesheet" href="admin_toppage.css">
<body>

<div class="admin-container">
  <h1>ECサイト管理者画面</h1>
  <a href="order_management.php" class="admin-button">
    受注管理
    <span class="notification-badge" id="orderBadge">5</span> <!-- 例：5件の通知 -->
  </a>
  <a href="https://mail.google.com/mail/u/1/#inbox" class="admin-button">
    メール管理
    <span class="notification-badge" id="emailBadge"></span> <!-- 未読メール数 -->
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
