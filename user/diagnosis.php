<?php
include "../../db_open.php";
include "../head.php";
include "../header.php";
echo "<link rel='stylesheet' href='./header.css'>";
?>
<link rel="stylesheet" href="diagnosis.css">
<body>
    <div class="container">
    <h1><?php echo $translations['Let\'s do a fashion diagnosis!'] ?></h1>
    <div id="question-container">
            <p id="question">質問が表示されます</p>
            <button id="option1" class="option-btn">選択肢1</button>
            <button id="option2" class="option-btn">選択肢2</button>
        </div>
        <div id="result" class="hidden">
            <h2>診断結果</h2>
            <p id="final-result">診断結果がここに表示されます</p>
        </div>
    </div>
    <script src="diagnosis.js"></script>
</body>
</html>
