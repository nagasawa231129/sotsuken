<?php
include "../../db_open.php";
include "../head.php";
include "../header.php";
echo "<link rel='stylesheet' href='header.css'>";
echo "<link rel='stylesheet' href='./category.css'>";
?>

<form action="advanced_search.php" method="get">
    <!-- キーワード -->
    <label for="keyword">キーワード</label>
    <input type="text" name="keyword" placeholder="キーワード" value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>" />

    <!--性別 -->
    <label for="gender">性別：</label>
    <select name="gender">
        <option value="">すべて</option>
        <option value="1" <?php echo (isset($_GET['gender']) && $_GET['gender'] === '1') ? 'selected' : ''; ?>>男性</option>
        <option value="0" <?php echo (isset($_GET['gender']) && $_GET['gender'] === '0') ? 'selected' : ''; ?>>女性</option>
    </select>

    <!--カテゴリー -->
    <label for="categoryr">カテゴリー：</label>
    <select name="category" id="category" onchange="updateSubcategories()">
        <option value="">すべて</option>
        <option value="1" <?php echo (isset($_GET['category']) && $_GET['category'] === '1') ? 'selected' : ''; ?>>トップス</option>
        <option value="2" <?php echo (isset($_GET['category']) && $_GET['category'] === '2') ? 'selected' : ''; ?>>ジャケット/アウター</option>
        <option value="3" <?php echo (isset($_GET['category']) && $_GET['category'] === '3') ? 'selected' : ''; ?>>パンツ</option>
        <option value="4" <?php echo (isset($_GET['category']) && $_GET['category'] === '4') ? 'selected' : ''; ?>>スカート</option>
        <option value="5" <?php echo (isset($_GET['category']) && $_GET['category'] === '5') ? 'selected' : ''; ?>>ワンピース</option>
    </select>

        <!-- サブカテゴリー -->
        <div id="subcategory-container" style="display: none;">
        <label for="subcategory">サブカテゴリー：</label>
        <select name="subcategory" id="subcategory">
            <!-- サブカテゴリーが動的に追加されます -->
        </select>
    </div>

    <!-- 価格帯 -->
    <label for="min_price">価格帯：</label>
    <input type="number" name="min_price" placeholder="最小価格" value="<?php echo isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : ''; ?>" />
    ～
    <input type="number" name="max_price" placeholder="最大価格" value="<?php echo isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : ''; ?>" />

    <!-- セール対象商品 -->
    <label for="sale_subject">セール対象：</label>
    <select name="sale_subject">
        <option value="">すべて</option>
        <option value="1" <?php echo (isset($_GET['sale_subject']) && $_GET['sale_subject'] === '1') ? 'selected' : ''; ?>>セール対象</option>
        <option value="0" <?php echo (isset($_GET['sale_subject']) && $_GET['sale_subject'] === '0') ? 'selected' : ''; ?>>セールなし</option>
    </select>

    <input type="submit" value="検索">
</form>


<script>
// カテゴリーごとのサブカテゴリー
const subcategories = {
    1: [
        { value: '1', text: 'Tシャツ/カットソー' },   // t-shirt, cutsew
        { value: '2', text: 'シャツ/ブラウス' },     // shirt, blouse
        { value: '3', text: 'ポロシャツ' },         // polo-shirt
        { value: '4', text: 'ニット/セーター' },    // knit, sweater
        { value: '5', text: 'ベスト' },            // vest
        { value: '6', text: 'パーカー' }, // parka, sweat
        { value: '7', text: 'スウェット' }, // sweat
        { value: '8', text: 'カーディガン/ボレロ' }, // cardigan, bolero
        { value: '9', text: 'アンサンブル' }, // ensemble
        { value: '10', text: 'ジャージ' }, // jersey
        { value: '11', text: 'タンクトップ' }, // tanktop
        { value: '12', text: 'キャミソール' }, // camisole
        { value: '13', text: 'チューブトップス' },   // tubetop
        { value: '14', text: 'その他トップス' },    // other-tops
    ],
    2: [
        { value: '15', text: 'ノーカラーコート' },    // collarless coat
        { value: '16', text: 'ノーカラージャケット' },// collarless jacket
        { value: '17', text: 'デニムジャケット' },    // denim jacket
        { value: '18', text: 'ダウンジャケット' },    // down jacket
        { value: '19', text: 'ダウンベスト' },        // down vest
        { value: '20', text: 'ダッフルコート' },      // duffle coat
        { value: '21', text: 'ブルゾン' },            // blouson
        { value: '22', text: 'ミリタリージャケット' },// military jacket
        { value: '23', text: 'モッズコート' },       // mods coat
        { value: '24', text: 'ナイロンジャケット' }, // nylon jacket
        { value: '25', text: 'ライダースジャケット' },// riders jacket
        { value: '26', text: 'テーラードジャケット' }, // tailored jacket
        { value: '27', text: 'トレンチコート' },      // trench coat
        { value: '28', text: 'その他アウター' },      // other outerwear
    ],
    3: [
        { value: '29', text: 'カーゴパンツ' },    // cargo pants
        { value: '30', text: 'チノパン' },        // chino pants
        { value: '31', text: 'デニムパンツ' },    // denim pants
        { value: '32', text: 'スラックス' },      // slacks
        { value: '33', text: 'スウェットパンツ' }, // sweat pants
        { value: '34', text: 'その他パンツ' },    // other pants
    ],
    4: [
        { value: '35', text: 'デニムスカート' },  // denim skirt
        { value: '36', text: 'ミニスカート' },    // mini skirt
        { value: '37', text: 'ミディスカート' },  // midi skirt
        { value: '38', text: 'ロングスカート' },  // long skirt
    ],
    5: [
        { value: '39', text: 'ドレス' },         //dress
        { value: '40', text: 'ジャンパースカート' },  // jumper skirt
        { value: '41', text: 'ワンピース' },            // onepiece dress
        { value: '42', text: 'パンツドレス' },          // pants dress
        { value: '43', text: 'シャツワンピース' },      // shirt dress
        { value: '44', text: 'チュニック' },            // tunic
    ],
};

