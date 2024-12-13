<?php
include "../../../../db_open.php";
include "../../../head.php";
include "../../../header.php";
echo "<link rel='stylesheet' href='../../header.css'>";
echo "<link rel='stylesheet' href='../category.css'>";

$gender = isset($_GET['gender']) ? $_GET['gender'] : 'ALL';  // デフォルトはALL


$sql = "SELECT * FROM category";  // categoryテーブルのすべてのカテゴリーを取得
$result = $dbh->prepare($sql);
$result->execute();
?>

<!DOCTYPE html>
<html lang="ja">

<body>
    <div class="container">
        <header>
            <h1><?php echo $translations['Search By Category'] ?></h1>
        </header>


        <nav class="tabs">
            <a href="../category.php?gender=ALL" class="tab-button <?php echo basename($_SERVER['PHP_SELF']) == '../category.php' ? 'active' : ''; ?>" data-target="all"><?php echo $translations['All'] ?></a>
            <a href="../men-category/men-category.php?gender=man" class="tab-button <?php echo basename($_SERVER['PHP_SELF']) == '../men-category/men-category.php' ? 'active' : ''; ?>" data-target="men"><?php echo $translations['Mens'] ?></a>
            <a href="../woman-category/woman-category.php?gender=woman" class="tab-button <?php echo basename($_SERVER['PHP_SELF']) == '../woman-category/woman-category.php' ? 'active' : ''; ?>" data-target="woman"><?php echo $translations['Ledies'] ?></a>
            <a href="kids-category.php?gender=kids" class="tab-button <?php echo basename($_SERVER['PHP_SELF']) == 'kids-category.php' ? 'active' : ''; ?>" data-target="kids"><?php echo $translations['Kids'] ?></a>
        </nav>

        <div class="category-list">
            <ul class="category-name">
                <li><a href="#tops"><?php echo $translations['Tops'] ?></a></li>
                <li><a href="#jacket"><?php echo $translations['Jacket'] ?></a></li>
                <li><a href="#pants"><?php echo $translations['Pants'] ?></a></li>
                <li><a href="#skirt"><?php echo $translations['Skirt'] ?></a></li>
                <li><a href="#onepiece"><?php echo $translations['Onepiece'] ?></a></li>
            </ul>
            <div>
                <div id="tops">
                    <h2><a href="../tops.php"><?php echo $translations['Tops'] ?></a></h2>
                    <?php
                    $stmt = $dbh->prepare("SELECT * FROM subcategory WHERE category_id = 1");
                    $stmt->execute();


                    $categories = $stmt->fetchAll();

                    // データをフェッチして表示
                    if ($categories) {
                        echo "<ul>";  // リスト形式で表示
                        foreach ($categories as $category) {
                            echo "<li>";

                            // サブカテゴリ名に応じて遷移先を決定
                            $subcategorycode = strtolower($category['subcategory_code']);
                            $subcategoryName = $category['subcategory_name'];

                            // サブカテゴリ名を翻訳
                            $translatedSubcategoryName = isset($translations[$subcategoryName]) ? $translations[$subcategoryName] : $subcategoryName;
                            $url = '../tops/' . $subcategorycode . '.php';

                            echo "<a href='{$url}'>" . htmlspecialchars($translatedSubcategoryName) . "</a><br>";
                            echo "</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "該当するカテゴリーはありません。";
                    }
                    ?>
                </div>
                <div id="jacket">
                    <h2><a href="../jacket-outerwear.php"><?php echo $translations['Jacket'] ?></a></h2>

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
                            $subcategoryName = ($category['subcategory_name']);
                            $subcategorycode = strtolower($category['subcategory_code']);

                            $translatedSubcategoryName = isset($translations[$subcategoryName]) ? $translations[$subcategoryName] : $subcategoryName;

                            $url = '../jacket-outerwear/' . $subcategorycode . '.php';

                            echo "<a href='{$url}'>" . htmlspecialchars($translatedSubcategoryName) . "</a><br>";
                            echo "</li>";

                            // echo "<a href='{$url}'> " . htmlspecialchars($category['subcategory_name']) . "</a><br>";
                            // echo "</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "該当するカテゴリーはありません。";
                    }
                    ?>
                </div>
                <div id="pants">
                    <h2><a href="../pants.php"><?php echo $translations['Pants'] ?></a></h2>

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
                            $subcategoryName = ($category['subcategory_name']);
                            $subcategorycode = strtolower($category['subcategory_code']);

                            $translatedSubcategoryName = isset($translations[$subcategoryName]) ? $translations[$subcategoryName] : $subcategoryName;

                            $url = '../pants/' . $subcategorycode . '.php';

                            echo "<a href='{$url}'>" . htmlspecialchars($translatedSubcategoryName) . "</a><br>";
                            echo "</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "該当するカテゴリーはありません。";
                    }
                    ?>
                </div>
                <div id="skirt">
                    <h2><a href="../skirt.php"><?php echo $translations['Skirt'] ?></a></h2>

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
                            $subcategoryName = ($category['subcategory_name']);
                            $subcategorycode = strtolower($category['subcategory_code']);

                            $translatedSubcategoryName = isset($translations[$subcategoryName]) ? $translations[$subcategoryName] : $subcategoryName;

                            $url = '../skirt/' . $subcategorycode . '.php';

                            echo "<a href='{$url}'>" . htmlspecialchars($translatedSubcategoryName) . "</a><br>";
                            echo "</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "該当するカテゴリーはありません。";
                    }
                    ?>
                </div>
                <div id="onepiece">
                    <h2><a href="../onepiece.php"><?php echo $translations['Onepiece'] ?></a></h2>

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
                            $subcategoryName = ($category['subcategory_name']);
                            $subcategorycode = strtolower($category['subcategory_code']);

                            $translatedSubcategoryName = isset($translations[$subcategoryName]) ? $translations[$subcategoryName] : $subcategoryName;

                            $url = '../onepiece/' . $subcategorycode . '.php';

                            echo "<a href='{$url}'>" . htmlspecialchars($translatedSubcategoryName) . "</a><br>";
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