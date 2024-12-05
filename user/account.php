<?php
include "../head.php";
include "../header.php";
include "../../db_open.php";
echo "<link rel='stylesheet' href='header.css'>";
echo "<link rel='stylesheet' href='account.css'>";
// ユーザーIDをセッションから取得
if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];
} else {
    header("Location: login.php"); // 再読み込みして設定完了
    exit;
}

if (isset($_POST['lang'])) {
    $_SESSION['lang'] = $_POST['lang'];
    header("Location: account.php"); // 再読み込みして設定完了
    exit();
}

// ユーザー情報をデータベースから取得
$stmt = $dbh->prepare("SELECT * FROM user LEFT OUTER JOIN gender ON gender.gender_id = user.gender_id WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

function formatPhoneNumber($phoneNumber)
{
    // 電話番号が11桁であれば、適切にハイフンを挿入
    if (strlen($phoneNumber) == 11) {
        return substr($phoneNumber, 0, 3) . '-' . substr($phoneNumber, 3, 4) . '-' . substr($phoneNumber, 7);
    }
    return $phoneNumber;  // それ以外の形式はそのまま表示
}
// var_dump($user);
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

<body>
    <div class="container">
        <!-- サブ項目 -->
        <aside>
            <h2>メニュー</h2>
            <ul>
                <!-- メインカテゴリ -->
                <li><strong>会員情報</strong></li>
                <li><a href="#profile">会員登録情報</a></li>
                <li><a href="#id-link">ID連携</a></li>
                <li><a href="#lang_link">言語設定</a></li>
                <hr>
                <!-- 注文履歴 -->
                <li><strong>注文履歴</strong></li>
                <li><a href="order.php#history">注文履歴</a></li>
                <li><a href="order.php#pending">発送前商品</a></li>
                <li><a href="order.php#shipped">発送済み商品</a></li>
                <li><a href="order.php#review">レビュー</a></li>
                <hr>
                <!-- 退会 -->
                <li><strong>退会</strong></li>
                <li><a href="unsubscribe.php">退会手続き</a></li>
            </ul>
        </aside>

        <!-- メイン部分 -->
        <main>
            <div>
                <div id="profile">

                    <h2>会員登録情報</h2>
                    <table>
                        <tr>
                            <td><?php echo htmlspecialchars($user['display_name']); ?></td>
                        </tr>
                        <tr>
                            <td><?php echo htmlspecialchars($user['sei']); ?><?php echo htmlspecialchars($user['mei']); ?></td>
                        </tr>
                        <tr>
                            <td><?php echo htmlspecialchars($user['kanasei']); ?><?php echo htmlspecialchars($user['kanamei']); ?></td>
                        </tr>
                        <tr>
                            <td><?php echo htmlspecialchars($user['gender']); ?></td>
                        </tr>

                        <?php if (!empty($user['postcode']) && $user['postcode'] != '0'): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(strval($user['postcode'])); ?></td>
                            </tr>
                            <tr>
                                <td><?php echo htmlspecialchars($user['address']); ?></td>
                            </tr>
                        <?php endif; ?>

                        <?php if (!empty($user['postcode2']) && $user['postcode2'] != '0'): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(strval($user['postcode2'])); ?></td>
                            </tr>
                            <tr>
                                <td><?php echo htmlspecialchars($user['address2']); ?></td>
                            </tr>
                        <?php endif; ?>

                        <?php if (!empty($user['postcode3']) && $user['postcode3'] != '0'): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(strval($user['postcode3'])); ?></td>
                            </tr>
                            <tr>
                                <td><?php echo htmlspecialchars($user['address3']); ?></td>
                            </tr>
                        <?php endif; ?>

                        <tr>
                            <td><?php echo htmlspecialchars(formatPhoneNumber($user['phone'])); ?></td>
                        </tr>
                        <button onclick="window.location.href='normal_setting.php'" class="button-container">変更</button>
                    </table>
                </div>
                <div>
                    <h2>メールアドレス</h2>
                    <table>
                        <tr>
                            <td><?php echo htmlspecialchars($user['mail']); ?></td>
                        </tr>
                        <button onclick="window.location.href='mail_setting.php'" class="button-container">変更</button>
                    </table>
                </div>
                <div>
                    <h2>パスワード</h2>
                    <table>
                        <tr>
                            <td>********</td>
                        </tr>
                        <button onclick="window.location.href='pass_setting.php'" class="button-container">変更</button>
                    </table>
                </div>
                <div>
                    <div id="id-link">

                        <h2>ID連携</h2>
                        <table>
                            <tr>
                                <td>LINE</td>
                                <td>
                                    <?php echo $user['line_user_id'] ? '連携済み' : '未連携'; ?>
                                </td>
                                <td>
                                    <button onclick="handleLineUnlink()" class='button-container'>
                                        <?php echo $user['line_user_id'] ? '解除' : '連携'; ?>
                                    </button>
                                </td>
                            </tr>

                            <script>
                                function handleLineUnlink() {
                                    // 連携されていない場合はそのままリダイレクト
                                    <?php if (!$user['line_user_id']) { ?>
                                        window.location.href = 'line_link.php';
                                        return;
                                    <?php } ?>

                                    // 確認ダイアログを表示
                                    if (confirm("LINE連携を解除してもよろしいですか？")) {
                                        // OKを押した場合、連携解除のリクエストを送信
                                        fetch('unlink_line.php', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                },
                                                body: JSON.stringify({
                                                    action: 'unlink'
                                                }),
                                            })
                                            .then(response => response.json())
                                            .then(data => {
                                                if (data.success) {
                                                    alert("LINE連携を解除しました。");
                                                    location.reload(); // ページを再読み込み
                                                } else {
                                                    alert("連携解除に失敗しました。");
                                                }
                                            })
                                            .catch(error => {
                                                console.error("エラーが発生しました:", error);
                                                alert("エラーが発生しました。");
                                            });
                                    } else {
                                        // キャンセルを押した場合、何もしない
                                        console.log("連携解除をキャンセルしました。");
                                    }
                                }
                            </script>

                            <tr>
                                <td>Twitter</td>
                                <td>
                                    <?php echo $user['twitter_user_id'] ? '連携済み' : '未連携'; ?>
                                </td>
                                <td>
                                    <button onclick="window.location.href='<?php echo $user['twitter_user_id'] ? 'twitter_logout.php' : 'twitter_link.php'; ?>'" class='button-container'>
                                        <?php echo $user['twitter_user_id'] ? '解除' : '連携'; ?>
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div>
                    <div id="lang_link">
                        <h2>言語設定</h2>
                        <form method="post" action="account.php">
                            <table>
                                <tr>
                                    <td><?php echo $translations['language_select']; ?>:</td>
                                    <td>
                                        <label for="ja">
                                            <input type="radio" id="ja" name="lang" value="ja" <?php if ($lang == 'ja') echo 'checked'; ?>>
                                            日本語
                                        </label>
                                    </td>
                                    <td>
                                        <label for="en">
                                            <input type="radio" id="en" name="lang" value="en" <?php if ($lang == 'en') echo 'checked'; ?>>
                                            English
                                        </label>
                                    </td>
                                </tr>
                                <button type="submit" class="button-container"><?php echo $translations['change']; ?></button>
                            </table>
                        </form>
                    </div>
                </div>


            </div>
            <a href="toppage.php">戻る</a>
        </main>
</body>

</html>