// カテゴリー変更時にサブカテゴリーを更新する関数
function updateSubcategories() {
    const categorySelect = document.getElementById('category');
    const subcategoryContainer = document.getElementById('subcategory-container');
    const subcategorySelect = document.getElementById('subcategory');

    // 選択されたカテゴリーの値を取得
    const selectedCategory = categorySelect.value;

    // サブカテゴリーをクリア
    subcategorySelect.innerHTML = '';

    // サブカテゴリーを非表示にする（デフォルト）
    subcategoryContainer.style.display = 'none';

    // 選択されたカテゴリーに対応するサブカテゴリーを表示
    if (subcategories[selectedCategory]) {
        subcategories[selectedCategory].forEach((subcat) => {
            const option = document.createElement('option');
            option.value = subcat.value;
            option.textContent = subcat.text;
            subcategorySelect.appendChild(option);
        });

        // サブカテゴリーを表示
        subcategoryContainer.style.display = 'block';
    }
}
</script>

<?php
// フィルタリング条件を取得
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : null;
$gender = isset($_GET['gender']) && $_GET['gender'] !== '' ? $_GET['gender'] : null;
$subcategory = isset($_GET['subcategory']) && $_GET['subcategory'] !== '' ? $_GET['subcategory'] : null;
$category = isset($_GET['category']) && $_GET['category'] !== '' ? $_GET['category'] : null;
$min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? $_GET['min_price'] : null;
$max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? $_GET['max_price'] : null;
$sale_subject = isset($_GET['sale_subject']) && $_GET['sale_subject'] !== '' ? $_GET['sale_subject'] : null;

// SQLクエリの作成
$sql = "
    SELECT DISTINCT shop.*, subcategory.*, category.*
    FROM shop
    LEFT OUTER JOIN subcategory ON subcategory.subcategory_id = shop.subcategory_id
    LEFT OUTER JOIN category ON category.category_id = shop.category_id
    WHERE 1 = 1
";

$params = [];

// キーワードフィルターの追加
if ($keyword !== null && $keyword !== '') {
    $sql .= " AND goods LIKE ?";
    $params[] = '%' . $keyword . '%';
}

// 性別フィルターの追加
if ($gender !== null) {
    $sql .= " AND gender = ?";
    $params[] = $gender;
}

// カテゴリーフィルターの追加
if ($category !== null) {
    $sql .= " AND category.category_id= ?";
    $params[] = $category;
}

// サブカテゴリーのフィルター追加
if ($subcategory !== null) {
    $sql .= " AND subcategory.subcategory_id = ?";
    $params[] = $subcategory;
}

// 価格帯フィルターの追加
if ($min_price !== null) {
    $sql .= " AND price >= ?";
    $params[] = $min_price;
}
if ($max_price !== null) {
    $sql .= " AND price <= ?";
    $params[] = $max_price;
}

// セール対象商品フィルターの追加
if ($sale_subject !== null) {
    $sql .= " AND sale_subject = ?";
    $params[] = $sale_subject;
}

// プリペアドステートメントでSQLを実行
$stmt = $dbh->prepare($sql);
$stmt->execute($params);


// 検索結果を取得
$results = $stmt->fetchAll();

// 検索結果がない場合の表示
if (empty($results)) {
    echo "該当する商品はありません。";
} else {
    // 検索結果の表示
    foreach ($results as $row) {
        // 商品IDを利用してリンクを生成
        $product_id = htmlspecialchars($row['shop_id']); // 商品IDが `id` カラムに格納されている場合
        $product_link = "goods.php?shop_id=" . $product_id; // 商品詳細ページへのリンク

        echo "<a href=\"$product_link\" style=\"text-decoration: none; color: inherit;\">"; // リンク開始
        echo "商品名: " . htmlspecialchars($row['goods']) . "<br>";
        echo "価格: " . htmlspecialchars($row['price']) . "<br>";

        // 割引率を計算
        $sale_subject = $row['sale_subject'];
        if ($sale_subject >= 1 && $sale_subject <= 9) {
            $discount_percentage = $sale_subject * 10;  // 1なら10%、2なら20%など
            echo "割引率: " . $discount_percentage . "%<br>";
        } else {
            echo "割引率: 0% (セールなし)<br>";
        }
        echo "</a>"; // リンク終了
        echo "<br>";
    }
}

?>