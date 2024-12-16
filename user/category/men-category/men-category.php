<?php
include "../../../../db_open.php";
include "../../../head.php";
include "../../../header.php";
echo "<link rel='stylesheet' href='../../header.css'>";
echo "<link rel='stylesheet' href='../category.css'>";

$sql = "SELECT * FROM category";  // categoryテーブルのすべてのカテゴリーを取得
$result = $dbh->prepare($sql);
$result->execute();
?>

<!DOCTYPE html>
<html lang="ja">

<body>
    <div class="container">
        <header>
            <h1>カテゴリー検索</h1>
        </header>

        <body>
            <nav class="tabs">
                <a href="../category.php" class="tab-button active" data-target="all">ALL</a>
                <a href="men-category.php" class="tab-button" data-target="men">メンズ</a>
                <a href="../woman-category/woman-category.php" class="tab-button" data-target="woman">レディース</a>
                <a href="../kids-category/kids-category.php" class="tab-button" data-target="kids">キッズ</a>
            </nav>

            <div class="category-list">
            <h2>カテゴリー一覧</h2>
            <ul>
                <?php
                // PDOのfetch()メソッドを使ってデータを表示
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    echo "<li><a href='#'>" . htmlspecialchars($row['category_name']) . "</a></li>";
                }
                ?>
            </ul>
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