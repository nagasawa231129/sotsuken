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
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ブランド検索</title>
    <link rel="stylesheet" href="brand.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>ブランド検索</h1>
        </header>

        <div class="filters">
            <div class="filter-alphabet">
                <h3>アルファベットで絞り込む</h3>
                <div class="alphabet-buttons">
                    <?php foreach (range('A', 'Z') as $alphabet): ?>
                        <a href="#<?php echo $alphabet; ?>"><?php echo $alphabet; ?></a>
                    <?php endforeach; ?>
                    <a href="#ALL">ALL</a>
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
                                $brand_url = "brand_detail.php?brand_id=" . $brand['brand_id'];
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