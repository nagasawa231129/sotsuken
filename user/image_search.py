import sys
import json
import cv2
import numpy as np

def extract_histogram(image_path):
    # 画像を読み込む
    img = cv2.imread(image_path)
    if img is None:
        return {}

    # グレースケールに変換
    gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
    
    # ヒストグラムを計算
    hist = cv2.calcHist([gray], [0], None, [256], [0, 256])
    hist = hist.flatten()  # 1D配列に変換
    return hist.tolist()  # JSONで返せる形式にする

if __name__ == "__main__":
    if len(sys.argv) != 2:
        print(json.dumps({"error": "画像ファイルが指定されていません"}))
        sys.exit(1)

    image_path = sys.argv[1]  # PHPから渡されたファイルパス
    histogram = extract_histogram(image_path)
    print(json.dumps(histogram))
