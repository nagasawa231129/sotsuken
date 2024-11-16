<?php
include "../../db_open.php";
include "../head.php";
include "../header.php";
echo "<link rel='stylesheet' href='header.css'>";
echo "<link rel='stylesheet' href='./category.css'>";
?>

<form action="advanced_search.php" method="get">
    <!-- 価格帯 -->
    <label for="min_price">価格帯：</label>
    <input type="number" name="min_price" placeholder="最小価格" value="<?php echo isset($_GET['min_price']) ? $_GET['min_price'] : ''; ?>" />
    ～ 
    <input type="number" name="max_price" placeholder="最大価格" value="<?php echo isset($_GET['max_price']) ? $_GET['max_price'] : ''; ?>" />
    
    <!-- セール対象商品 -->
    <label for="sale_subject">セール対象：</label>
    <select name="sale_subject">
        <option value="">すべて</option>
        <option value="1" <?php echo (isset($_GET['sale_subject']) && $_GET['sale_subject'] == '1') ? 'selected' : ''; ?>>セール対象</option>
        <option value="0" <?php echo (isset($_GET['sale_subject']) && $_GET['sale_subject'] == '0') ? 'selected' : ''; ?>>セールなし</option>
    </select>
    
    <input type="submit" value="検索">
</form>

<?php
// フィルタリング条件を取得
$min_price = isset($_GET['min_price']) ? $_GET['min_price'] : null;
$max_price = isset($_GET['max_price']) ? $_GET['max_price'] : null;
$sale_subject = isset($_GET['sale_subject']) ? $_GET['sale_subject'] : null;

// SQLクエリの作成
$sql = "SELECT * FROM shop WHERE 1=1";

// 価格帯フィルターの追加
if ($min_price !== null) {
    $sql .= " AND price >= ?";
}

// 最大価格フィルターの追加
if ($max_price !== null) {
    $sql .= " AND price <= ?";
}

// セール対象商品フィルターの追加
if ($sale_subject !== null) {
    $sql .= " AND sale_subject = ?";
}

// プリペアドステートメントでSQLを実行
$stmt = $dbh->prepare($sql);

// バインドする値
$params = [];
if ($min_price !== null) {
    $params[] = $min_price;
}
if ($max_price !== null) {
    $params[] = $max_price;
}
if ($sale_subject !== null) {
    $params[] = $sale_subject;
}

// パラメータを渡して実行
$stmt->execute($params);

// 検索結果を取得
$results = $stmt->fetchAll();

// 検索結果がない場合の表示
if (empty($results)) {
    echo "該当する商品はありません。";
} else {
    // 検索結果の表示
    foreach ($results as $row) {
        echo "商品名: " . $row['goods'] . "<br>";
        echo "価格: " . $row['price'] . "<br>";

        // 割引率を計算
        $sale_subject = $row['sale_subject'];
        if ($sale_subject >= 1 && $sale_subject <= 9) {
            $discount_percentage = $sale_subject * 10;  // 1なら10%、2なら20%など
            echo "割引率: " . $discount_percentage . "%<br>";
        } else {
            echo "割引率: 0% (セールなし)<br>";
        }

        echo "<br>";
    }
}
?>
