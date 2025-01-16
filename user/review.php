<?php
include "../../db_open.php";
include "../header.php";
include "../head.php";
echo "<link rel='stylesheet' href='header.css'>";
echo "<link rel='stylesheet' href='review.css'>";

// ユーザーIDと注文IDを取得
// $order_id = $_GET['order_id'] ?? null;
$shop_id = $_GET['shop_id'] ?? null;
// var_dump($shop_id);
if (isset($_SESSION['id'])) {
    $user_id = $_SESSION['id'];
} else {
    $user_id = null;
}
// レビューが投稿された場合
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 入力されたデータを取得
    $review_content = $_POST['review_content'];
    $rate = $_POST['rate']; // ☆の評価（1-5）
    $shop_id = $_POST['shop_id'];
    // 入力チェック（簡易的なもの）
    if (empty($review_content) || $rate < 1 || $rate > 5) {
        echo "<p>レビュー内容と評価は必須です。</p>";
    } else {
        // レビューをデータベースに保存
        $stmt = $dbh->prepare("INSERT INTO reviews (user_id, review_content, rate, shop_id, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
        $stmt->bindParam(1, $user_id, PDO::PARAM_INT);
        $stmt->bindParam(2, $review_content, PDO::PARAM_STR);
        $stmt->bindParam(3, $rate, PDO::PARAM_INT);
        $stmt->bindParam(4, $shop_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo "<p>レビューが投稿されました！</p>";
        } else {
            echo "<p>レビューの投稿に失敗しました。再試行してください。</p>";
        }
    }
}

// 既存のレビューを表示（注文に対するレビューがあれば）
$stmt = $dbh->prepare("SELECT * FROM reviews WHERE shop_id = ? AND user_id = ?");
$stmt->bindParam(1, $shop_id, PDO::PARAM_INT);
$stmt->bindParam(2, $user_id, PDO::PARAM_INT);
$stmt->execute();
$review = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>

<body>
    <h1>レビューを投稿</h1>
    <div class="your_review">
        <?php if ($review): ?>
            <h2>あなたのレビュー</h2>
            <p><strong>評価:</strong> <?php echo str_repeat("☆", $review['rate']); ?></p>
            <p><strong>レビュー内容:</strong> <?php echo nl2br(htmlspecialchars($review['review_content'])); ?></p>
        <?php else: ?>
            <form action="review.php" method="POST">

                <input type="hidden" name="shop_id" value="<?php echo $shop_id; ?>" />

                <label for="rate">評価 (☆5つで最高評価):</label>
                <div class="star-rating">
                    <span class="star" data-value="1">&#9733;</span>
                    <span class="star" data-value="2">&#9733;</span>
                    <span class="star" data-value="3">&#9733;</span>
                    <span class="star" data-value="4">&#9733;</span>
                    <span class="star" data-value="5">&#9733;</span>
                </div>
                <input type="hidden" id="star-rating-value" name="rate" value="0" />

                <br><br>

                <label for="review_content">レビュー内容:</label>
                <textarea name="review_content" id="review_content" rows="5" cols="40" required></textarea>
                <br><br>

                <button type="submit">レビューを投稿</button>
            </form>
        <?php endif; ?>

        <a href="order.php">購入履歴に戻る</a>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stars = document.querySelectorAll('.star');
            const ratingInput = document.getElementById('star-rating-value');

            // 星にカーソルを合わせたとき
            stars.forEach(star => {
                star.addEventListener('mouseover', function() {
                    const value = this.getAttribute('data-value');
                    updateStars(value);
                });

                // 星からカーソルを離したとき
                star.addEventListener('mouseout', function() {
                    const currentRating = ratingInput.value;
                    updateStars(currentRating);
                });

                // 星をクリックしたとき（評価を確定）
                star.addEventListener('click', function() {
                    const value = this.getAttribute('data-value');
                    ratingInput.value = value;
                    updateStars(value);
                });
            });

            function updateStars(rating) {
                stars.forEach(star => {
                    const value = star.getAttribute('data-value');
                    if (value <= rating) {
                        star.classList.add('selected');
                    } else {
                        star.classList.remove('selected');
                    }
                });
            }
        });
    </script>
</body>

</html>