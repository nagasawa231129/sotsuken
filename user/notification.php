<?php
include "../../db_open.php"; // データベース接続
include "../header.php"; // ヘッダー
include "../head.php"; // ヘッド
echo "<link rel='stylesheet' href='header.css'>";


if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];
} else {
    $userId = null;
}

// 未読の通知を取得
$query = "SELECT * FROM notification WHERE user_id = :user_id AND read_status = 0 ORDER BY created_at DESC";
$stmt = $dbh->prepare($query);
$stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
$stmt->execute();
$unread_notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 通知を既読にする（クリック時）
if (isset($_GET['notification_id'])) {
    $notification_id = $_GET['notification_id'];
    
    // 既読にする処理
    $update_query = "UPDATE notification SET read_status = 1 WHERE alert_id = :alert_id";
    $update_stmt = $dbh->prepare($update_query);
    $update_stmt->bindValue(':alert_id', $notification_id, PDO::PARAM_INT);
    $update_stmt->execute();
    
    // 既読にしたら再読み込みして通知一覧を表示
    header("Location: notification.php");
    exit();
}

// 通知の件数を取得（未読）
$notification_count_stmt = $dbh->prepare("SELECT COUNT(*) FROM notification WHERE user_id = :user_id AND read_status = 0");
$notification_count_stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
$notification_count_stmt->execute();
$unread_count = $notification_count_stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>お知らせ</title>
    <link rel="stylesheet" href="notification.css">
</head>
<body>

<header>
    <!-- 通知アイコン -->
    <div class="icon">
        <a href="notification.php">
            <!-- <i class="bell-icon">🔔</i> -->
            <?php if ($unread_count > 0): ?>
                <span class="notification-count"><?php echo $unread_count; ?></span>
            <?php endif; ?>
        </a>
    </div>
</header>

<main>
    <h1><?php echo $translations['Notice'] ?></h1>

    <?php if ($unread_notifications): ?>
        <ul class="notification-list">
            <?php foreach ($unread_notifications as $notification): ?>
                <li class="notification-item">
                    <a href="?notification_id=<?php echo $notification['alert_id']; ?>" class="notification-link">
                        <div class="notification-content">
                            <h2 class="notification-title"><?php echo htmlspecialchars($notification['title']); ?></h2>
                            <p class="notification-text"><?php echo htmlspecialchars($notification['content']); ?></p>
                            <span class="notification-time"><?php echo $notification['created_at']; ?></span>
                        </div>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p><?php echo $translations['There are no new notices'] ?></p>
    <?php endif; ?>
</main>

</body>
</html>
