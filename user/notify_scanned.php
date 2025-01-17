<?php
// スキャン状態をファイルに保存（セッションやデータベースでも可能）
file_put_contents('qr_scanned.txt', json_encode(['scanned' => true]));
?>
