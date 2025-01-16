<?php
session_start();
include "../../db_open.php";

$code = isset($_GET['code']) ? $_GET['code'] : null;
$state = isset($_GET['state']) ? $_GET['state'] : null;

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}
$userId = $_SESSION['id'];

if (!$code || !$state) {
    die('認証コードまたはstateがありません。');
}

// セッションに保存されているstateと照合
if ($state !== $_SESSION['state']) {
    die('Invalid state');
}

// 認証コードを使ってアクセストークンを取得する
$channelId = '2006579866';
$channelSecret = '26f246b185363ee411b39a3577b8c25f';
$redirectUri = 'https://y231129.daa.jp/sotsuken/sotsuken/user/line_link_callback.php';

// アクセストークンを取得
$tokenUrl = "https://api.line.me/oauth2/v2.1/token";
$data = [
    'grant_type' => 'authorization_code',
    'code' => $code,
    'redirect_uri' => $redirectUri,
    'client_id' => $channelId,
    'client_secret' => $channelSecret,
];
$options = [
    'http' => [
        'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
    ],
];
$context  = stream_context_create($options);
$response = file_get_contents($tokenUrl, false, $context);
$responseData = json_decode($response, true);

if ($response === false) {
    die('アクセストークンの取得に失敗しました。');
}

$_SESSION['access_token'] = $responseData['access_token'];


// LINEプロフィールを取得
$profileUrl = "https://api.line.me/v2/profile";
$options = [
    'http' => [
        'header' => "Authorization: Bearer " . $responseData['access_token'],
        'method' => 'GET',
    ],
];
$context  = stream_context_create($options);
$profileResponse = file_get_contents($profileUrl, false, $context);
$profileData = json_decode($profileResponse, true);

if (!isset($profileData['userId'])) {
    echo "LINEユーザー情報の取得に失敗しました。";
    exit;
}

// データベースにline_user_idを登録
$lineUserId = $profileData['userId'];
$stmt = $dbh->prepare("UPDATE user SET line_user_id = ? WHERE user_id = ?");
$stmt->execute([$lineUserId, $userId]);

echo "LINEアカウントが連携されました。";
header("Location: account.php");
exit;
?>
