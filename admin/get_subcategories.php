<?php
include './../../db_open.php'; // DB接続ファイル

// カテゴリIDを取得
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

if ($category_id > 0) {
    // サブカテゴリを取得
    $sql = "SELECT subcategory_id, subcategory_name FROM subcategory WHERE category_id = ?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute([$category_id]);

    $subcategories = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $subcategories[] = $row;
    }

    // JSON形式で返す
    header('Content-Type: application/json');
    echo json_encode($subcategories);
} else {
    // カテゴリIDが無効な場合は空の配列を返す
    echo json_encode([]);
}
