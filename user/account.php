<?php
include "settings.php";
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo $translations['language_setting']; ?></title>
</head>
<body>
    <h2><?php echo $translations['language_setting']; ?></h2>
    <form action="account.php" method="get">
        <select name="lang">
            <option value="ja" <?php echo $lang === 'ja' ? 'selected' : ''; ?>>日本語</option>
            <option value="en" <?php echo $lang === 'en' ? 'selected' : ''; ?>>English</option>
        </select>
        <button type="submit"><?php echo $translations['save_button']; ?></button>
    </form>
    <a href="toppage.php"><?php echo $translations['back_to_toppage']; ?></a>
</body>
</html>
