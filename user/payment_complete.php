<!DOCTYPE html>
<html>
    <head>
        <title>決済完了</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <h1>決済完了</h1>
        <?php
        include '../../db_open.php';

        // 接続が成功している場合
        if ($dbh) {
            // // ユーザーIDを取得（例えば、セッションから）
            // session_start();
            // $userId = $_SESSION['user_id']; // ユーザーIDがセッションに保存されていると仮定

            // `cart` テーブルの `trade_situation` を 4 に更新するSQL
            $sql = "UPDATE `cart` SET `trade_situation` = 4 WHERE `user_id` = :user_id AND `trade_situation` != 4";

            // SQL文の実行準備
            $stmt = $dbh->prepare($sql);
            // パラメータをバインド
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

            // SQLの実行
            $result = $stmt->execute();

            if ($result) {
                echo "<p>決済が完了しました！</p>";
            } else {
                echo "<p>決済の更新に失敗しました。</p>";
            }
        } else {
            echo "<p>データベース接続に失敗しました。</p>";
        }

        // データベース接続を閉じる
        $dbh = null;
        ?>
        <p><a href="toppage.php">トップページに戻る</a></p>
    </body>
</html>
