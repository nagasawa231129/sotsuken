// ページを離れる前にスクロール位置を保存
// window.onbeforeunload = function(){
//     localStorage.setItem("scrollPosition", window.scrollY);
//   };
//   // // スクロール位置保存に戻る
//   window.onload = function(){
//     if (localStorage.getItem("scrollPosition") !== null) {
//       window.scrollTo(0, localStorage.getItem("scrollPosition"));
//     }
//   };
function updateThumbnail(shopId) {
    var input = document.getElementById('thumbnailInput' + shopId);
    if (!input) {
        console.error('Input element not found');
        return;
    }

    var file = input.files[0];
    if (!file) {
        console.error('No file selected');
        return;
    }

    // 画像のプレビュー表示
    var reader = new FileReader();
    reader.onload = function(e) {
        // 即座にプレビュー画像を更新
        document.getElementById('thumbnailImage' + shopId).src = e.target.result;
    };
    reader.readAsDataURL(file);

    // サーバーに送信
    var formData = new FormData();
    formData.append('shop_id', shopId);
    formData.append('thumbnail', file);

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'update_thumbnail.php', true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            // サムネイルが正常に更新された場合に画像を変更
            // レスポンスで返されたサムネイルURLを利用して、画像のURLを更新
            var response = JSON.parse(xhr.responseText);
            if (response.thumbnail) {
                document.getElementById('thumbnailImage' + shopId).src = response.thumbnail + '?t=' + new Date().getTime();
                console.log('サムネイルが正常に更新されました');
            }
        } else {
            alert('サムネイルの更新に失敗しました');
        }
    };
    xhr.send(formData);
}





document.addEventListener('DOMContentLoaded', function() {
    // 他の初期化コードをここに追加
});

var imageElement = document.getElementById('subthumbnailImage' + imageId);
if (imageElement && imageElement.parentElement) {
    imageElement.parentElement.remove();
} else {
    console.error('Element or parentElement not found');
}



function deleteImage(shopId, imageId) {
    if (confirm('本当にこの画像を削除しますか？')) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'delete_image.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                // 画像の削除が成功した場合、画像要素を削除
                var imageContainer = document.getElementById('imageContainer' + imageId);
                if (imageContainer) {
                    imageContainer.remove();
                } else {
                    console.error('Image container not found for imageId: ' + imageId);
                }
            } else {
                alert('画像の削除に失敗しました。');
            }
        };
        xhr.send('shop_id=' + shopId + '&image_id=' + imageId);
    }
}

   
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
// const thumbnails = document.querySelectorAll('.thumbnail');
// const modal = document.getElementById('imageModal');
// const modalContent = document.getElementById('modalContent');
// const closeModal = document.getElementById('closeModal');

// thumbnails.forEach(thumbnail => {
//     thumbnail.addEventListener('click', function() {
//         const shopId = this.dataset.shopId; // クリックしたサムネイルのshop_idを取得
//         fetch(`show_images.php?shop_id=${shopId}`) // shop_idを渡して画像を取得
//             .then(response => response.json()) // 画像のBase64エンコードされた配列を取得
//             .then(images => {
//                 // モーダル内のコンテンツをクリア
//                 modalContent.innerHTML = '';

//                 if (images.length > 0) {
//                     // 画像を順にモーダルに追加
//                     images.forEach(encodedImg => {
//                         const imgElement = document.createElement('img');
//                         imgElement.src = encodedImg; // Base64エンコードされた画像をセット
//                         imgElement.alt = '商品画像';
//                         modalContent.appendChild(imgElement); // モーダル内に画像を追加
//                     });
//                     modal.style.display = 'flex'; // モーダルを表示
//                 } else {
//                     modalContent.innerHTML = "画像が見つかりません"; // 画像がない場合
//                     modal.style.display = 'flex'; // モーダルを表示
//                 }
//             })
//             .catch(error => {
//                 console.error("画像の取得に失敗しました:", error);
//             });
//     });
// });



document.getElementById('uploadButton').addEventListener('click', function() {
    console.log("Button clicked"); // ボタンがクリックされたかを確認
    var fileInput = document.getElementById('fileInput');
    var shopId = document.getElementById('shop_id').value;
    var files = fileInput.files;

    if (files.length > 0) {
        var formData = new FormData();
        formData.append('shop_id', shopId); // shop_idをフォームデータに追加

        // ファイルをFormDataに追加
        for (var i = 0; i < files.length; i++) {
            formData.append('subthumbnail[]', files[i]);
        }

        // アップロード中に表示する進行状況
        document.getElementById('uploadStatus').textContent = 'アップロード中...';

        // Fetch APIで画像を非同期に送信
        fetch('insert_image.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json()) // サーバーからのレスポンスをJSONとして処理
        .then(data => {
            if (data.success) {
                document.getElementById('uploadStatus').textContent = '画像が正常にアップロードされました。';
            } else {
                document.getElementById('uploadStatus').textContent = '画像のアップロードに失敗しました。';
            }
        })
        .catch(error => {
            document.getElementById('uploadStatus').textContent = 'アップロードエラーが発生しました。';
            console.error(error);
        });
    } else {
        document.getElementById('uploadStatus').textContent = '画像を選択してください。';
    }
});
