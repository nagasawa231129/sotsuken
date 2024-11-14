// アルファベットボタンがクリックされた時の処理
document.querySelectorAll('.alphabet-buttons button').forEach(button => {
    button.addEventListener('click', function() {
        const letter = this.textContent.trim();  // クリックされたボタンのアルファベット
        const targetElement = document.getElementById(letter);  // 対応するアルファベットのブランドグループ

        if (targetElement) {
            window.scrollTo({
                top: targetElement.offsetTop - 20,  // 少し上に余白を取る
                behavior: 'smooth'  // スムーズスクロール
            });
        }
    });
});