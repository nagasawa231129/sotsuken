<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/sotsuken/vendor/autoload.php';
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

$history_sql = "SELECT c.cart_id, c.shop_id, s.goods, c.quantity, s.price, c.order_date, c.trade_situation, r.review_id, s.thumbnail, g.shop_group, c.cart_group
                FROM cart_detail c
                JOIN shop s ON c.shop_id = s.shop_id
                LEFT JOIN reviews r ON c.shop_id = r.shop_id
                LEFT OUTER JOIN `group` g ON g.shop_id = s.shop_id
                WHERE c.user_id = :user_id and (c.trade_situation = '3' OR c.trade_situation = '2' OR c.trade_situation = '1')";

$pending_sql = "SELECT c.cart_id, c.shop_id, s.goods, c.quantity, s.price, c.order_date, c.trade_situation, r.review_id, s.thumbnail, g.shop_group, c.cart_group
                FROM cart_detail c
                JOIN shop s ON c.shop_id = s.shop_id
                LEFT JOIN reviews r ON c.shop_id = r.shop_id
                LEFT OUTER JOIN `group` g ON g.shop_id = s.shop_id
                WHERE c.user_id = :user_id AND c.trade_situation = '2'";

$shipped_sql = "SELECT c.cart_id, c.shop_id, s.goods, c.quantity, s.price, c.order_date, c.trade_situation, r.review_id, s.thumbnail, g.shop_group, c.cart_group
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.toggle-barcode-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const barcodeContainer = this.nextElementSibling; // ボタンの次の要素（バーコードコンテナ）

                    if (barcodeContainer.style.display === 'none') {
                        barcodeContainer.style.display = 'block'; // バーコードを表示
                        this.textContent = 'バーコードを非表示'; // ボタンのテキスト変更

                        const barcodeData = this.closest('.product-item').querySelector('.barcode-content').textContent; // バーコードデータ
                        console.log(barcodeData);

                        fetch(`https://y231129.daa.jp/sotsuken/sotsuken/user/process_barcode.php?barcode=${encodeURIComponent(barcodeData)}`)
                            .then(response => response.json())
                            .then(data => {
                                console.log(data); // サーバーからのレスポンスを確認
                                if (data.status === 'success') {
                                    alert("購入処理が完了しました！");
                                } else {
                                    alert("エラー: " + data.message);
                                }
                            })
                            .catch(error => console.error('エラー:', error));
                    } else {
                        barcodeContainer.style.display = 'none'; // バーコードを非表示
                        this.textContent = 'バーコードを表示'; // ボタンのテキスト変更
                    }
                });
            });
        });
    </script>
</head>

