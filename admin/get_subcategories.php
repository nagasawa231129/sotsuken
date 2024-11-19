<?php
include './../../db_open.php';

if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];

    // サブカテゴリーを取得
    $subcategory_sql = "SELECT subcategory_id, subcategory_name FROM subcategory WHERE category_id = ?";
    $stmt_subcategory = $dbh->prepare($subcategory_sql);
    $stmt_subcategory->execute([$category_id]);

    // サブカテゴリーの選択肢を表示
    while ($subcategory = $stmt_subcategory->fetch(PDO::FETCH_ASSOC)) {
        echo "<option value='{$subcategory['subcategory_id']}'>{$subcategory['subcategory_name']}</option>";
    }
}
?>
