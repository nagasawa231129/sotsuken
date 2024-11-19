<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// セッション変数を全てクリア
$_SESSION = array();

// セッションを破棄
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// デバッグ用出力
echo "セッションを破棄します。<br>";


// セッションを終了
session_destroy();

// ログインページにリダイレクト
header("Location: login.php");
exit();
?>