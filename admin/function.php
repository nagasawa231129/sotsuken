<?php
function update() {
    global $dbh; // データベース接続をグローバルに取得
    $price = 0;
    $shop_id = $_POST['shop_id'];
    $goods = $_POST['goods'];
    $original_price = $_POST['price'];
    $goods_info = $_POST['goods_info'];

    // その他のフォームデータを取得
    $size = $_POST['size'];
    $color = $_POST['color'];
    $category = $_POST['category'];
    $subcategory = $_POST['subcategory'];
    $gender = $_POST['gender'];
    $brand = $_POST['brand'];

    $sale_stmt = $dbh->prepare("SELECT sale_id FROM shop WHERE shop_id = :shop_id");
    $sale_stmt->bindValue(':shop_id', $shop_id, PDO::PARAM_INT);
    $sale_stmt->execute();
            $sale = $sale_stmt->fetch(PDO::FETCH_ASSOC);
            $sale_id = $sale['sale_id'];

    $price =  (1-$sale_id /10) * $original_price; 

    // 商品情報を更新するSQLクエリ
    $update_sql = "UPDATE shop 
                   SET goods = :goods, price = :price , original_price = :original_price, size = :size, color = :color, category_id = :category, 
                       subcategory_id = :subcategory, gender = :gender, brand_id = :brand, exp = :exp
                   WHERE shop_id = :shop_id";

    // SQLの準備
    $stmt = $dbh->prepare($update_sql);
    $stmt->bindParam(':price', $price );
    $stmt->bindParam(':goods', $goods);
    $stmt->bindParam(':original_price', $original_price);
    $stmt->bindParam(':size', $size);
    $stmt->bindParam(':color', $color);
    $stmt->bindParam(':category', $category);
    $stmt->bindParam(':subcategory', $subcategory);
    $stmt->bindParam(':gender', $gender);
    $stmt->bindParam(':brand', $brand);
    $stmt->bindParam(':shop_id', $shop_id);
    $stmt->bindParam(':exp', $goods_info);

    // 実行
    $stmt->execute();

    // サブ画像のアップロード処理
    if (isset($_FILES['subthumbnail']) && !empty($_FILES['subthumbnail']['name'][0])) {
        // 画像情報をデータベースに保存するSQL
        $insert_img_sql = "INSERT INTO image (shop_id, img) VALUES (:shop_id, :img)";
        $insert_img_stmt = $dbh->prepare($insert_img_sql);

        // 画像が複数アップロードされている場合
        foreach ($_FILES['subthumbnail']['tmp_name'] as $key => $tmp_name) {
            $img_name = $_FILES['subthumbnail']['name'][$key];
            $img_tmp = $_FILES['subthumbnail']['tmp_name'][$key];
            $img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));

            // 画像の拡張子が許可されているか確認
            if (in_array($img_ext, ['jpeg', 'jpg', 'png'])) {
                // 画像のバイナリデータを読み込む
                $img_data = file_get_contents($img_tmp);

                // データベースに画像バイナリデータを保存
                $insert_img_stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
                $insert_img_stmt->bindParam(':img', $img_data, PDO::PARAM_LOB); // LONGBLOBとして保存
                $insert_img_stmt->execute();
            } else {
                echo "許可されていないファイル形式です。";
            }
        }
    }

    // 画像が選択されていなくても商品情報は更新される
    echo "<script>alert('商品情報が正常に更新されました。');</script>";
}




function delete(){
    global $dbh; // データベース接続をグローバルに取得
    $shop_id = $_POST['shop_id'];

    // 商品を削除するSQLクエリ
    $delete_sql = "DELETE FROM shop WHERE shop_id = :shop_id";

    // SQLの準備
    $stmt = $dbh->prepare($delete_sql);
    $stmt->bindParam(':shop_id', $shop_id);

    // 実行
    $stmt->execute();
}

function s_reset(){
    global $dbh; // データベース接続をグローバルに取得
    $search_query = '';  // 検索クエリを空にしてすべての商品を表示
    $stmt = $dbh->prepare("SELECT 
                                    shop.shop_id,
            shop.goods,
            shop.original_price,
            shop.exp,
            shop.size AS size_id,
            shop.color AS color_id,
            shop.category_id AS category_id,
            shop.gender AS gender_id,
            brand.brand_id, 
            brand.brand_name,          
            color.ja_color AS color_name,  
            gender.gender AS gender_name,
            size.size,
            subcategory.subcategory_name,
            category.category_name,
            shop.subcategory_id AS subcategory_id,
            shop.thumbnail
                                    FROM shop
                                    LEFT JOIN brand ON shop.brand_id = brand.brand_id  
                                    LEFT JOIN color ON shop.color = color.color_id  
                                    LEFT JOIN category ON shop.category_id = category.category_id  
                                    LEFT JOIN gender ON shop.gender = gender.gender_id
                                    LEFT JOIN subcategory ON shop.subcategory_id = subcategory.subcategory_id
                                    LEFT JOIN size ON shop.size = size.size_id");
    $stmt->execute();
}