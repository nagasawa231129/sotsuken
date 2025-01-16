<?php
    include '../../db_open.php';
    session_start();
    $address = "";
  

    $user_id = $_SESSION['id'] ?? null;

    if($user_id == null){
        echo "<p>ログインしていません</p>";
        exit;   
    
    }

    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_address'])){
    $newAddress  = $_POST['new_address'];

    $updateAddressSql = "UPDATE user SET address = :new_address WHERE user_id = :user_id";
    $stmt = $dbh->prepare($updateAddressSql);
    $stmt->bindParam(':new_address', $newAddress, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

    if($stmt->execute()){
        $address = $newAddress;
        echo "<p>住所の変更が完了しました</p>";
        echo "<a href='register.php'>戻る</a>";
    }else{
        echo "<p>住所の変更に失敗しました</p>";
    }
    }
?>