<?php
session_start();
include "../../db_open.php";

// リダイレクトされたURLからcodeとstateを取得
$code = isset($_GET['code']) ? $_GET['code'] : null;
$state = isset($_GET['state']) ? $_GET['state'] : null;

// code または state が存在しない場合はエラーを表示
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
$redirectUri = 'https://y231129.daa.jp/sotsuken/sotsuken/user/line_callback.php';

// アクセストークン取得のリクエスト
$tokenUrl = 'https://api.line.me/oauth2/v2.1/token';
$postData = [
    'grant_type' => 'authorization_code',
    'code' => $code,
    'redirect_uri' => $redirectUri,
    'client_id' => $channelId,
    'client_secret' => $channelSecret,
];

$options = [
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => http_build_query($postData),
    ]
];
$context = stream_context_create($options);
$response = file_get_contents($tokenUrl, false, $context);

// レスポンスが成功か確認
if ($response === false) {
    die('アクセストークンの取得に失敗しました。');
}

$tokenData = json_decode($response, true);

// アクセストークンをセッションに保存
$_SESSION['access_token'] = $tokenData['access_token'];

// ユーザー情報を取得
$userProfileUrl = 'https://api.line.me/v2/profile';
$options = [
    'http' => [
        'method' => 'GET',
        'header' => "Authorization: Bearer " . $tokenData['access_token'] . "\r\n",
    ]
];
$context = stream_context_create($options);
$userProfileResponse = file_get_contents($userProfileUrl, false, $context);

// ユーザー情報の取得に失敗した場合
if ($userProfileResponse === false) {
    die('ユーザー情報の取得に失敗しました。');
}

$userProfile = json_decode($userProfileResponse, true);

// ユーザー情報を取得
$email = isset($userProfile['email']) ? $userProfile['email'] : null;
$lineUserId = $userProfile['userId']; // LINEのuserId
$displayName = $userProfile['displayName']; // 表示名

// データベースでline_user_idを検索
$sql = "SELECT user_id, display_name FROM user WHERE line_user_id = :lineUserId";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':lineUserId', $lineUserId);
$stmt->execute();
$userExists = $stmt->fetch(PDO::FETCH_ASSOC);

if ($userExists) {
    // ユーザーがすでに存在する場合、セッションに保存してログイン
    $_SESSION['login'] = true;
    $_SESSION['id'] = $userExists['user_id'];
    $_SESSION['display_name'] = $userExists['display_name'];
    echo "ログイン成功";
} else {
    // ユーザーが存在しない場合、新規登録
    if ($email !== null) {
        // メールアドレスが取得できている場合
        $sql = "INSERT INTO user (line_user_id, mail, display_name) VALUES (:lineUserId, :mail, :displayName)";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':mail', $email);
    } else {
        // メールアドレスが取得できていない場合
        $sql = "INSERT INTO user (line_user_id, display_name) VALUES (:lineUserId, :displayName)";
        $stmt = $dbh->prepare($sql);
    }

    $stmt->bindParam(':lineUserId', $lineUserId);
    $stmt->bindParam(':displayName', $displayName);
    $stmt->execute();

    // 新規登録後、セッションにIDを保存
    $_SESSION['id'] = $dbh->lastInsertId();
    $_SESSION['display_name'] = $displayName;
    echo "新規登録が完了しました";
}

// トップページにリダイレクト
header('Location: toppage.php');
exit();
?>
