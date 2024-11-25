<?php
// データベース接続
include './../../db_open.php';

// カテゴリIDを取得
$category_id = $_GET['category_id'];

// サブカテゴリーの情報を取得
$sql = "SELECT subcategory_id, subcategory_name FROM subcategory WHERE category_id = ?";
$stmt = $dbh->prepare($sql);
$stmt->execute([$category_id]);

// サブカテゴリーの選択肢を生成
$options = "";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $options .= "<option value='{$row['subcategory_id']}'>{$row['subcategory_name']}</option>";
}

// サブカテゴリーの選択肢を返す
echo $options;
?>


