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
// 画像を削除する関数
function deleteImage(shop_id, image_id) {
    if (confirm("本当にこの画像を削除しますか？")) {
        var formData = new FormData();
        formData.append('image_id', image_id);

        // AJAXで削除リクエストを送信
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'delete_image.php', true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                // 成功した場合は画像をページから削除
                var imgElement = document.getElementById('shopImage' + shop_id);
                imgElement.parentElement.removeChild(imgElement); // 画像を削除

                // 「×」ボタンも削除
                var deleteButton = document.querySelector('.delete-button');
                if (deleteButton) {
                    deleteButton.parentElement.removeChild(deleteButton);
                }
                alert("画像が削除されました。");
            } else {
                alert("画像の削除に失敗しました。");
            }
        };
        xhr.send(formData);
    }
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


