<?php             
    include '../../db_open.php';
    session_start();

    session_start();
    if (isset($_SESSION['id'])) {
        $userId = $_SESSION['id'];
    } else {
        header('Location: login.php');
        $userId = null;
    }

    $user_id = $_SESSION['id'] ?? null;

    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['shop_id'])){
        $shopId = $_POST['shop_id'];

        $deleteSql = "DELETE FROM cart WHERE shop_id = :shop_id AND user_id = :user_id";
        $stmt = $dbh->prepare($deleteSql);

        $stmt->bindParam(':shop_id', $shopId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

        $stmt->execute();

        
    }
?>