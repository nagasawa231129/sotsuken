<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>管理者画面 - ECサイト</title>
  <style>
    /* ベーススタイル */
    body {
      font-family: 'Arial', sans-serif;
      background-color: #f4f6f8;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      color: #333;
    }

    /* コンテナ */
    .admin-container {
      width: 100%;
      max-width: 500px;
      background-color: #ffffff;
      border-radius: 12px;
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
      padding: 30px 20px;
      text-align: center;
    }

    /* タイトル */
    .admin-container h1 {
      font-size: 1.8em;
      color: #333;
      margin-bottom: 20px;
      font-weight: 600;
    }

    /* 管理ボタンのスタイル */
    .admin-button {
      position: relative;
      display: block;
      width: 100%;
      margin: 12px 0;
      padding: 15px 0;
      background-color: #007bff;
      color: #ffffff;
      border: none;
      border-radius: 8px;
      font-size: 1.1em;
      font-weight: 500;
      text-align: center;
      cursor: pointer;
      transition: background-color 0.3s ease, transform 0.1s ease;
      text-decoration: none;
    }

    /* 通知バッジのスタイル */
    .notification-badge {
      position: absolute;
      top: -8px;
      right: 12px;
      background-color: #ff3b30;
      color: #ffffff;
      border-radius: 50%;
      padding: 4px 8px;
      font-size: 0.8em;
      font-weight: bold;
    }

    /* ホバースタイルとアクセシビリティ対応 */
    .admin-button:hover {
      background-color: #0056b3;
      transform: translateY(-2px);
    }

    .admin-button:active {
      background-color: #004494;
      transform: translateY(0);
    }

    /* メディアクエリ */
    @media (max-width: 768px) {
      .admin-container {
        padding: 20px 15px;
      }
      .admin-container h1 {
        font-size: 1.5em;
      }
      .admin-button {
        font-size: 1em;
        padding: 12px 0;
      }
    }
  </style>
</head>
<body>

<div class="admin-container">
  <h1>ECサイト管理者画面</h1>
  <a href="order_management.php" class="admin-button">
    受注管理
    <span class="notification-badge" id="orderBadge">5</span> <!-- 例：5件の通知 -->
  </a>
  <a href="admin_mail.php" class="admin-button">
    メール管理
    <span class="notification-badge" id="emailBadge">2</span> <!-- 例：2件の通知 -->
  </a>
  <a href="inventory_management.php" class="admin-button">
    在庫管理
    
  </a>
</div>

<script>
  // 通知件数の初期設定（例）
  let orderCount = 5;
  let emailCount = 2;
  

  // 通知件数を更新する関数
  function updateNotificationBadge() {
    document.getElementById('orderBadge').textContent = orderCount;
    document.getElementById('emailBadge').textContent = emailCount;
    
  }

  // 任意のタイミングで件数を増減させる例
  function incrementOrderCount() {
    orderCount++;
    updateNotificationBadge();
  }

  function decrementOrderCount() {
    if (orderCount > 0) orderCount--;
    updateNotificationBadge();
  }

  // ページロード時にバッジを更新
  updateNotificationBadge();

  // 任意のタイミングでカウントを増減（デモ用）
  setTimeout(incrementOrderCount, 5000); // 5秒後に通知数を増やす
</script>

</body>
</html>
