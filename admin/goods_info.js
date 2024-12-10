// サムネイル画像を更新する関数
function updateThumbnail(shop_id) {
    var fileInput = document.getElementById('thumbnailInput' + shop_id);
    var file = fileInput.files[0];

    if (file) {
        var formData = new FormData();
        formData.append('thumbnail', file);
        formData.append('shop_id', shop_id); // shop_idをサーバーに送信

        // AJAXでサムネイル画像をサーバーに送信して更新
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'update_thumbnail.php', true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                // 画像更新成功後、新しいサムネイルを表示
                var reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('shopImage' + shop_id).src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        };
        xhr.send(formData);
    }
}
// 画像選択ダイアログを開くための関数
function triggerFileInput(shopId) {
    // 対応するinputファイルをクリック
    document.getElementById('imageInput' + shopId).click();
}

// 画像を更新するための関数
function updateImage(shopId) {
    var fileInput = document.getElementById('imageInput' + shopId);
    var file = fileInput.files[0]; // 選択されたファイル
    var formData = new FormData();

    formData.append('subimage', file); // 画像データをFormDataに追加
    formData.append('shop_id', shopId); // 商品ID
    formData.append('image_id', document.querySelector(`#shopImage${shopId}`).getAttribute('data-image-id')); // 現在の画像IDを追加

    // 画像の更新処理をサーバーに送信
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'update_subimage.php', true);
    
    xhr.onload = function() {
        if (xhr.status === 200) {
            // 画像が正常に更新された場合、サムネイル画像を更新
            var response = xhr.responseText;
            if (response === "サブサムネイルが正常に更新されました。") {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('shopImage' + shopId).src = e.target.result; // 新しい画像を表示
                };
                reader.readAsDataURL(file); // 選択された画像をbase64エンコードして表示
            } else {
                alert('画像の更新に失敗しました。');
            }
        } else {
            alert('エラーが発生しました。');
        }
    };

    xhr.send(formData); // FormDataを送信
}





// function sendImage(imageId) {
//     const input = document.getElementById(`imageInput${imageId}`);
//     const formData = new FormData();
//     formData.append("image", input.files[0]);
//     formData.append("image_id", imageId); // 画像IDをフォームデータに追加

//     const xhr = new XMLHttpRequest();
//     xhr.open("POST", "update_subthumbnail.php", true);
//     xhr.onload = function () {
//         if (xhr.status == 200) {
//             const response = xhr.responseText; // サーバーからの応答（Base64エンコードされた画像データ）

//             // サーバーから画像のBase64データが返ってきた場合
//             if (response.startsWith("data:image/jpeg;base64,") || response.startsWith("data:image/png;base64,")) {
//                 const newImageSrc = response; // 返ってきたBase64画像
//                 const imgElement = document.getElementById(`shopImage${imageId}`); // 修正: imageIdを使う

//                 if (imgElement) {
//                     imgElement.src = newImageSrc; // 更新された画像を表示
//                     alert("画像が更新されました。");
//                 } else {
//                     console.error("画像要素が見つかりません。");
//                 }
//             } else {
//                 alert("画像の更新に失敗しました。");
//             }
//         } else {
//             alert("サーバーエラーが発生しました。");
//         }
//     };
//     xhr.send(formData);
// }


   
   // categoryが選択された時にsubcategoriesを更新
   function updateSubcategory(categoryElement) {
    var categoryId = categoryElement.value;
    var subcategorySelect = categoryElement.closest('tr').querySelector('.subcategory');

    // AJAXを使用してサーバーにリクエストを送信
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'get_subcategories.php?category_id=' + categoryId, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            // サブカテゴリーを更新
            subcategorySelect.innerHTML = xhr.responseText;
        }
    };
    xhr.send();
}
const thumbnails = document.querySelectorAll('.thumbnail');
const modal = document.getElementById('imageModal');
const modalContent = document.getElementById('modalContent');
const closeModal = document.getElementById('closeModal');

thumbnails.forEach(thumbnail => {
    thumbnail.addEventListener('click', function() {
        const shopId = this.dataset.shopId; // クリックしたサムネイルのshop_idを取得
        fetch(`show_images.php?shop_id=${shopId}`) // shop_idを渡して画像を取得
            .then(response => response.json()) // 画像のBase64エンコードされた配列を取得
            .then(images => {
                // モーダル内のコンテンツをクリア
                modalContent.innerHTML = '';

                if (images.length > 0) {
                    // 画像を順にモーダルに追加
                    images.forEach(encodedImg => {
                        const imgElement = document.createElement('img');
                        imgElement.src = encodedImg; // Base64エンコードされた画像をセット
                        imgElement.alt = '商品画像';
                        modalContent.appendChild(imgElement); // モーダル内に画像を追加
                    });
                    modal.style.display = 'flex'; // モーダルを表示
                } else {
                    modalContent.innerHTML = "画像が見つかりません"; // 画像がない場合
                    modal.style.display = 'flex'; // モーダルを表示
                }
            })
            .catch(error => {
                console.error("画像の取得に失敗しました:", error);
            });
    });
});

// // モーダルを閉じる処理
// closeModal.addEventListener('click', function() {
//     modal.style.display = 'none';
// });

// モーダルの外側をクリックすると閉じる
// window.addEventListener('click', function(event) {
//     if (event.target === modal) {
//         modal.style.display = 'none';
//     }
// });


