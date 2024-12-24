<?php
include "../../db_open.php";
include "../header.php";
include "../head.php";
echo "<link rel='stylesheet' href='header.css'>";
echo "<link rel='stylesheet' href='order.css'>";

// ユーザーIDをセッションから取得
if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];
} else {
    $userId = null;
}

// 各注文状態の情報を取得
$history_sql = "SELECT c.cart_id, c.shop_id, s.goods, c.quantity, s.price, c.order_date, c.trade_situation, r.review_id, s.thumbnail, g.shop_group
                FROM cart_detail c
                JOIN shop s ON c.shop_id = s.shop_id
                LEFT JOIN reviews r ON c.shop_id = r.shop_id
                LEFT OUTER JOIN `group` g ON g.shop_id = s.shop_id
                WHERE c.user_id = :user_id";

$pending_sql = "SELECT c.cart_id, c.shop_id, s.goods, c.quantity, s.price, c.order_date, c.trade_situation, r.review_id, s.thumbnail, g.shop_group
                FROM cart_detail c
                JOIN shop s ON c.shop_id = s.shop_id
                LEFT JOIN reviews r ON c.shop_id = r.shop_id
                LEFT OUTER JOIN `group` g ON g.shop_id = s.shop_id
                WHERE c.user_id = :user_id AND c.trade_situation = '2'";

$shipped_sql = "SELECT c.cart_id, c.shop_id, s.goods, c.quantity, s.price, c.order_date, c.trade_situation, r.review_id, s.thumbnail, g.shop_group
                FROM cart_detail c
                JOIN shop s ON c.shop_id = s.shop_id
                LEFT JOIN reviews r ON c.shop_id = r.shop_id
                LEFT OUTER JOIN `group` g ON g.shop_id = s.shop_id
                WHERE c.user_id = :user_id AND c.trade_situation = '3'";


$stmt_history = $dbh->prepare($history_sql);
$stmt_history->bindParam(1, $userId, PDO::PARAM_INT);
$stmt_history->execute();
$result_history = $stmt_history->fetchAll(PDO::FETCH_ASSOC);

$stmt_pending = $dbh->prepare($pending_sql);
$stmt_pending->bindParam(1, $userId, PDO::PARAM_INT);
$stmt_pending->execute();
$result_pending = $stmt_pending->fetchAll(PDO::FETCH_ASSOC);

$stmt_shipped = $dbh->prepare($shipped_sql);
$stmt_shipped->bindParam(1, $userId, PDO::PARAM_INT);
$stmt_shipped->execute();
$result_shipped = $stmt_shipped->fetchAll(PDO::FETCH_ASSOC);

$review_sql = "SELECT r.review_id, r.shop_id, r.review_content, r.created_at, s.goods, r.rate, g.shop_group
               FROM reviews r
               JOIN shop s ON r.shop_id = s.shop_id
               LEFT OUTER JOIN `group` g ON g.shop_id = s.shop_id
               WHERE r.user_id = :user_id";

$stmt_review = $dbh->prepare($review_sql);
$stmt_review->bindParam(1, $userId, PDO::PARAM_INT);
$stmt_review->execute();
$result_review = $stmt_review->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>注文履歴</title>
    <link rel="stylesheet" href="order.css">
</head>

