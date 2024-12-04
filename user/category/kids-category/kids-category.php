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
    <a href="../category.php" class="tab-button <?php echo basename($_SERVER['PHP_SELF']) == 'category.php' ? 'active' : ''; ?>" data-target="all">ALL</a>
    <a href="../men-category/men-category.php" class="tab-button <?php echo basename($_SERVER['PHP_SELF']) == 'men-category.php' ? 'active' : ''; ?>" data-target="men">メンズ</a>
    <a href="../woman-category/woman-category.php" class="tab-button <?php echo basename($_SERVER['PHP_SELF']) == 'woman-category.php' ? 'active' : ''; ?>" data-target="woman">レディース</a>
    <a href="kids-category.php" class="tab-button <?php echo basename($_SERVER['PHP_SELF']) == 'kids-category.php' ? 'active' : ''; ?>" data-target="kids">キッズ</a>
</nav>


            <div class="category-list">
            <ul class="category-name">
                <li><a href="#tops">トップス</a></li>
                <li><a href="#jacket">ジャケット/アウター</a></li>
                <li><a href="#pants">パンツ</a></li>
                <li><a href="#skirt">スカート</a></li>
                <li><a href="#onepiece">ワンピース</a></li>
            </ul>
            <div>
                <div id="tops">
                    <h2>トップス</h2>
                    <?php
                    $stmt = $dbh->prepare("SELECT * FROM subcategory WHERE category_id = 1");
                    $stmt->execute();

                    // データをフェッチして表示
                    $categories = $stmt->fetchAll();

                    if ($categories) {
                        echo "<ul>";  // リスト形式で表示
                        foreach ($categories as $category) {
                            echo "<li>";

                            // サブカテゴリ名に応じて遷移先を決定
                            $subcategoryName = strtolower($category['subcategory_name']);
                            $subcategorycode = strtolower($category['subcategory_code']);
                            
                            $url = '';
                                $url = '../tops/' . $subcategorycode . '.php';

                            echo "<a href='{$url}'> " . htmlspecialchars($category['subcategory_name']) . "</a><br>";
                            echo "</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "該当するカテゴリーはありません。";
                    }
                    ?>
                </div>
                <div id="jacket">
                <h2>ジャケット/アウター</h2>

                    <?php
                    $stmt = $dbh->prepare("SELECT * FROM subcategory WHERE category_id = 2");
                    $stmt->execute();

                    // データをフェッチして表示
                    $categories = $stmt->fetchAll();

                    if ($categories) {
                        echo "<ul>";  // リスト形式で表示
                        foreach ($categories as $category) {
                            echo "<li>";

                            // サブカテゴリ名に応じて遷移先を決定
                            $subcategoryName = strtolower($category['subcategory_name']);
                            $subcategorycode = strtolower($category['subcategory_code']);
                            
                            $url = '';
                                $url = '../jacket-outerwear/' . $subcategorycode . '.php';

                            echo "<a href='{$url}'> " . htmlspecialchars($category['subcategory_name']) . "</a><br>";
                            echo "</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "該当するカテゴリーはありません。";
                    }
                    ?>
                </div>
                <div id="pants">
                <h2>パンツ</h2>

                    <?php
                    $stmt = $dbh->prepare("SELECT * FROM subcategory WHERE category_id = 3");
                    $stmt->execute();

                    // データをフェッチして表示
                    $categories = $stmt->fetchAll();

                    if ($categories) {
                        echo "<ul>";  // リスト形式で表示
                        foreach ($categories as $category) {
                            echo "<li>";

                            // サブカテゴリ名に応じて遷移先を決定
                            $subcategoryName = strtolower($category['subcategory_name']);
                            $subcategorycode = strtolower($category['subcategory_code']);
                            
                            $url = '';
                                $url = '../pants/' . $subcategorycode . '.php';

                            echo "<a href='{$url}'> " . htmlspecialchars($category['subcategory_name']) . "</a><br>";
                            echo "</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "該当するカテゴリーはありません。";
                    }
                    ?>
                </div>
                <div id="skirt">
                <h2>スカート</h2>

                    <?php
                    $stmt = $dbh->prepare("SELECT * FROM subcategory WHERE category_id = 4");
                    $stmt->execute();

                    // データをフェッチして表示
                    $categories = $stmt->fetchAll();

                    if ($categories) {
                        echo "<ul>";  // リスト形式で表示
                        foreach ($categories as $category) {
                            echo "<li>";

                            // サブカテゴリ名に応じて遷移先を決定
                            $subcategoryName = strtolower($category['subcategory_name']);
                            $subcategorycode = strtolower($category['subcategory_code']);
                            
                            $url = '';
                                $url = '../skirt/' . $subcategorycode . '.php';

                            echo "<a href='{$url}'> " . htmlspecialchars($category['subcategory_name']) . "</a><br>";
                            echo "</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "該当するカテゴリーはありません。";
                    }
                    ?>
                </div>
                <div id="onepiece">
                <h2>ワンピース</h2>

                    <?php
                    $stmt = $dbh->prepare("SELECT * FROM subcategory WHERE category_id = 5");
                    $stmt->execute();

                    // データをフェッチして表示
                    $categories = $stmt->fetchAll();

                    if ($categories) {
                        echo "<ul>";  // リスト形式で表示
                        foreach ($categories as $category) {
                            echo "<li>";

                            // サブカテゴリ名に応じて遷移先を決定
                            $subcategoryName = strtolower($category['subcategory_name']);
                            $subcategorycode = strtolower($category['subcategory_code']);
                            
                            $url = '';
                                $url = '../onepiece/' . $subcategorycode . '.php';

                            echo "<a href='{$url}'> " . htmlspecialchars($category['subcategory_name']) . "</a><br>";
                            echo "</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "該当するカテゴリーはありません。";
                    }
                    ?>
                </div>

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