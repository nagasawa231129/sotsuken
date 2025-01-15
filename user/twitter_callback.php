<?php
session_start();
require_once __DIR__ . '/../../vendor/autoload.php';

use League\OAuth2\Client\Provider\GenericProvider;

// プロバイダーの再設定
$provider = new GenericProvider([
    'clientId'                => 'SENxclpNQ3NRSy1sVGtmcEw2LUQ6MTpjaQ',
    'clientSecret'            => 'P7X2XGQ79g_ptEmXOka6JCuudbBHs6MN124uNlCPlcL0KUEp94',
    'redirectUri'             => 'https://y231129.daa.jp/sotsuken/sotsuken/user/twitter_callback.php',
    'urlAuthorize'            => 'https://twitter.com/i/oauth2/authorize',
    'urlAccessToken'          => 'https://api.twitter.com/2/oauth2/token',
    'urlResourceOwnerDetails' => 'https://api.twitter.com/2/users/me',
]);

// stateの検証
if (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['state'])) {
    unset($_SESSION['state']);
    die('Invalid state');
}

try {
    // アクセストークンの取得
    $accessToken = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code'],
    ]);

    // ユーザー情報の取得
    $resourceOwner = $provider->getResourceOwner($accessToken);

    // ユーザー情報をセッションに保存
    $_SESSION['twitter_user'] = $resourceOwner->toArray();
    $_SESSION['access_token'] = $accessToken->getToken();

    // メールアドレスが含まれている場合、取得
    if (isset($resourceOwner->toArray()['email'])) {
        $_SESSION['email'] = $resourceOwner->toArray()['email'];
    }

    // トップページにリダイレクト
    header('Location: toppage.php');
    exit();

} catch (Exception $e) {
    echo 'エラーが発生しました: ' . $e->getMessage();
    exit();
}
