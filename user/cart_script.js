
// 増加ボタンをクリックした時の処理
// 増加ボタンをクリックした時の処理
function increaseQuantity(shopId, currentQuantity, price) {
var newQuantity = currentQuantity + 1;

//ボタンを無効化
var increaseButton = document.getElementById('increaseBtn_' + shopId);
var decreaseButton = document.getElementById('decreaseBtn_' + shopId);
increaseButton.disabled = true;
decreaseButton.disabled = true;

// 即時に数量を更新
document.getElementById('quantity_' + shopId).innerText = newQuantity;

// 商品ごとの合計金額を即座に更新
updateTotalAmount(shopId, price, newQuantity);

// サーバーに数量更新リクエストを送信
updateQuantity(shopId, newQuantity, function() {
// AJAXが成功した場合にボタンを再度有効化
increaseButton.disabled = false;
decreaseButton.disabled = false;
});
}


function updateQuantityHandler(button) {
var shopId = button.getAttribute('data-shop-id');
var currentQuantity = parseInt(button.getAttribute('data-quantity'));
var price = parseInt(button.getAttribute('data-price'));

var newQuantity;
if (button.classList.contains('increase-btn')) {
newQuantity = currentQuantity + 1; // 数量を1増加
} else if (button.classList.contains('decrease-btn')) {
if (currentQuantity > 1) { // 最低数量を1に制限
newQuantity = currentQuantity - 1; // 数量を1減少
} else {
return; // 数量が1の場合は減らさない
}
}

// 数量を即座に更新
document.getElementById('quantity_' + shopId).innerText = newQuantity;
// 合計金額を即座に更新
updateTotalAmount(shopId, price, newQuantity);



button.disabled = true;

// サーバーに数量更新リクエストを送信
updateQuantity(shopId, newQuantity, function() {
// 更新が成功したらボタンを再度有効化
button.disabled = false;

// 数量をボタンに反映
button.setAttribute('data-quantity', newQuantity);
});
}

// AJAXで個数の増減を処理する関数
function updateQuantity(shopId, newQuantity, callback) {
var xhr = new XMLHttpRequest();
xhr.open('POST', 'update_quantity.php', true);
xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
xhr.onreadystatechange = function() {
if (xhr.readyState == 4 && xhr.status == 200) {
// サーバーからの応答を受け取った後の処理
if (xhr.responseText !== 'success') {
    alert('個数の更新に失敗しました。');
    // 更新失敗時には、数量をリセットする
    location.reload();  // リロードして最新のデータを再取得
}
// 成功したらコールバックを実行
if (callback) callback();
}
};
xhr.send('shop_id=' + shopId + '&quantity=' + newQuantity);
}

// 合計金額を更新する関数
function updateTotalAmount(shopId, price, quantity) {
// 商品ごとの合計金額を即座に更新
var totalAmountElement = document.getElementById('totalAmount_' + shopId);
if (totalAmountElement) {
totalAmountElement.innerText = (price * quantity) + "円";
}

// 全体金額の更新
var totalSum = 0;
var totalElements = document.querySelectorAll('[id^="totalAmount_"]');
totalElements.forEach(function(element) {
var amount = parseInt(element.innerText.replace('円', '').trim());
if (!isNaN(amount)) {
totalSum += amount;
}
});

// 合計金額を更新
var totalSumElement = document.getElementById('totalSum');
if (totalSumElement) {
totalSumElement.innerText = totalSum + "円";
}
}



// 減少ボタンをクリックした時の処理
function decreaseQuantity(shopId, currentQuantity, price) {
if (currentQuantity > 1) { // 個数が1以下にはならないように制限
var newQuantity = currentQuantity - 1;

    // ボタンを無効化
    var increaseButton = document.getElementById('increaseBtn_' + shopId);
var decreaseButton = document.getElementById('decreaseBtn_' + shopId);
increaseButton.disabled = true;
decreaseButton.disabled = true;

// 即時に数量を更新
document.getElementById('quantity_' + shopId).innerText = newQuantity;

// 商品ごとの合計金額を即座に更新
updateTotalAmount(shopId, price, newQuantity);

// サーバーに数量更新リクエストを送信
updateQuantity(shopId, newQuantity, function() {
// AJAXが成功した場合にボタンを再度有効化
increaseButton.disabled = false;
decreaseButton.disabled = false;
});
}
}

// 合計金額の即時更新
function updateTotalAmount(shopId, price, quantity) {
// 商品ごとの合計金額を即座に更新
var totalAmountElement = document.getElementById('totalAmount_' + shopId);
if (totalAmountElement) {
totalAmountElement.innerText = (price * quantity) + "円";
}

// 全体金額の更新
var totalSum = 0;
// 各商品ごとの合計金額の要素を取得して合計する
var totalElements = document.querySelectorAll('[id^="totalAmount_"]');
totalElements.forEach(function(element) {
var amount = parseInt(element.innerText.replace('円', '').trim());
if (!isNaN(amount)) {
totalSum += amount;
}
});

// 合計金額を更新
var totalSumElement = document.getElementById('totalSum');
if (totalSumElement) {
totalSumElement.innerText = totalSum + "円";
}
}

// AJAXで個数の増減を処理する関数
function updateQuantity(shopId, newQuantity,callback) {
var xhr = new XMLHttpRequest();
xhr.open('POST', 'update_quantity.php', true);
xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
xhr.onreadystatechange = function() {
if (xhr.readyState == 4 && xhr.status == 200) {
// サーバーからの応答を受け取った後の処理
if (xhr.responseText !== 'success') {
    alert('個数の更新に失敗しました。');
    // 更新失敗時には、数量をリセットする
    location.reload();  // リロードして最新のデータを再取得
}
if (callback) callback();
}
};
xhr.send('shop_id=' + shopId + '&quantity=' + newQuantity);
}
var totalSum = 0;
var totalElements
