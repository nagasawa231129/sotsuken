<!DOCTYPE html>
<html lang="ja">
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登録確認 - 就活Helper.com</title>
    <link rel="stylesheet" href="./css/st_css/student_function.css">

<?php

function update()
{
    include "./../../db_open.php"; // データベース接続
  
        $shop_id = $_POST['shop_id'];
        $brand = $_POST['brand'];
        $goods = $_POST['goods'];
        $price = $_POST['price'];
        $size = $_POST['size'];
        $color = $_POST['color'];
        $category = $_POST['category'];
        $subcategory = $_POST['subcategory'];
        $gender = $_POST['gender'];

        // 更新処理
        $update_sql = "UPDATE shop SET 
                        brand_id = ?, 
                        goods = ?, 
                        price = ?, 
                        size = ?, 
                        color = ?, 
                        category_id = ?, 
                        subcategory_id = ?, 
                        gender = ? 
                        WHERE shop_id = ?";
        $stmt_update = $dbh->prepare($update_sql);
        $stmt_update->execute([$brand, $goods, $price, $size, $color, $category, $subcategory, $gender, $shop_id]);

        // 更新後リロード
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }



function delete(){
    include "./../../db_open.php"; // データベース接続
    $shop_id = $_POST['shop_id'];

    // 削除処理
    $delete_sql = "DELETE FROM shop WHERE shop_id = ?";
    $stmt_delete = $dbh->prepare($delete_sql);
    $stmt_delete->execute([$shop_id]);

    // 削除後リロード
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

function insert(){
    include "./../../db_open.php"; // データベース接続
    $brand = $_POST['brand'];
    $goods = $_POST['goods'];
    $price = $_POST['price'];
    $size = $_POST['size'];
    $color = $_POST['color'];
    $category = $_POST['category'];
    $subcategory = $_POST['subcategory'];
    $gender = $_POST['gender'];

    // 追加処理
    $insert_sql = "INSERT INTO shop (brand_id, goods, price, size, color, category_id, subcategory_id, gender) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = $dbh->prepare($insert_sql);
    $stmt_insert->execute([$brand, $goods, $price, $size, $color, $category, $subcategory, $gender]);

    // 追加後リロード
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}