<?php
// データベース接続
include './../../db_open.php';

// 送信されたデータを取得
$brands = $_POST['brand'];
$goods = $_POST['goods'];
$prices = $_POST['price'];
$sizes = $_POST['size'];
$colors = $_POST['color'];
$categories = $_POST['category'];
$subcategories = $_POST['subcategory'];
$genders = $_POST['gender'];

// エラーチェック（空のフィールドがあるか確認）
if (empty($brands) || empty($goods) || empty($prices) || empty($sizes) || empty($colors) || empty($categories) || empty($subcategories) || empty($genders)) {
    echo "すべての項目を入力してください。";
    exit;
}

// トランザクション開始
try {
    $dbh->beginTransaction();

    // 商品情報をデータベースに挿入
    for ($i = 0; $i < count($brands); $i++) {
        $sql = "INSERT INTO shop (brand_id, goods, price, size, color, category_id, subcategory_id, gender) 
                VALUES (:brand_id, :goods_name, :price, :size, :color, :category_id, :subcategory_id, :gender_id)";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':brand_id', $brands[$i], PDO::PARAM_INT);
        $stmt->bindParam(':goods_name', $goods[$i], PDO::PARAM_STR);
        $stmt->bindParam(':price', $prices[$i], PDO::PARAM_INT);
        $stmt->bindParam(':size', $sizes[$i], PDO::PARAM_INT);
        $stmt->bindParam(':color', $colors[$i], PDO::PARAM_INT);
        $stmt->bindParam(':category_id', $categories[$i], PDO::PARAM_INT);
        $stmt->bindParam(':subcategory_id', $subcategories[$i], PDO::PARAM_INT);
        $stmt->bindParam(':gender_id', $genders[$i], PDO::PARAM_INT);
        
        $stmt->execute();
    }

    // トランザクションをコミット
    $dbh->commit();

    echo "商品が正常に追加されました。";
    header('Location: success_page.php'); // 追加後にリダイレクト
    exit;

} catch (PDOException $e) {
    // エラーが発生した場合はロールバック
    $dbh->rollBack();
    echo "エラー: " . $e->getMessage();
    exit;
}
?>