<body>
    <h1><?php echo $translations['Order History'] ?></h1>

    <div class="tabs">
        <div class="tab <?php echo isset($_GET['tab']) && $_GET['tab'] == 'history' ? 'active' : ''; ?>" id="history-tab" onclick="showTab('history')"><?php echo $translations['Order History'] ?></div>
        <div class="tab <?php echo isset($_GET['tab']) && $_GET['tab'] == 'pending' ? 'active' : ''; ?>" id="pending-tab" onclick="showTab('pending')"><?php echo $translations['Items Pending Shipment'] ?></div>
        <div class="tab <?php echo isset($_GET['tab']) && $_GET['tab'] == 'shipped' ? 'active' : ''; ?>" id="shipped-tab" onclick="showTab('shipped')"><?php echo $translations['Shipped Items'] ?></div>
        <div class="tab <?php echo isset($_GET['tab']) && $_GET['tab'] == 'review' ? 'active' : ''; ?>" id="review-tab" onclick="showTab('review')"><?php echo $translations['Review'] ?></div>
    </div>

    <div id="history-content" class="tab-content <?php echo isset($_GET['tab']) && $_GET['tab'] == 'history' ? 'active' : ''; ?>">
        <?php
        if (count($result_history) > 0) {
            foreach ($result_history as $row) {
                // 画像データの取得とBase64エンコード
        $imgBlob = $row['thumbnail'];
        $mimeType = 'image/png';  // デフォルトMIMEタイプ
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($imgBlob); // 実際のMIMEタイプを取得
        $encodedImg = base64_encode($imgBlob); // Base64エンコード
                echo "<div class='order-item'>";
                echo "<a href='goods.php?shop_id={$row['shop_id']}&shop_group={$row['shop_group']}'>";
                echo "<img class='image' src='data:{$mimeType};base64,{$encodedImg}' alt='goods img' class='sale-product-image'></br>";
                echo "</a>";
                echo "<p>商品名: {$row['goods']}</p>";
                echo "<p>購入日: {$row['order_date']}</p>";
                // echo "<p>ステータス: {$row['trade_situation']}</p>";
                if (!empty($row['review_id'])) {
                    // レビュー済みの場合
                    echo "<p>レビュー済み</p>";
                } else {
                    // レビュー未記入の場合
                    echo "<a href='review.php?shop_id={$row['shop_id']}'>レビューを書く</a>";
                }
                echo "</div><hr>";
            }
        } else {
            echo $translations['No order history available'];
        }
        ?>
    </div>

    <div id="pending-content" class="tab-content <?php echo isset($_GET['tab']) && $_GET['tab'] == 'pending' ? 'active' : ''; ?>">
        <?php
        if (count($result_pending) > 0) {
            foreach ($result_pending as $row) {
                $imgBlob = $row['thumbnail'];
        $mimeType = 'image/png';  // デフォルトMIMEタイプ
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($imgBlob); // 実際のMIMEタイプを取得
        $encodedImg = base64_encode($imgBlob); // Base64エンコード
                echo "<div class='order-item'>";
                echo "<a href='goods.php?shop_id={$row['shop_id']}&shop_group={$row['shop_group']}'>";
                echo "<img class='image' src='data:{$mimeType};base64,{$encodedImg}' alt='goods img' class='sale-product-image'></br>";
                echo "</a>";
                echo "<p>商品名: {$row['goods']}</p>";
                echo "<p>購入日: {$row['order_date']}</p>";
                if ($row['trade_situation'] != 'shipped' && !isset($row['review_id'])) {
                    // review_idが未設定の場合の処理
                    echo "<a href='review.php?shop_id={$row['shop_id']}'>レビューを書く</a>";
                } else {
                    // review_idが設定されている場合の処理
                    echo "<p>レビュー済み</p>";
                }
                echo "</div><hr>";
            }
        } else {
            echo $translations['No items pending shipment'];
        }
        ?>
    </div>

    <div id="shipped-content" class="tab-content <?php echo isset($_GET['tab']) && $_GET['tab'] == 'shipped' ? 'active' : ''; ?>">
        <?php
        if (count($result_shipped) > 0) {
            foreach ($result_shipped as $row) {
                $imgBlob = $row['thumbnail'];
        $mimeType = 'image/png';  // デフォルトMIMEタイプ
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($imgBlob); // 実際のMIMEタイプを取得
        $encodedImg = base64_encode($imgBlob); // Base64エンコード
                echo "<div class='order-item'>";
                echo "<a href='goods.php?shop_id={$row['shop_id']}&shop_group={$row['shop_group']}'>";
                echo "<img class='image' src='data:{$mimeType};base64,{$encodedImg}' alt='goods img' class='sale-product-image'></br>";
                echo "</a>";
                echo "<p>商品名: {$row['goods']}</p>";
                echo "<p>購入日: {$row['order_date']}</p>";
                if ($row['trade_situation'] != 'shipped' && !isset($row['review_id'])) {
                    // review_idが未設定の場合の処理
                    echo "<a href='review.php?shop_id={$row['shop_id']}'>レビューを書く</a>";
                } else {
                    // review_idが設定されている場合の処理
                    echo "<p>レビュー済み</p>";
                }
                echo "</div><hr>";
            }
        } else {
            echo $translations['No items shipped'];
        }
        ?>
    </div>

    <!-- レビュタブ -->
    <div id="review-content" class="tab-content">
        <?php
        if (count($result_review) > 0) {
            foreach ($result_review as $review) {
                echo "<div class='review-item'>";
                echo "<a href='goods.php?shop_id={$row['shop_id']}&shop_group={$row['shop_group']}'>";
                echo "<img class='image' src='data:{$mimeType};base64,{$encodedImg}' alt='goods img' class='sale-product-image'></br>";
                echo "</a>";
                echo "<p>商品名: {$review['goods']}</p>";
                echo "<p>評価: {$review['rate']}</p>";
                echo "<p>レビュー内容: {$review['review_content']}</p>";
                echo "<p>投稿日: {$review['created_at']}</p>";
                echo "</div><hr>";
            }
        } else {
            echo $translations['No reviews available'];
        }
        ?>
    </div>

    <script>
        function showTab(tabId) {
            // 非表示にするタブを全て非表示
            document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

            // 選択されたタブをアクティブにする
            document.getElementById(tabId + '-tab').classList.add('active');
            document.getElementById(tabId + '-content').classList.add('active');

            // URLにクエリパラメータを追加
            window.location.hash = '#' + tabId;
        }

        // デフォルトでタブの選択をURLのハッシュに基づいて設定
        window.onload = function() {
            const tab = window.location.hash.replace('#', '');
            if (tab) {
                showTab(tab);
            } else {
                showTab('history'); // デフォルトで「注文履歴」タブを表示
            }
        }
    </script>
</body>

</html>