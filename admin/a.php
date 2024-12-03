<?php
// データベース接続
include './../../db_open.php';

// 送信されたデータを取得
// $thumbnail = $_POST['thumbnail'];  // 画像データ（またはファイルパス）
$brands = $_POST['brand'];
$goods = $_POST['goods'];
$prices = $_POST['price'];
$sizes = $_POST['size'];
$colors = $_POST['color'];
$categories = $_POST['category'];
$subcategories = $_POST['subcategory'];
$genders = $_POST['gender'];
$goods_info = $_POST['goods_info'];


echo $goods_info;