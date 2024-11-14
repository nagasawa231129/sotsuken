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
    <a class="site-name" href="/sotsuken//sotsuken/user/toppage.php">卒研TOWN</a>
    <input type="text" id="search-input" placeholder="すべてのアイテムから探す" onkeydown="if(event.key === 'Enter') performSearch()">
    </div>
</div>
    <div class="icon">
        <a href="/sotsuken/sotsuken/user/login.php">ログイン</a>
        <a href="/sotsuken/sotsuken/user/notification.php">🔔</a>
        <a href="/sotsuken/sotsuken/user/cart.php">カート</a>
        <a href="/sotsuken/sotsuken/user/favorite.php">♡</a>
    </div>
</header>