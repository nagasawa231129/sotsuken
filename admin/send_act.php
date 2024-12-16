<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_items'])) {
    $selected_items = $_POST['selected_items'];
    $user_mails = $_POST['user_mail'];
    $goods = $_POST['goods'];
    $sizes = $_POST['size'];
    $brands = $_POST['brand'];
    $colors = $_POST['color'];
    $quantities = $_POST['quantity'];

 






        try {
        include './../../db_open.php'; // データベース接続

        // トランザクションを開始
        $dbh->beginTransaction();

        // 更新処理をループで実行
        $stmt = $dbh->prepare("UPDATE cart_detail SET trade_situation = 3 WHERE cart_id = :cart_id");
        foreach ($selected_items as $item) {
            $stmt->bindValue(':cart_id', $item, PDO::PARAM_INT);
            $stmt->execute();
        }

        // トランザクションをコミット
        $dbh->commit();

        $mail = $user_mails[0]; // 最初のメールアドレスを宛先に設定
        $subject = "卒研TOWN 荷物発送について";
        $message = "平素より卒研TOWNをご利用いただき、誠にありがとうございます。\n\n";
        $message .= "以下の商品が発送されました。\n\n";
        $message .= "------------------------------------\n";
    
        foreach ($selected_items as $index => $item) {
            $good = $goods[$index] ?? '不明';
            $size = $sizes[$index] ?? '不明';
            $color = $colors[$index] ?? '不明';
            $quantity = $quantities[$index] ?? '不明';
            $brand = $brands[$index] ?? '不明';
    
            $message .= "商品名     : $good\n";
            $message .= "サイズ     : $size\n";
            $message .= "色         : $color\n";
            $message .= "個数       : $quantity\n";
            $message .= "ブランド   : $brand\n";
            $message .= "------------------------------------\n";
        }
    
        $message .= "\n今後とも卒研TOWNをよろしくお願いいたします。\n";
        $message .= "お問い合わせ: support@example.com\n";
    
        $headers = "From: yamibaito@jp.com";
    
        if (mb_send_mail($mail, $subject, $message, $headers)) {
            echo "メールが送信されました。<br>";
        } else {
            echo "メール送信に失敗しました。<br>";
        }
    




        // 更新成功のアラートを表示し、元のページへリダイレクト
        echo '<script>';
        echo 'alert("更新しました。");'; // アラートを表示
        echo 'window.location.href = "' . htmlspecialchars($_SERVER['HTTP_REFERER'], ENT_QUOTES, 'UTF-8') . '";'; // 元のページへ戻る
        echo '</script>';
    } catch (Exception $e) {
        // トランザクションをロールバック
        $dbh->rollBack();
        echo '<script>';
        echo 'alert("エラーが発生しました: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '");';
        echo 'window.location.href = "' . htmlspecialchars($_SERVER['HTTP_REFERER'], ENT_QUOTES, 'UTF-8') . '";';
        echo '</script>';
    }
} else {
    echo '<script>';
    echo 'alert("商品が選択されていません。");';
    echo 'window.location.href = "' . htmlspecialchars($_SERVER['HTTP_REFERER'], ENT_QUOTES, 'UTF-8') . '";';
    echo '</script>';

}





 ?>
