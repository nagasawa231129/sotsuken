document.addEventListener("DOMContentLoaded", function() {
    let currentQuestion = 0;
    const answers = {
        faceType: null,
        colorType: null,
        bodyType: null,
    };

    const questions = [
        //顔タイプ
        {
            question: "顔の形は？",
            options: ["丸顔・横長のベース型", "卵型か面長、縦長のベース型"],
            key: "faceType"
        },
        {
            question: "顔全体の立体感は？",
            options: ["顔が平面的", "顔が立体的"],
            key: "faceType"
        },
        {
            question: "顔全体の形状は？",
            options: ["顔に骨を感じる", "顔に骨を感じない"],
            key: "faceType"
        },
        {
            question: "眉毛の印象は？",
            options: ["眉が薄い", "眉が濃い"],
            key: "faceType"
        },
        {
            question: "眉毛の角度は？",
            options: ["上がり眉", "下がり眉or並行眉"],
            key: "faceType"
        },
        {
            question: "目の位置は？",
            options: ["目が寄っている", "目が離れ気味"],
            key: "faceType"
        },
        {
            question: "目の印象は？",
            options: ["目が眉と離れている", "目と眉が近い"],
            key: "faceType"
        },
        {
            question: "目の形は？",
            options: ["目が切れ長である", "目が丸く縦幅がある"],
            key: "faceType"
        },
        {
            question: "目の角度は？",
            options: ["目がたれ目である", "目がつり目である"],
            key: "faceType"
        },
        //パーソナルカラー
        {
            question: "あなたの地毛の色に近い方は？",
            options: ["茶色っぽい", "黒っぽい"],
            key: "colorType"
        },
        {
            question: "髪質は？",
            options: ["しっとり艶がある", "柔らかくさらさらしている"],
            key: "colorType"
        },
        {
            question: "肌の色に近い方は？",
            options: ["色白～普通", "やや褐色"],
            key: "colorType"
        },
        {
            question: "血色がいいと言われる？",
            options: ["はい", "いいえ"],
            key: "colorType"
        },
        {
            question: "瞳の色は？",
            options: ["黒っぽい", "茶色っぽい"],
            key: "colorType"
        },
        {
            question: "白目と黒目のコントラストは？",
            options: ["はっきりしている", "ソフトな印象"],
            key: "colorType"
        },
        {
            question: "掌の色は？",
            options: ["黄色味/オレンジが強い", "赤色味/ピンクが強い"],
            key: "colorType"
        },
        {
            question: "どっちの色がしっくりくる？",
            options: ["青", "グレー"],
            key: "colorType"
        },
        {
            question: "似合うと思うアクセサリーの色は？",
            options: ["シルバー", "ゴールド"],
            key: "colorType"
        },
        //骨格
        {
            question: "首が長いと言われたことがある",
            options: ["ある", "ない"],
            key: "bodyType"
        },
        {
            question: "首の筋が目立つほうだと思う",
            options: ["思う", "思わない"],
            key: "bodyType"
        },
        {
            question: "鎖骨の中心は目立つ",
            options: ["目立つ", "目立たない"],
            key: "bodyType"
        },
        {
            question: "胸に厚みがある",
            options: ["ある", "ない"],
            key: "bodyType"
        },
        {
            question: "手足の大きさは小さいほう",
            options: ["小さめ", "大きめ"],
            key: "bodyType"
        },
        {
            question: "手首は平べったく丸みがある",
            options: ["ある", "ない"],
            key: "bodyType"
        },
        {
            question: "指や手首など間接が目立つ",
            options: ["目立つ", "目立たない"],
            key: "bodyType"
        },
        {
            question: "上半身より下半身が太りやすい",
            options: ["はい", "いいえ"],
            key: "bodyType"
        },
        {
            question: "膝を触った時にしっかり山がある",
            options: ["ある", "ない"],
            key: "bodyType"
        },
        {
            question: "ふくらはぎに筋肉や脂肪がつきにくい",
            options: ["はい", "いいえ"],
            key: "bodyType"
        },
    ];

    const questionElement = document.getElementById("question");
    const resultContainer = document.getElementById("result");
    const finalResult = document.getElementById("final-result");
    const option1 = document.getElementById("option1");
    const option2 = document.getElementById("option2");

    function showQuestion(index) {
        if (index < questions.length) {
            const current = questions[index];
            questionElement.textContent = current.question;
            option1.textContent = current.options[0];
            option2.textContent = current.options[1];

            option1.onclick = () => handleAnswer(0, current.key);
            option2.onclick = () => handleAnswer(1, current.key);
        } else {
            showResult();
        }
    }

    function handleAnswer(selectedOption, key) {
        answers[key] = selectedOption;
        currentQuestion++;
        showQuestion(currentQuestion);
    }

    function showResult() {
        resultContainer.classList.remove("hidden");
        const fashionResult = getFashionResult();  // ここで結果を取得
    const colorTypeResult = getColorTypeResult();
    const bodyTypeResult = getBodyTypeResult();

    // 結果を表示
    finalResult.innerHTML = `
        <p><strong>ファッションタイプ:</strong> ${fashionResult}</p>
        <p><strong>カラーパターン:</strong> ${colorTypeResult}</p>
        <p><strong>骨格タイプ:</strong> ${bodyTypeResult}</p>
    `;

    const returnToTopButton = document.createElement("button");
    returnToTopButton.textContent = "トップページへ戻る";
    returnToTopButton.classList.add("return-btn");

    // ボタンクリック時にトップページへ遷移
    returnToTopButton.onclick = function() {
        window.location.href = './toppage.php'; // トップページのURLを指定
    };

    // 結果の下にボタンを追加
    resultContainer.appendChild(returnToTopButton);
    // 質問セクションを非表示に
    document.getElementById("question-container").classList.add("hidden");
}

    function getFashionResult() {
        const faceCounts = { soft: 0, hard: 0 }; // スコアカウント用
        const softQuestions = [0, 1, 7]; // ソフトに関連する質問インデックス
        const hardQuestions = [2, 4, 8]; // ハードに関連する質問インデックス
    
        // カウントの更新
        questions.forEach((question, index) => {
            if (softQuestions.includes(index) && answers.faceType === 0) {
                faceCounts.soft++;
            }
            if (hardQuestions.includes(index) && answers.faceType === 1) {
                faceCounts.hard++;
            }
        });
    
        // フェイスタイプ判定
        if (faceCounts.soft >= 6) {
            return "フレッシュ（ソフト）";
        } else if (faceCounts.hard >= 6) {
            return "エレガント（ハード）";
        } else if (faceCounts.soft >= 5 && faceCounts.hard >= 4) {
            return "フレッシュ（ハード）";
        } else if (faceCounts.soft >= 4 && faceCounts.hard >= 5) {
            return "チャーミング（ハード）";
        } else {
            return "クール（ソフト）";
        }
    }

    function getColorTypeResult() {
        const colorTypeCounts = { left: 0, right: 0 }; // 回答の左右のカウント
        const totalQuestions = questions.filter(q => q.key === "colorType").length;
    
        // カラー質問に基づきカウント
        questions.forEach((question, index) => {
            if (question.key === "colorType") {
                if (answers.colorType === 0) {
                    colorTypeCounts.left++;
                } else {
                    colorTypeCounts.right++;
                }
            }
        });
    
        // カラータイプ判定条件
        if (colorTypeCounts.left === 9 && colorTypeCounts.right === 0) {
            return "ブルベ夏: 明るく澄んだ色が似合います！";
        } else if (colorTypeCounts.left === 0 && colorTypeCounts.right === 9) {
            return "イエベ秋: 暖かく深い色味がピッタリです。";
        } else if (colorTypeCounts.left === 5 && colorTypeCounts.right === 4) {
            return "ブルベ夏: 柔らかく優しい印象の色が得意です。";
        } else if (colorTypeCounts.left === 4 && colorTypeCounts.right === 5) {
            return "イエベ春: 明るく軽やかな色味がおすすめです。";
        } else if (colorTypeCounts.left === 6 && colorTypeCounts.right === 3) {
            return "ブルベ夏: 鮮やかで涼やかな色が映えます。";
        } else if (colorTypeCounts.left === 3 && colorTypeCounts.right === 6) {
            return "イエベ春: 優しく温かい色調が似合います！";
        } else if (colorTypeCounts.left === 1 && colorTypeCounts.right === 8) {
            return "イエベ秋: リッチで濃い色味がお似合いです。";
        } else if (colorTypeCounts.left === 8 && colorTypeCounts.right === 1) {
            return "ブルベ夏: 繊細で涼しげな印象を持つ色が得意です。";
        } else if (colorTypeCounts.left > colorTypeCounts.right) {
            return "ブルベ夏: 爽やかで透明感のある色が似合います！";
        } else if (colorTypeCounts.right > colorTypeCounts.left) {
            return "イエベ秋: 暖かみのある深い色が得意です！";
        } else if (answers.colorType[0] === 0 && answers.colorType[totalQuestions - 1] === 0) {
            return "ブルベ冬: クールで鮮やかな色味が似合います！";
        } else {
            return "多彩な色が似合う柔軟な魅力をお持ちです！";
        }
    }

    function getBodyTypeResult() {
        const bodyTypeCounts = { left: 0, right: 0 }; // 回答の左右のカウント
        const totalQuestions = questions.filter(q => q.key === "bodyType").length;
    
        // 骨格質問に基づきカウント
        questions.forEach((question, index) => {
            if (question.key === "bodyType") {
                if (answers.bodyType[index] === 0) {
                    bodyTypeCounts.left++;
                } else {
                    bodyTypeCounts.right++;
                }
            }
        });
    
        // 骨格タイプ判定条件
        if (bodyTypeCounts.left === 10 && bodyTypeCounts.right === 0) {
            return "ナチュラル: 骨感やフレーム感が特徴です！";
        } else if (bodyTypeCounts.left === 0 && bodyTypeCounts.right === 10) {
            return "ウェーブ: 柔らかく曲線的なラインが特徴です！";
        } else if (bodyTypeCounts.left === 5 && bodyTypeCounts.right === 5) {
            return "ナチュラル: バランスの取れた骨格タイプです！";
        } else if (bodyTypeCounts.left === 4 && bodyTypeCounts.right === 6) {
            return "ナチュラル: 少し骨感のある印象です。";
        } else if (bodyTypeCounts.left === 6 && bodyTypeCounts.right === 4) {
            return "ストレート: 立体感と筋肉感が特徴的です！";
        } else if (bodyTypeCounts.left === 3 && bodyTypeCounts.right === 7) {
            return "ナチュラル: 柔軟なスタイルが似合います。";
        } else if (bodyTypeCounts.left === 7 && bodyTypeCounts.right === 3) {
            return "ナチュラル: フレームを生かしたファッションがおすすめ！";
        } else if (bodyTypeCounts.left === 1 && bodyTypeCounts.right === 9) {
            return "ウェーブ: 女性らしいしなやかさが特徴です。";
        } else if (bodyTypeCounts.left === 9 && bodyTypeCounts.right === 1) {
            return "ナチュラル: 骨格がしっかりした印象です！";
        } else if (bodyTypeCounts.left > bodyTypeCounts.right) {
            return "ウェーブ: 柔らかなラインが得意です！";
        } else if (bodyTypeCounts.right > bodyTypeCounts.left) {
            return "ストレート: しっかりした立体感を生かしましょう！";
        } else if (answers.bodyType[0] === 0 && answers.bodyType[totalQuestions - 1] === 0) {
            return "ウェーブ: 曲線美が特徴の骨格タイプです！";
        } else {
            return "個性的な骨格でさまざまなスタイルが似合います！";
        }
    }
    showQuestion(currentQuestion);
});