<body>
    <h1><?php echo $translations['Order History'] ?></h1>
    <div class="tabs">
        <div class="tab <?php echo isset($_GET['tab']) && $_GET['tab'] == 'history' ? 'active' : ''; ?>" id="history-tab" onclick="showTab('history')"><?php echo $translations['Order History'] ?></div>
        <div class="tab <?php echo isset($_GET['tab']) && $_GET['tab'] == 'unpaid' ? 'active' : ''; ?>" id="unpaid-tab" onclick="showTab('unpaid')"><?php echo $translations['Unpaid'] ?></div>
        <div class="tab <?php echo isset($_GET['tab']) && $_GET['tab'] == 'pending' ? 'active' : ''; ?>" id="pending-tab" onclick="showTab('pending')"><?php echo $translations['Items Pending Shipment'] ?></div>
        <div class="tab <?php echo isset($_GET['tab']) && $_GET['tab'] == 'shipped' ? 'active' : ''; ?>" id="shipped-tab" onclick="showTab('shipped')"><?php echo $translations['Shipped Items'] ?></div>
        <div class="tab <?php echo isset($_GET['tab']) && $_GET['tab'] == 'review' ? 'active' : ''; ?>" id="review-tab" onclick="showTab('review')"><?php echo $translations['Review'] ?></div>
    </div>

    <div id="history-content" class="tab-content <?php echo isset($_GET['tab']) && $_GET['tab'] == 'history' ? 'active' : ''; ?>">
        <?php
            if (count($result_history) > 0) {
            $currentCartGroup = null;

            foreach ($result_history as $row) {
                if ($currentCartGroup != $row['cart_group']) {
                    if ($currentCartGroup !== null) {
                        echo "</div><hr>";
                    }
                    $currentCartGroup = $row['cart_group'];
                    echo "<h3>Cart Group: {$currentCartGroup}</h3>";
                    echo "<div class='cart-group'>";
                }

                $imgBlob = $row['thumbnail'];
                $mimeType = 'image/png';
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->buffer($imgBlob);
                $encodedImg = base64_encode($imgBlob);

                echo "<div class='order-item'>";
                echo "<a href='goods.php?shop_id={$row['shop_id']}&shop_group={$row['shop_group']}'>";
                echo "<img class='image' src='data:{$mimeType};base64,{$encodedImg}' alt='goods img' class='sale-product-image'></br>";
                echo "</a>";
                echo "<p>商品名: {$row['goods']}</p>";
                echo "<p>購入日: {$row['order_date']}</p>";

                if (!empty($row['review_id'])) {
                    echo "<p>レビュー済み</p>";
                } else {
                    echo "<a href='review.php?shop_id={$row['shop_id']}'>レビューを書く</a>";
                }
                echo "</div>";
            }
            echo "</div>";
        } else {
            echo $translations['No order history available'];
        }
        ?>
    </div>

    <div id="unpaid-content" class="tab-content <?php echo isset($_GET['tab']) && $_GET['tab'] == 'unpaid' ? 'active' : ''; ?>">
        <?php
        if (isset($userId)) {
            $sql = "SELECT cart_detail.order_date, cart_detail.shop_id, shop.thumbnail, shop.goods, cart_detail.cart_id, cart_detail.cart_group, g.shop_group
            FROM cart_detail
            JOIN `group` g ON g.shop_id = cart_detail.shop_id
            JOIN shop ON cart_detail.shop_id = shop.shop_id
            WHERE cart_detail.user_id = :user_id AND cart_detail.trade_situation = '1'
            ORDER BY cart_detail.order_date ASC, cart_detail.cart_group ASC";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
        ?>

        <?php
            if ($stmt->rowCount() > 0) {
                $currentCartGroup = null;

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    if ($currentCartGroup != $row['cart_group']) {
                        if ($currentCartGroup !== null) {
                            echo <<<EOF
                        <form method="GET" action="result.php">
                            <input type="hidden" name="cart_group" value="{$encodedCartGroup}">
                            <input type="hidden" name="user_id" value="{$encodedUserId}">
                            <input type="submit" value="支払う">
                        </form>
                        </div><hr>
                        EOF;
                        }

                        $currentCartGroup = $row['cart_group'];
                        $encodedCartGroup = urlencode($currentCartGroup);
                        $encodedUserId = urlencode($userId);

                        echo "<h3>Cart Group: {$currentCartGroup}</h3>";
                        echo "<div class='cart-group'>";
                    }

                    $imgBlob = $row['thumbnail'];
                    $mimeType = 'image/png';
                    $finfo = new finfo(FILEINFO_MIME_TYPE);
                    $mimeType = $finfo->buffer($imgBlob);
                    $encodedImg = base64_encode($imgBlob);

                    echo "<div class='product-item'>";
                    echo "<a href='goods.php?shop_id={$row['shop_id']}&shop_group={$row['shop_group']}'>";
                    echo "<img class='image' src='data:{$mimeType};base64,{$encodedImg}' alt='goods img' class='sale-product-image'></br>";
                    echo "</a>";
                    echo "<p>商品名: {$row['goods']}</p>";
                    echo "<p>購入日: {$row['order_date']}</p>";
                }

                if ($currentCartGroup !== null) {
                    echo <<<EOF
                <form method="GET" action="result.php">
                    <input type="hidden" name="cart_group" value="{$encodedCartGroup}">
                    <input type="hidden" name="user_id" value="{$encodedUserId}">
                    <input type="submit" value="支払う">
                </form>
                </div>
                EOF;
                }
            } else {
                echo "<p>未入金の商品はありません。</p>";
            }
        }
        ?>
    </div>

    <div id="pending-content" class="tab-content <?php echo isset($_GET['tab']) && $_GET['tab'] == 'pending' ? 'active' : ''; ?>">
        <?php
        if (count($result_pending) > 0) {
            $currentCartGroup = null;
            foreach ($result_pending as $row) {
                if ($currentCartGroup != $row['cart_group']) {
                    if ($currentCartGroup !== null) {
                        echo "</div><hr>";
                    }
                    $currentCartGroup = $row['cart_group'];
                    echo "<h3>Cart Group: {$currentCartGroup}</h3>";
                    echo "<div class='cart-group'>";
                }

                $imgBlob = $row['thumbnail'];
                $mimeType = 'image/png';
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->buffer($imgBlob);
                $encodedImg = base64_encode($imgBlob);

                echo "<div class='order-item'>";
                echo "<a href='goods.php?shop_id={$row['shop_id']}&shop_group={$row['shop_group']}'>";
                echo "<img class='image' src='data:{$mimeType};base64,{$encodedImg}' alt='goods img' class='sale-product-image'></br>";
                echo "</a>";
                echo "<p>商品名: {$row['goods']}</p>";
                echo "<p>購入日: {$row['order_date']}</p>";

                if ($row['trade_situation'] != 'shipped' && !isset($row['review_id'])) {
                    echo "<a href='review.php?shop_id={$row['shop_id']}'>レビューを書く</a>";
                } else {
                    echo "<p>レビュー済み</p>";
                }
                echo "</div>";
            }
            echo "</div>";
        } else {
            echo $translations['No items pending shipment'];
        }
        ?>
    </div>

    <div id="shipped-content" class="tab-content <?php echo isset($_GET['tab']) && $_GET['tab'] == 'shipped' ? 'active' : ''; ?>">
        <?php
        if (count($result_shipped) > 0) {
            $currentCartGroup = null;
            foreach ($result_shipped as $row) {
                if ($currentCartGroup != $row['cart_group']) {
                    if ($currentCartGroup !== null) {
                        echo "</div><hr>";
                    }
                    $currentCartGroup = $row['cart_group'];
                    echo "<h3>Cart Group: {$currentCartGroup}</h3>";
                    echo "<div class='cart-group'>";
                }

                $imgBlob = $row['thumbnail'];
                $mimeType = 'image/png';
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->buffer($imgBlob);
                $encodedImg = base64_encode($imgBlob);

                echo "<div class='order-item'>";
                echo "<a href='goods.php?shop_id={$row['shop_id']}&shop_group={$row['shop_group']}'>";
                echo "<img class='image' src='data:{$mimeType};base64,{$encodedImg}' alt='goods img' class='sale-product-image'></br>";
                echo "</a>";
                echo "<p>商品名: {$row['goods']}</p>";
                echo "<p>購入日: {$row['order_date']}</p>";

                if (!empty($row['review_id'])) {
                    echo "<p>レビュー済み</p>";
                } else {
                    echo "<a href='review.php?shop_id={$row['shop_id']}'>レビューを書く</a>";
                }
                echo "</div>";
            }
            echo "</div>";
        } else {
            echo $translations['No items shipped'];
        }
        ?>
    </div>

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
            document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

            document.getElementById(tabId + '-tab').classList.add('active');
            document.getElementById(tabId + '-content').classList.add('active');

            window.location.hash = '#' + tabId;
        }

        window.onload = function() {
            const tab = window.location.hash.replace('#', '');
            if (tab) {
                showTab(tab);
            } else {
                showTab('history');
            }
            const barcodeImages = document.querySelectorAll('.barcode-image');
            barcodeImages.forEach(img => {
                img.onload = function() {
                    console.log("バーコード画像の読み込み完了");
                };
            });
        }
    </script>
</body>
</html>