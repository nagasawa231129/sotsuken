<?php
include "../../db_open.php";
include "../header.php";
include "../head.php";

// ログインチェック
$userId = isset($_SESSION['id']) ? $_SESSION['id'] : null;
$user_name = isset($_SESSION['login']) ? $_SESSION['display_name'] : 'ゲスト';

// 言語設定
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ja';
$lang_file = __DIR__ . "/{$lang}.php";

if (file_exists($lang_file)) {
    include($lang_file);
} else {
    die("Error: Language file not found.");
}

try {
    // sale_idが10未満の商品を取得 (割引が適用されている商品)
    $sql = "SELECT * FROM shop WHERE sale_id < 10";
    $stmt = $dbh->query($sql);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("データベースエラー: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="toppage.css">
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="sale.css">
    <title>セール商品一覧</title>
</head>

<body>
    <h1>セール商品一覧</h1>

    <?php if (count($products) > 0): ?>
        <div class="product-container">
            <?php foreach ($products as $product): ?>
                <?php
                $imgBlob = $product['thumbnail'];
                $mimeType = 'image/png'; // MIMEタイプはデフォルトを設定（例としてPNG）

                // MIMEタイプを動的に取得
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->buffer($imgBlob); // BLOBデータからMIMEタイプを取得

                // Base64にエンコード
                $encodedImg = base64_encode($imgBlob);
                // sale_idに基づいて割引率を計算
                $discount_rate = $product['sale_id'] * 10; // sale_idが1なら10%割引
                $sale_price = $product['price'] * (1 - $discount_rate / 100);
                ?>
                <div class="product-card">
                    <a href="goods.php?shop_id=<?= htmlspecialchars($product['shop_id']) ?>" class="product-link">
                        <img
                            src="data:<?php echo htmlspecialchars($mimeType, ENT_QUOTES, 'UTF-8'); ?>;base64,<?php echo htmlspecialchars($encodedImg, ENT_QUOTES, 'UTF-8'); ?>"
                            alt="goods img"
                            style="height: 100px; width: 100px; object-fit: cover; display: block; margin: 0 auto;">
                        <div class="product-name"><?= htmlspecialchars($product['goods']) ?></div>
                        <div class="product-price">¥<?= number_format($sale_price) ?></div>
                        <div class="original-price">¥<?= number_format($product['price']) ?></div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>現在セール中の商品はありません。</p>
    <?php endif; ?>
</body>

</html>