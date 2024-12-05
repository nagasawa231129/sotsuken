<?php
session_start();
if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];
} else {
    $userId = null;
}
$user_name = isset($_SESSION['login']) ? $_SESSION['display_name'] : 'ゲスト';
$supported_languages = ['ja', 'en'];
$lang = isset($_SESSION['lang']) && in_array($_SESSION['lang'], $supported_languages)
    ? $_SESSION['lang']
    : 'ja';

// 言語ファイルの絶対パスを指定
$languageFile = $_SERVER['DOCUMENT_ROOT'] . '/sotsuken/sotsuken/user/' . $lang . '.php';

// 言語ファイルが存在するか確認
if (file_exists($languageFile)) {
    require_once $languageFile; // 言語ファイルを読み込む
} else {
    die("Error: Language file not found.");
}

$notification_count_stmt = $dbh->prepare("SELECT COUNT(*) FROM notification WHERE user_id = :user_id AND read_status = 0");
$notification_count_stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
$notification_count_stmt->execute();
$unread_count = $notification_count_stmt->fetchColumn();

?>

<script src="https://cdn.i18next.com/i18next.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/i18next/21.6.0/i18next.min.js"></script>

<header>
    <link rel="stylesheet" href="./user/header.css">
    <script>
        // カタカナと英語を統一するための関数
        function normalizeQuery(query) {
            // ひらがなをカタカナに変換
            query = query.replace(/[\u3041-\u3096]/g, function(match) {
                return String.fromCharCode(match.charCodeAt(0) + 0x60);
            });

            // 英語を日本語のカテゴリに変換するマッピング
            const englishToJapanese = {
                // トップス
                'tops': 'トップス',
                't-shirt': 'tシャツ',
                'cutsew': 'カットソー',
                'shirt': 'シャツ',
                'blouse': 'ブラウス',
                'polo-shirt': 'ポロシャツ',
                'knit': 'ニット',
                'sweater': 'セーター',
                'vest': 'ベスト',
                'parka': 'パーカー',
                'sweat': 'スウェット',
                'cardigan': 'カーディガン',
                'bolero': 'ボレロ',
                'ensemble': 'アンサンブル',
                'jersey': 'ジャージ',
                'tanktop': 'タンクトップ',
                'camisole': 'キャミソール',
                'tubetop': 'チューブトップス',
                'other-tops': 'その他トップス',

                // ジャケット/アウター
                'jacket': 'ジャケット',
                'outerwear': 'アウター',
                'tailored-jacket': 'テーラードジャケット',
                'collarless-jacket': 'ノーカラージャケット',
                'collarless-coat': 'ノーカラーコート',
                'denim-jacket': 'デニムジャケット',
                'riders-jacket': 'ライダースジャケット',
                'blouson': 'ブルゾン',
                'military-jacket': 'ミリタリージャケット',
                'down-jacket': 'ダウンジャケット',
                'coat': 'コート',
                'down-vest': 'ダウンベスト',
                'duffle-coat': 'ダッフルコート',
                'trench-coat': 'トレンチコート',
                'nylon-jacket': 'ナイロンジャケット',
                'mods-coat': 'モッズコート',
                'other-outerwear': 'その他アウター',

                // パンツ
                'pants': 'パンツ',
                'denim-pants': 'デニムパンツ',
                'cargo-pants': 'カーゴパンツ',
                'chino-pants': 'チノパンツ',
                'sweat-pants': 'スウェットパンツ',
                'slacks': 'スラックス',
                'other-pants': 'その他パンツ',

                // スカート
                'skirt': 'スカート',
                'denim-skirt': 'デニムスカート',

                // ワンピース
                'onepiece': 'ワンピース',
                'shirt-onepiece': 'シャツワンピース',
                'jumper-skirt': 'ジャンパースカート',
                'tunic': 'チュニック',
                'dress': 'ドレス',
                'pants-dress': 'パンツドレス'
            };

            // 英語を日本語に変換
            if (englishToJapanese[query.toLowerCase()]) {
                query = englishToJapanese[query.toLowerCase()];
            }

            return query;
        }

        // カテゴリページと商品検索ページの遷移を一括で管理
        function performSearch() {
            let query = document.getElementById('search-input').value.trim().toLowerCase();

            // クエリの変換処理（ひらがな→カタカナ + 英語→日本語）
            query = normalizeQuery(query);

            let redirectUrl = '';

            // カテゴリ名と対応するURLを定義
            const categoryMapping = {
                'トップス': 'category/tops.php',
                'tシャツ': 'category/tops/tshirt-cutsew.php',
                'カットソー': 'category/tops/tshirt-cutsew.php',
                'シャツ': 'category/tops/shirt.php',
                'ブラウス': 'category/tops/shirt.php',
                'ポロシャツ': 'category/tops/polo-shirt.php',
                'ニット': 'category/tops/knit-sweater.php',
                'セーター': 'category/tops/knit-sweater.php',
                'ベスト': 'category/tops/vest.php',
                'パーカー': 'category/tops/parka.php',
                'スウェット': 'category/tops/sweat.php',
                'カーディガン': 'category/tops/cardigan.php',
                'ボレロ': 'category/tops/cardigan.php',
                'アンサンブル': 'category/tops/ensemble.php',
                'ジャージ': 'category/tops/jersey.php',
                'タンクトップ': 'category/tops/tanktop.php',
                'キャミソール': 'category/tops/camisole.php',
                'チューブトップス': 'category/tops/tubetops.php',
                'その他トップス': 'category/tops/auter-tops.php',

                // ジャケット/アウター
                'ジャケット': 'category/jacket-outerwear.php',
                'アウター': 'category/jacket-outerwear.php',
                'テーラードジャケット': 'category/jacket-outerwear/tailored-jacket.php',
                'ノーカラージャケット': 'category/jacket-outerwear/collarless-jacket.php',
                'ノーカラーコート': 'category/jacket-outerwear/collarless-coat.php',
                'デニムジャケット': 'category/jacket-outerwear/denim-jacket.php',
                'ライダースジャケット': 'category/jacket-outerwear/riders-jacket.php',
                'ブルゾン': 'category/jacket-outerwear/jacket.php',
                'ミリタリージャケット': 'category/jacket-outerwear/military-jacket.php',
                'ダウンジャケット': 'category/jacket-outerwear/down-jacket.php',
                'コート': 'category/jacket-outerwear/down-jacket.php',
                'ダウンベスト': 'category/jacket-outerwear/down-vest.php',
                'ダッフルコート': 'category/jacket-outerwear/duffle-coat.php',
                'トレンチコート': 'category/jacket-outerwear/trench-coat.php',
                'ナイロンジャケット': 'category/jacket-outerwear/nylon-jacket.php',
                'モッズコート': 'category/jacket-outerwear/mods-coat.php',
                'その他アウター': 'category/jacket-outerwear/auter-jacket.php',

                // パンツ
                'パンツ': 'category/pants.php',
                'デニムパンツ': 'category/pants/denim-pants.php',
                'カーゴパンツ': 'category/pants/cargo-pants.php',
                'チノパンツ': 'category/pants/chino-pants.php',
                'スウェットパンツ': 'category/pants/sweat-pants.php',
                'スラックス': 'category/pants/slacks.php',
                'その他パンツ': 'category/pants/auter-pants.php',

                // スカート
                'スカート': 'category/skirt.php',
                'デニムスカート': 'category/skirt/denim-skirt.php',

                // ワンピース
                'ワンピース': 'category/onepiece/onepiece-dress.php',
                'シャツワンピース': 'category/onepiece/shirts-onepiece.php',
                'ジャンパースカート': 'category/onepiece/jumper-skirt.php',
                'チュニック': 'category/onepiece/tunic.php',
                'ドレス': 'category/onepiece/dress.php',
                'パンツドレス': 'category/onepiece/pants-dress.php'
            };

            // 完全一致するカテゴリがあるかをチェック
            if (categoryMapping[query]) {
                redirectUrl = categoryMapping[query];
            } else {
                // 一致しない場合は商品検索ページに遷移
                redirectUrl = 'search.php?query=' + encodeURIComponent(query);
            }

            // 遷移先にリダイレクト
            window.location.href = redirectUrl;
        }
    </script>

    <div class="search-container">
        <!-- 「卒研TOWN」を左側に移動 -->
        <div class="search-bar">
            <a class="site-name" href="/sotsuken/sotsuken/user/toppage.php">卒研TOWN</a>
            <input type="text" id="search-input" data-i18n="search_placeholder" placeholder="<?php echo $translations['search_placeholder'] ?? 'すべてのアイテムから探す'; ?>" onkeydown="if(event.key === 'Enter') performSearch()">
        </div>
    </div>
    <div class="icon">
        <a href="/sotsuken/sotsuken/user/login.php" data-i18n="login"><?php echo $translations['login'] ?></a>
        <a href="/sotsuken/sotsuken/user/notification.php" data-i18n="🔔">
            <?php echo $translations['🔔']; ?>
            <?php if ($unread_count > 0): ?>
                <span class="notification-count"><?php echo $unread_count; ?></span>
            <?php endif; ?>

        </a>
        <a href="/sotsuken/sotsuken/user/cart.php" data-i18n="cart"><?php echo $translations['cart'] ?></a>
        <a href="/sotsuken/sotsuken/user/favorite.php" data-i18n="♡"><?php echo $translations['♡'] ?></a>
        <div class="user-menu">
            <span class="user-name"><?php echo htmlspecialchars($user_name); ?></span>
            <div class="dropdown-menu">
                <a href="/sotsuken/sotsuken/user/account.php" data-i18n="info"><?php echo $translations['info'] ?></a>
                <a href="/sotsuken/sotsuken/user/order.php" data-i18n="order"><?php echo $translations['order'] ?></a>
                <a href="/sotsuken/sotsuken/user/logout.php" data-i18n="logout"><?php echo $translations['logout'] ?></a>
            </div>
        </div>
    </div>
</header>