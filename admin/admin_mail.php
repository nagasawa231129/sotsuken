<?php
// 未読メール数を取得
$unreadCount = include('get_unread_count.php');
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>メール管理 - ECサイト</title>
  <style>
    /* スタイルを適宜調整 */
  </style>
</head>
<body>

<h1>メール管理</h1>
<p>未読メール数: <?php echo $unreadCount; ?> 件</p>

</body>
</html>
