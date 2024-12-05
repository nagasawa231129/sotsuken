<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_items'])) {
    $selected_items = $_POST['selected_items']; // 選択された商品のIDが配列で渡される
    echo '<h1>選択された商品</h1>';
    foreach ($selected_items as $item) {
        echo '<p>商品ID: ' . htmlspecialchars($item, ENT_QUOTES, 'UTF-8') . '</p>';
    }
} else {
    echo '<p>商品が選択されていません。</p>';
}
?>
