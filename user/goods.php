<?php
include "../../db_open.php"; // PDO接続のファイルをインクルード
include "../head.php";
include "../header.php";
echo "<link rel='stylesheet' href='header.css'>";
echo "<link rel='stylesheet' href='./category.css'>";

// ログインしていないときにエラーが出ない処理
if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];
} else {
    $userId = null;
}
?>

<!DOCTYPE html>
<?php
include "../head.php";
?>
<link rel="stylesheet" href="goods.css">
<?php
$shop_id = isset($_GET['shop_id']) ? $_GET['shop_id'] : '';
if ($shop_id) {
    // 商品情報とブランド情報を取得
    // $sql = "SELECT shop.*, brand.brand_name FROM shop
    //     LEFT OUTER JOIN brand ON brand.brand_id = shop.brand_id
    //     WHERE shop.shop_id = :shop_id";
    
    $sql = "SELECT shop.*, brand.brand_name, sale.sale FROM shop
    LEFT OUTER JOIN brand ON brand.brand_id = shop.brand_id
    LEFT OUTER JOIN sale ON sale.sale_id = shop.sale_id
    WHERE shop.shop_id = :shop_id";

    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':shop_id', $shop_id);
    $stmt->execute();
    $goodsResult = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($goodsResult) {
        // 商品情報を表示
        echo "<h1>{$goodsResult['goods']}</h1>";

        // ブランド名の表示
        echo "<p>ブランド名：{$goodsResult['brand_name']}</p>";

        // 商品情報の他の部分を表示
        echo "<p>値段：{$goodsResult['price']}</p>";

// 商品価格がセール中の場合、セール価格を計算
if ($goodsResult['sale_id']) {
    $sale_id = $goodsResult['sale_id'];
    // saleテーブルから割引率を取得
    $sql_sale = "SELECT sale FROM sale WHERE sale_id = :sale_id";
    $stmt_sale = $dbh->prepare($sql_sale);
    $stmt_sale->bindParam(':sale_id', $sale_id);
    $stmt_sale->execute();
    $sale = $stmt_sale->fetch(PDO::FETCH_ASSOC);

    if ($sale) {
        $discounted_price = $goodsResult['price'] * (1 - $sale['sale'] / 100);
        echo "<p>割引後価格：{$discounted_price}円</p>";
    }
}


        echo "<p>商品説明：{$goodsResult['explanation']}</p>";

        // 商品に紐づくすべての画像を取得
        $sql_images = "SELECT img FROM image WHERE shop_id = :shop_id";
        $stmt_images = $dbh->prepare($sql_images);
        $stmt_images->bindParam(':shop_id', $shop_id);
        $stmt_images->execute();
        
        // 画像がある場合
        if ($stmt_images->rowCount() > 0) {
            echo "<h3>商品画像</h3>";
            while ($image = $stmt_images->fetch(PDO::FETCH_ASSOC)) {
                $imgBlob = $image['img'];
                
                // BLOB型の画像データをBase64エンコードして表示
                if (!empty($imgBlob)) {
                    // MIMEタイプを動的に取得
                    $finfo = new finfo(FILEINFO_MIME_TYPE);
                    $mimeType = $finfo->buffer($imgBlob); // BLOBデータからMIMEタイプを取得

                    // Base64にエンコード
                    $encodedImg = base64_encode($imgBlob);

                    // エンコードされた画像をimgタグに表示
                    echo "<p><img src='data:{$mimeType};base64,{$encodedImg}' alt='商品画像' style='width: 150px; height: auto;'></p>";
                }
            }
        } else {
            echo "<p>画像がありません。</p>";
        }
        
    } else {
        echo "<p>該当する商品が見つかりません。</p>";
    }
} else {
    echo "<p>商品IDが指定されていません。</p>";
}
?>
<body>
    <nav class="tabs">
        <div class="tab-button active" data-target="info">アイテム説明</div>
        <div class="tab-button" data-target="size">サイズ</div>
        <div class="tab-button" data-target="review">レビュー</div>
    </nav>

    <div id="info" class="tab-content active-tab">
        <h1>アイテム説明</h1>
    </div>
    <div id="size" class="tab-content">
        <h1>サイズ</h1>
    </div>
    <div id="review" class="tab-content">
        <h1>レビュー</h1>
    </div>

    <script>
        // タブボタンのクリックイベント
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', () => {
                // すべてのタブコンテンツとタブボタンのactiveクラスをリセット
                document.querySelectorAll('.tab-content').forEach(tab => {
                    tab.classList.remove('active-tab');
                });
                document.querySelectorAll('.tab-button').forEach(tabButton => {
                    tabButton.classList.remove('active');
                });

                // クリックされたタブボタンと対応するタブコンテンツにactiveクラスを追加
                const targetId = button.getAttribute('data-target');
                document.getElementById(targetId).classList.add('active-tab');
                button.classList.add('active');
            });
        });
    </script>
</body>
</html>