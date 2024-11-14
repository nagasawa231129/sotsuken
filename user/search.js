document.getElementById('search-icon').addEventListener('click', function() {
    var searchBar = document.getElementById('search-bar');
    // 検索バーの表示・非表示を切り替え
    if (searchBar.style.display === 'none') {
        searchBar.style.display = 'flex';
    } else {
        searchBar.style.display = 'none';
    }
});

document.getElementById('search-button').addEventListener('click', function() {
    var query = document.getElementById('search-input').value;
    // 検索処理をここに追加
    console.log("検索クエリ: " + query);
    // 例: 検索結果ページに遷移
    // window.location.href = 'search_results.php?query=' + encodeURIComponent(query);
});
