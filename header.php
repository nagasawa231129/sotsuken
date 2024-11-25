<?php
session_start();
$user_name = isset($_SESSION['login']) ? $_SESSION['name'] : 'ゲスト';
$supported_languages = ['ja', 'en'];
$lang = isset($_SESSION['lang']) && in_array($_SESSION['lang'], $supported_languages) 
    ? $_SESSION['lang'] 
    : 'ja';

if (file_exists("{$lang}.php")) {
    include("{$lang}.php");
} else {
    die("Error: Language file not found.");
}

?>
<script src="https://cdn.i18next.com/i18next.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/i18next/21.6.0/i18next.min.js"></script>

<!-- 言語切り替えボタン -->
<!-- <button id="btn-ja">日本語</button>
<button id="btn-en">English</button>

<script src="lunguage.js"></script> -->

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
        <a href="/sotsuken/sotsuken/user/login.php" data-i18n="login"><?php echo $translations['login']?></a>
        <a href="/sotsuken/sotsuken/user/notification.php" data-i18n="🔔"><?php echo $translations['🔔']?></a>
        <a href="/sotsuken/sotsuken/user/cart.php" data-i18n="cart"><?php echo $translations['cart']?></a>
        <a href="/sotsuken/sotsuken/user/favorite.php" data-i18n="♡"><?php echo $translations['♡']?></a>
        <div class="user-menu">
            <span class="user-name"><?php echo htmlspecialchars($user_name); ?></span>
            <div class="dropdown-menu">
                <a href="/sotsuken/sotsuken/user/settings.php" data-i18n="info"><?php echo $translations['info']?></a>
                <a href="/sotsuken/sotsuken/user/order.php" data-i18n="order"><?php echo $translations['order']?></a>
                <a href="/sotsuken/sotsuken/user/logout.php" data-i18n="logout"><?php echo $translations['logout']?></a>
            </div>
        </div>
    </div>
