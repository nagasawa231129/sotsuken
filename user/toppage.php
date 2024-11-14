<?php
include "../../db_open.php";

// ログインしていないときにエラーが出ない処理
if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];
} else {
    $userId = null;
}
?>

<!DOCTYPE html>
<?php
include "../header.php";
include "../head.php";
?>
<link rel="stylesheet" href="top.css">
<link rel="stylesheet" href="header.css">

<?php
$itemsPerPage = 85;

// 現在のページ番号を取得（デフォルトは1）
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// ページ番号が1より小さい場合は1に設定
if ($page < 1) {
    $page = 1;
}

// OFFSETを計算
$offset = ($page - 1) * $itemsPerPage;

// 総企業数を取得
$totalQuery = "SELECT COUNT(*) FROM shop";
$totalResult = $dbh->query($totalQuery);
$totalItems = $totalResult->fetchColumn();

// 総ページ数を計算
$totalPages = ceil($totalItems / $itemsPerPage);

// brandテーブルからbrand_nameを取得するSQLクエリ
$sql = "SELECT shop.*, brand.brand_name, image.img
        FROM shop 
        LEFT outer JOIN brand ON brand.brand_id = shop.brand_id 
        left outer join image on image.img_id = shop.img_id
        LIMIT :limit OFFSET :offset";

$stmt = $dbh->prepare($sql);
$stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    while ($rec = $stmt->fetch()) {
        // BLOB型の画像データをBase64エンコードして表示
        $imgBlob = $rec['img'];
        $mimeType = 'image/png'; // MIMEタイプはデフォルトを設定しておく（例としてPNG）

        // MIMEタイプを動的に取得する例
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($imgBlob); // BLOBデータからMIMEタイプを取得

        // Base64にエンコード
        $encodedImg = base64_encode($imgBlob);

        // 商品情報を表示
        echo "<div>";
        echo "<a href='goods.php?shop_id={$rec['shop_id']}'><img src='data:{$mimeType};base64,{$encodedImg}' alt='goods img' height=100px width=100px></a>";
        echo "<p>ブランド：{$rec['brand_name']}</p>"; // brand_nameを表示
        echo "<a href='goods.php?shop_id={$rec['shop_id']}'>商品名：{$rec['goods']}</a>";
        echo "<p>値段：{$rec['price']}</p>";
        echo "<p>商品説明：{$rec['explanation']}</p>";
        echo "</div>";
        echo "<br>";
    }
} else {
    echo "<p>商品が見つかりません。</p>";
}

?>

<div class="pagination">
    <?php if ($totalPages > 1): // ページ数が1ページより多い場合のみ表示 ?>
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>">前へ</a>
        <?php endif; ?>

        <!-- 最初のページ -->
        <?php if ($page > 3): ?>
            <a href="?page=1">1</a>
            <span>...</span> <!-- 省略 -->
        <?php endif; ?>

        <!-- ページ番号のリンクを表示 -->
        <?php for ($i = max(1, $page - 1); $i <= min($totalPages, $page + 1); $i++): ?>
            <?php if ($i == $page): ?>
                <span><?php echo $i; ?></span> <!-- 現在のページ -->
            <?php else: ?>
                <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a> <!-- 他のページ -->
            <?php endif; ?>
        <?php endfor; ?>

        <!-- 最後のページ -->
        <?php if ($page < $totalPages - 2): ?>
            <span>...</span> <!-- 省略 -->
            <a href="?page=<?php echo $totalPages; ?>"><?php echo $totalPages; ?></a>
        <?php endif; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?php echo $page + 1; ?>">次へ</a>
        <?php endif; ?>
    <?php endif; ?>
</div>
</html>
