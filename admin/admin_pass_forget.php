<!DOCTYPE html>
<html lang="ja">

  <link rel="stylesheet" href="admin_pass_forget.css">
<body>
   
    <div class="reset_password">
        <h2>パスワード再登録</h2>
        <form action="reset_check.php" method="post">
            <label for="checksei">姓</label>
            <input type="text" id="checksei" name="checksei"  maxlength="10" placeholder="吉田"  required>
            <label for="checkmei">名</label>
            <input type="text" id="checkmei" name="checkmei"  maxlength="10" placeholder="太郎"  required>


            <label for="checktel">電話番号</label>
            <input type="text" id="checktel" name="checktel"  maxlength="15" placeholder="012-3456-7890"  required>

            <label for="check_email">メールアドレス</label>
            <input type="email" id="check_email" name="check_email"  maxlength="50" placeholder="yoshidajobi@example.com"  required>

           

            <button type="submit">パスワードリセットへ</button>
        </form>
    </div>
</body>
</html>
