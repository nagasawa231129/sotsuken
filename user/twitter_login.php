<?php
session_start();
require_once __DIR__ . '/../../vendor/autoload.php'; // Composerのautoloadファイルを読み込む

use League\OAuth2\Client\Provider\GenericProvider;

// Twitter API認証の設定
$provider = new GenericProvider([
    'clientId'                => 'SENxclpNQ3NRSy1sVGtmcEw2LUQ6MTpjaQ',  // クライアントID
    'clientSecret'            => 'P7X2XGQ79g_ptEmXOka6JCuudbBHs6MN124uNlCPlcL0KUEp94',  // クライアントシークレット
    'redirectUri'             => 'https://y231129.daa.jp/sotsuken/sotsuken/user/twitter_callback.php',  // コールバックURL
    'urlAuthorize'            => 'https://twitter.com/i/oauth2/authorize',  // 認証URL
    'urlAccessToken'          => 'https://api.twitter.com/2/oauth2/token',  // アクセストークンURL
    'urlResourceOwnerDetails' => 'https://api.twitter.com/2/users/me',  // ユーザー情報URL
]);

// ランダムなstateを生成
$state = bin2hex(random_bytes(16));  // ランダムに生成
$_SESSION['oauth2state'] = $state;  // セッションに保存

// 認証URLを生成
$authorizationUrl = $provider->getAuthorizationUrl([
    'state' => $state,
    'scope' => 'email'  // メールアドレスをリクエストするスコープ
]);
// ユーザーを認証ページにリダイレクト
header('Location: ' . $authorizationUrl);
exit();
