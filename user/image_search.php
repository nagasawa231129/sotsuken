<?php

function calculateSimilarity($hist1, $hist2) {
    // ヒストグラムの類似度（ユークリッド距離）
    $distance = 0;

    // 各色チャネル（Hue, Saturation, Value）の比較
    foreach (['hue', 'saturation', 'value'] as $channel) {
        $distance += array_sum(array_map(function($a, $b) {
            return pow($a - $b, 2);
        }, $hist1[$channel], $hist2[$channel]));
    }

    return sqrt($distance);
}

function getImageHistogram($imagePath) {
    // Pythonスクリプトを呼び出して画像のヒストグラムを取得
    $command = escapeshellcmd("python3 /sotsuken/sotsuken/user/image_search.py " . escapeshellarg($imagePath));
    $output = shell_exec($command);

    // JSONデータを解析
    $histData = json_decode($output, true);

    return $histData;
}

// 例：2つの画像のヒストグラムを比較する
$image1Path = 'path/to/image1.jpg';
$image2Path = 'path/to/image2.jpg';

// 画像1と画像2のヒストグラムを取得
$hist1 = getImageHistogram($image1Path);
$hist2 = getImageHistogram($image2Path);

// 類似度を計算
$similarity = calculateSimilarity($hist1, $hist2);

echo "Similarity: " . $similarity . "\n";

?>