</header>
<!-- <script>
    i18next.init({
        lng: 'ja', // 初期言語
        resources: {
            ja: {
                translation: {
                    translation: {
                        "search_placeholder": "すべてのアイテムから探す",
                        "guest": "ゲスト",
                        "user_name": "{{name}}さん"
                    },
                    "brand_search_title": "ブランド検索",
                    "filter_by_alphabet": "アルファベットで絞り込む",
                    "all": "すべて",
                    "alphabet_A": "A",
                    "alphabet_B": "B",
                    "alphabet_C": "C",
                    "alphabet_D": "D",
                    "alphabet_E": "E",
                    "alphabet_F": "F",
                    "alphabet_G": "G",
                    "alphabet_H": "H",
                    "alphabet_I": "I",
                    "alphabet_J": "J",
                    "alphabet_K": "K",
                    "alphabet_L": "L",
                    "alphabet_M": "M",
                    "alphabet_N": "N",
                    "alphabet_O": "O",
                    "alphabet_P": "P",
                    "alphabet_Q": "Q",
                    "alphabet_R": "R",
                    "alphabet_S": "S",
                    "alphabet_T": "T",
                    "alphabet_U": "U",
                    "alphabet_V": "V",
                    "alphabet_W": "W",
                    "alphabet_X": "X",
                    "alphabet_Y": "Y",
                    "alphabet_Z": "Z",

                    "keyword_label": "キーワード",
                    "keyword_placeholder": "キーワードを入力",
                    "gender_label": "性別",
                    "men": "男性",
                    "woman": "女性",
                    "category_label": "カテゴリー",
                    "all": "すべて",
                    "outerwear": "ジャケット/アウター",
                    "pants": "パンツ",
                    "skirt": "スカート",
                    "onepiece": "ワンピース",
                    "subcategory_label": "サブカテゴリー",
                    "price_range": "価格帯",
                    "min_price_placeholder": "最小価格",
                    "max_price_placeholder": "最大価格",
                    "sale_label": "セール対象",
                    "sale": "セール対象",
                    "no_sale": "セールなし",
                    "search_button": "検索",

                    "cart": "カート",
                    "🔔": "🔔",
                    "login": "ログイン",
                    "♡": "♡",
                    "info": "登録情報",
                    "order": "発注履歴・発送状況",
                    "logout": "ログアウト",

                    "search": "探す",
                    "search_by_brand": "ブランドで探す",
                    "search_by_category": "カテゴリ―で探す",
                    "search_by_ranking": "ランキングで探す",
                    "search_by_sale": "セール対象で探す",
                    "search_by_diagnosis": "診断から探す",
                    "advanced_search": "詳細検索",
                    "categories_from": "カテゴリーから探す",
                    "tops": "トップス",
                    "brand": "ブランド",
                    "product_name": "商品名",
                    "price": "値段",
                    "discounted_price": "割引後価格",
                    "description": "商品説明",
                    "previous": "前へ",
                    "next": "次へ"
                }
            },
            en: {
                translation: {
                    translation: {
                        "search_placeholder": "Search all items",
                        "guest": "Guest",
                        "user_name": "{{name}}"
                    },
                    "brand_search_title": "Brand Search",
                    "filter_by_alphabet": "Filter by Alphabet",
                    "all": "All",
                    "alphabet_A": "A",
                    "alphabet_B": "B",
                    "alphabet_C": "C",
                    "alphabet_D": "D",
                    "alphabet_E": "E",
                    "alphabet_F": "F",
                    "alphabet_G": "G",
                    "alphabet_H": "H",
                    "alphabet_I": "I",
                    "alphabet_J": "J",
                    "alphabet_K": "K",
                    "alphabet_L": "L",
                    "alphabet_M": "M",
                    "alphabet_N": "N",
                    "alphabet_O": "O",
                    "alphabet_P": "P",
                    "alphabet_Q": "Q",
                    "alphabet_R": "R",
                    "alphabet_S": "S",
                    "alphabet_T": "T",
                    "alphabet_U": "U",
                    "alphabet_V": "V",
                    "alphabet_W": "W",
                    "alphabet_X": "X",
                    "alphabet_Y": "Y",
                    "alphabet_Z": "Z",

                    "keyword_label": "Keyword",
                    "keyword_placeholder": "Enter keyword",
                    "gender_label": "Gender",
                    "male": "Men",
                    "female": "Woman",
                    "category_label": "Category",
                    "all": "All",
                    "outerwear": "Jackets/Outerwear",
                    "pants": "Pants",
                    "skirt": "Skirt",
                    "onepiece": "Onepiece",
                    "subcategory_label": "Subcategory",
                    "price_range": "Price range",
                    "min_price_placeholder": "Minimum price",
                    "max_price_placeholder": "Maximum price",
                    "sale_label": "Sale items",
                    "sale": "On sale",
                    "no_sale": "No sale",
                    "search_button": "Search",

                    "cart": "Cart",
                    "🔔": "🔔",
                    "login": "Login",
                    "♡": "♡",
                    "info": "User Info",
                    "order": "Order",
                    "logout": "Logout",

                    "search": "Search",
                    "search_by_brand": "Search by Brand",
                    "search_by_category": "Search by Category",
                    "search_by_ranking": "Search by Ranking",
                    "search_by_sale": "Search by Sale",
                    "search_by_diagnosis": "Search by Diagnosis",
                    "advanced_search": "Advanced Search",
                    "categories_from": "Browse by Category",
                    "tops": "Tops",
                    "brand": "Brand",
                    "product_name": "Product Name",
                    "price": "Price",
                    "discounted_price": "Discounted Price",
                    "description": "Description",
                    "previous": "Previous",
                    "next": "Next"
                }
            }
        }
    });

    document.addEventListener("DOMContentLoaded", function() {
        // ページが読み込まれたときにplaceholderをi18nextで翻訳
        document.getElementById('search-input').placeholder = i18next.t('すべてのアイテムから探す');
    });

    // 言語切り替え時にplaceholderを更新
    document.getElementById('btn-ja').addEventListener('click', function() {
        i18next.changeLanguage('ja', () => {
            document.getElementById('search-input').placeholder = i18next.t('すべてのアイテムから探す');
        });
    });

    document.getElementById('btn-en').addEventListener('click', function() {
        i18next.changeLanguage('en', () => {
            document.getElementById('search-input').placeholder = i18next.t('Search all items');
        });
    });


    document.getElementById('btn-ja').addEventListener('click', function() {
        i18next.changeLanguage('ja', () => updateContent());
    });

    document.getElementById('btn-en').addEventListener('click', function() {
        i18next.changeLanguage('en', () => updateContent());
    });

    function updateContent() {
    document.querySelectorAll('[data-i18n]').forEach(function(element) {
        // 名前を動的に埋め込む
        let translatedText = i18next.t(element.getAttribute('data-i18n'), { name: document.querySelector('.user-name').innerText });
        element.innerHTML = translatedText;
    });
    }

    // コンテンツを更新する関数
    function updateContent() {
        document.querySelectorAll('[data-i18n]').forEach(function(element) {
            element.innerHTML = i18next.t(element.getAttribute('data-i18n'));
        });
    }
</script> -->