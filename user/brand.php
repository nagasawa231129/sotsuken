<?php
include "../../db_open.php";
include "../header.php";
include "../head.php";
echo "<link rel='stylesheet' href='header.css'>";
echo "<link rel='stylesheet' href='brand.css'>";

// アルファベットで絞り込みがなければ全ブランドを取得
$sql = "SELECT * FROM brand ORDER BY brand_name ASC";
$result = $dbh->prepare($sql);
$result->execute();
$brands = $result->fetchAll(PDO::FETCH_ASSOC);

// ブランドをアルファベット順にグループ化
$brand_groups = [];
foreach ($brands as $brand) {
    $first_letter = strtoupper(substr($brand['brand_name'], 0, 1)); // ブランド名の最初の文字を取得
    if (!isset($brand_groups[$first_letter])) {
        $brand_groups[$first_letter] = [];
    }
    $brand_groups[$first_letter][] = $brand;
}

$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ja';

// // 言語ファイルのパスを設定
$lang_file = __DIR__ . "/{$lang}.php";

// // 言語ファイルを読み込み
if (file_exists($lang_file)) {
    include($lang_file);
} else {
    die("Error: Language file not found.");
}
?>

<!DOCTYPE html>
<html lang="ja">
    <link rel="stylesheet" href="brand.css">
<body>
    <div class="container">
        <header>
            <h1 data-i18n="brand_search_title"><?php echo $translations['Search By Brand'] ?></h1>
        </header>

        <div class="filters">
            <div class="filter-alphabet">
                <h3 data-i18n="filter_by_alphabet"><?php echo $translations['Filter By Alphabet'] ?></h3>
                <div class="alphabet-buttons">
                    <?php foreach (range('A', 'Z') as $alphabet): ?>
                        <a href="#<?php echo $alphabet; ?>" data-i18n="alphabet_<?php echo $alphabet; ?>"><?php echo $alphabet; ?></a>
                    <?php endforeach; ?>
                    <a href="#ALL" data-i18n="all">ALL</a>
                </div>
            </div>
        </div>

        <!-- ブランドリスト -->
        <div class="brand-list">
            <?php foreach (range('A', 'Z') as $letter): ?>
                <?php if (isset($brand_groups[$letter])): ?>
                    <div id="<?php echo $letter; ?>" class="brand-group">
                        <h3><?php echo $letter; ?></h3>
                        <ul>
                            <?php foreach ($brand_groups[$letter] as $brand): ?>
                                <?php
                                // ブランドの詳細ページへのリンクURLを作成
                                $brand_url = "brand_detail.php?brand=" . $brand['brand_id'];
                                ?>
                                <li class="brand-item">
                                    <a href="<?php echo $brand_url; ?>" class="brand-link"><?php echo $brand['brand_name']; ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

    </div>

    <script src="brand.js"></script>
</body>

</html>
