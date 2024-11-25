<?php
session_start();

// ユーザーが言語を選択した場合、セッションに保存
if (isset($_POST['lang'])) {
    $_SESSION['lang'] = $_POST['lang'];
    header("Location: settings.php"); // 再読み込みして設定完了
    exit();
}

// 現在の言語を取得
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ja'; // デフォルトは日本語

// 言語ファイルを読み込み
$lang_file = __DIR__ . "/{$lang}.php";
if (file_exists($lang_file)) {
    include($lang_file);
} else {
    die("Error: Language file not found.");
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>設定</title>
</head>
<body>
    <h1><?php echo $translations['welcome_message']; ?></h1>
    <form method="post" action="settings.php">
        <label for="lang"><?php echo $translations['language_select']; ?>:</label>
        <select name="lang" id="lang">
            <option value="ja" <?php if ($lang == 'ja') echo 'selected'; ?>>日本語</option>
            <option value="en" <?php if ($lang == 'en') echo 'selected'; ?>>English</option>
        </select>
        <button type="submit"><?php echo $translations['save_button']; ?></button>
    </form>
    <a href="toppage.php">トップページ</a>
</body>
</html>
