<header>
    <link rel="stylesheet" href="user/search.css">
    <script>
        // 検索を実行する関数
        function performSearch() {
            const query = document.getElementById('search-input').value;

            let queryString = 'search.php?';
            if (query) {
                queryString += 'query=' + encodeURIComponent(query);
            }

            // 検索結果ページへ遷移
            window.location.href = queryString;
        }
    </script>

    <div class="search-container">
        <!-- 常時表示される検索フォーム -->
        <div class="search-bar">
            <input type="text" id="search-input" placeholder="検索キーワード">
            <button onclick="performSearch()">検索</button>
        </div>
    </div>

    <!-- その他のリンク -->
    <a href="login.php">ログイン</a>
    <a href="notification.php">🔔</a>
    <a href="cart.php">カート</a>
    <a href="favorite.php">♡</a>
</header>
