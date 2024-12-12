// グローバル変数を宣言（スクリプトの冒頭に追加）
let isUpdating = false;

// 増加ボタンをクリックした時の処理
function increaseQuantity(shopId, currentQuantity, price) {
    var newQuantity = currentQuantity + 1;

    // ボタンを無効化
    var increaseButton = document.getElementById('increaseBtn_' + shopId);
    var decreaseButton = document.getElementById('decreaseBtn_' + shopId);
    increaseButton.disabled = true;
    decreaseButton.disabled = true;

    // 即時に数量を更新
    document.getElementById('quantity_' + shopId).innerText = newQuantity;
    document.getElementById('increaseBtn_' + shopId).setAttribute('data-quantity', newQuantity);
    document.getElementById('decreaseBtn_' + shopId).setAttribute('data-quantity', newQuantity);
                                                    
    // 商品ごとの合計金額を即座に更新
    updateTotalAmount(shopId, price, newQuantity);

    // サーバーに数量更新リクエストを送信
    updateQuantity(shopId, newQuantity, function(success) {
        if (success) {
            // サーバーからの成功応答後にボタンを再度有効化
            increaseButton.disabled = false;
            decreaseButton.disabled = false;

            // location.reload();
        } else {
            alert('更新に失敗しました。再度お試しください。');
        }
    });
}


function updateQuantityHandler(button) {
    const shopId = button.getAttribute('data-shop-id');
    const currentQuantity = parseInt(button.getAttribute('data-quantity'));
    const price = parseInt(button.getAttribute('data-price'));

    let newQuantity;
    if (button.classList.contains('increase-btn')) {
        newQuantity = currentQuantity + 1; // 数量を1増加
    } else if (button.classList.contains('decrease-btn')) {
        if (currentQuantity > 1) { // 最低数量を1に制限
            newQuantity = currentQuantity - 1; // 数量を1減少
        } else {
            newQuantity = 0;
            deleteItemFromCart(shopId);
            return;
        }
    }

    // ボタンを無効化して二重送信を防止
    button.disabled = true;

    // サーバーに数量更新リクエストを送信
    updateQuantity(shopId, newQuantity, function
        (success) {
        if (success) {
            // 成功時のみ数量を更新
            document.getElementById('quantity_' + shopId).innerText = newQuantity;
            button.setAttribute('data-quantity', newQuantity); // ボタンのデータを更新

            // 合計金額を更新
            updateTotalAmount(shopId, price, newQuantity);

            // location.reload();
        } else {
            alert('更新に失敗しました。再度お試しください。');
            console.log(`shop_id=${shopId}&quantity=${newQuantity}`); // リクエスト内容を確認

            // const quantityStr = button.getAttribute('data-quantity');
            // console.log('quantityStr:', quantityStr); // ここで値を確認
            // const currentQuantity = parseInt(quantityStr);
            // console.log('currentQuantity:', currentQuantity); // ここでparseIntの結                                                                                                                                                                                                                                              を確認

        }

        // ボタンを再度有効化
        button.disabled = false;
    });
}

function deleteItemFromCart(shopId) {
     const xhr = new XMLHttpRequest(); xhr.open('POST', 'delete_cart.php', true); 
     xhr.setRequestHeader('Content-Type', 'application/json'); 
     xhr.onreadystatechange = function() { 
        if (xhr.readyState == 4) {
             if (xhr.status == 200) {
                 if (xhr.responseText.trim() === 'success') {
                     // カートから商品を削除 
                     document.getElementById('item_' + shopId).remove(); 
                     // 全体金額の更新を行う 
                     updateTotalAmount(shopId, 0, 0);
                     
                     const quentityElement = document.getElementById('quentity_' + shopId);
                     if(quentityElement){
                        quantityElement.innerText = '0';
                     }
                 } else { 
                    alert('削除に失敗しました。再度お試しください。'); 
                } 
            } else {
                 console.error('HTTPエラー:', xhr.status);
                 alert('削除に失敗しました。再度お試しください。');
                 } 
                }
             }; 
    xhr.send(JSON.stringify({ shop_id: shopId })); 
}


function updateQuantity(shopId, newQuantity, callback) {
    if (isUpdating) return; // 更新中のリクエストを防止
    isUpdating = true;

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'update_quantity.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4) {
            isUpdating = false;
            if (xhr.status == 200) {
                if (xhr.responseText.trim().startsWith('success')) {
                    const updatedQuantity = parseInt(xhr.responseText.split(':')[1]);
                    document.getElementById('quantity_' + shopId).innerText = updatedQuantity;

                    // ボタン要素を確認してから更新
                    const increaseButton = document.getElementById('increaseBtn_' + shopId);
                    const decreaseButton = document.getElementById('decreaseBtn_' + shopId);

                    if (increaseButton && decreaseButton) {
                        increaseButton.setAttribute('data-quantity', updatedQuantity);
                        decreaseButton.setAttribute('data-quantity', updatedQuantity);
                    } else {
                        console.error('ボタン要素が見つかりません: shopId=' + shopId);
                    }

                    callback(true);
                } else {
                    callback(false);
                }
            } else {
                console.error('HTTPエラー:', xhr.status);
                callback(false);
            }
        }
    };
    xhr.send(`shop_id=${shopId}&quantity=${newQuantity}`);
}



// 合計金額を更新する関数
function updateTotalAmount(shopId, price, quantity) {
    // 商品ごとの合計金額を即座に更新
    const totalAmountElement = document.getElementById('totalAmount_' + shopId);
    if (totalAmountElement) {
        totalAmountElement.innerText = (price * quantity) + "円";
    }

    // 全体金額の更新
    let totalSum = 0;
    document.querySelectorAll('[id^="totalAmount_"]').forEach(element => {
        const amount = parseInt(element.innerText.replace('円', '').trim());
        if (!isNaN(amount)) {
            totalSum += amount;
        }
    });

    // 合計金額を更新
    const totalSumElement = document.getElementById('totalSum');
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
        document.getElementById('increaseBtn_' + shopId).setAttribute('data-quantity', newQuantity);
        document.getElementById('decreaseBtn_' + shopId).setAttribute('data-quantity', newQuantity);

        // 商品ごとの合計金額を即座に更新
        updateTotalAmount(shopId, price, newQuantity);

        // サーバーに数量更新リクエストを送信
        updateQuantity(shopId, newQuantity, function(success) {
            if (success) {
                // サーバーからの成功応答後にボタンを再度有効化
                increaseButton.disabled = false;
                decreaseButton.disabled = false;

                // location.reload();
            } else {
                alert('更新に失敗しました。再度お試しください。');
            }
        });
    }
}

var totalSum = 0;
var totalElements;


var paymentButton = document.getElementById('submit');
if(paymentButton.style.display === none){
    console.log("カートの中身は空です");
}else{
    paymentButton.style.display = 'block';
}