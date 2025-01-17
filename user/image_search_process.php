<?php
include "../../db_open.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    // アップロードされたファイル情報を確認
    var_dump($_FILES);

    // アップロードされたファイルの一時ファイルのパス
    $uploadedFile = $_FILES['image']['tmp_name'];

    echo "</br>";
    var_dump($uploadedFile);

    // Python API の URL を指定
    $pythonApiUrl = 'https://your-python-api-url.com/image_search'; // Python の API URL に置き換える

    // cURL を使用して画像を Python サーバーに送信
    $cfile = new CURLFile($uploadedFile, $_FILES['image']['type'], $_FILES['image']['name']);
    $postData = array('image' => $cfile);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $pythonApiUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // レスポンスを文字列で取得する
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // HTTPS の場合、証明書検証を無効化（本番環境では必要に応じて設定）

    $response = curl_exec($ch);
    $error = curl_error($ch);
    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "</br>";
    var_dump($response);
    echo "</br>";
    var_dump($error);
    echo "</br>";
    var_dump($httpStatus);
    echo "</br>";

    // レスポンスを JSON としてデコード
    $histogramData = json_decode($response, true);
    var_dump($histogramData); // 解析されたデータを確認
    echo "</br>";

    if ($histogramData && $httpStatus === 200) {
        echo "<h3>画像のヒストグラム</h3>";
        echo "<pre>" . print_r($histogramData, true) . "</pre>";

        // ここでデータベース内の商品画像との類似度を計算するロジックを追加できます
        // 例えば、データベース内の画像の特徴量を取得して比較するなど
    } else {
        echo "画像の特徴量の抽出に失敗しました。";
    }
}
?>

<!-- HTMLフォーム -->
<form action="" method="POST" enctype="multipart/form-data">
    <label for="image">画像を選択:</label>
    <input type="file" name="image" id="image" accept="image/*">
    <button type="submit">検索</button>
</form>
