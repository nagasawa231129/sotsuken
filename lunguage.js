// i18nextの設定
i18next.init({
    lng: 'ja', // 初期言語設定
    resources: {
      ja: {
        translation: {
          "welcome": "ようこそ",
          "cart": "カート"
        }
      },
      en: {
        translation: {
          "welcome": "Welcome",
          "cart": "Cart"
        }
      }
    }
  }, function(err, t) {
    // 初期化後にページ内容を更新
    updateContent();
  });
  
  // 言語切り替え関数
  function changeLanguage(lang) {
    i18next.changeLanguage(lang, function(err, t) {
      updateContent();
    });
  }
  
  // ページ内のテキストを更新する関数
  function updateContent() {
    document.getElementById("welcome").innerText = i18next.t('welcome');
    document.getElementById("cart").innerText = i18next.t('cart');
  }
  
  // 言語切り替えボタンの設定
  document.getElementById("btn-ja").addEventListener("click", function() {
    changeLanguage('ja');
  });
  document.getElementById("btn-en").addEventListener("click", function() {
    changeLanguage('en');
  });
  