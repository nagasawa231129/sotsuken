<?php
session_start();

// LINEのチャネルIDとチャネルシークレット
$channelId = '2006579866';
$channelSecret = '26f246b185363ee411b39a3577b8c25f';
$redirectUri = 'https://y231129.daa.jp/sotsuken/sotsuken/user/line_link_callback.php';

// ランダムな16バイトの文字列を生成してstateに設定
$state = bin2hex(random_bytes(16));

// stateをセッションに保存
$_SESSION['state'] = $state;

// LINEログイン用の認証URLを作成
$lineLoginUrl = 'https://access.line.me/oauth2/v2.1/authorize';
$scope = 'profile openid email';  // emailを追加

// ログインURLに必要なパラメータを設定
$loginUrl = $lineLoginUrl . '?' . http_build_query([
    'response_type' => 'code',
    'client_id' => $channelId,
    'redirect_uri' => $redirectUri,
    'scope' => $scope,
    'state' => $state,  // stateをログインURLに付加
]);

// ログインURLにリダイレクト
header('Location: ' . $loginUrl);
exit();
?>
