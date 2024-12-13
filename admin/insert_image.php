<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['subthumbnail'])) {
    $shop_id = $_POST['shop_id'];
    $files = $_FILES['subthumbnail'];
    $uploadSuccess = true;
    $errorMessages = [];

    foreach ($files['tmp_name'] as $key => $tmpName) {
        if (is_uploaded_file($tmpName)) {
            $imgData = file_get_contents($tmpName);
            $base64Img = base64_encode($imgData); // Base64エンコード

            // デバッグ用にエンコードされた画像データの一部を出力
            $errorMessages[] = "Base64 encoded image $key: " . substr($base64Img, 0, 50) . "...";

            $stmt = $dbh->prepare("INSERT INTO image (shop_id, img) VALUES (:shop_id, :img)");
            $stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
            $stmt->bindParam(':img', $imgData, PDO::PARAM_LOB);

            if ($stmt->execute()) {
                $errorMessages[] = "Image $key uploaded and saved successfully.";
            } else {
                $errorMessages[] = "Failed to save image $key.";
                $uploadSuccess = false;
            }
        } else {
            $errorMessages[] = "File $key failed to upload.";
            $uploadSuccess = false;
        }
    }

    if ($uploadSuccess) {
        echo json_encode(['success' => true, 'messages' => $errorMessages]);
    } else {
        echo json_encode(['success' => false, 'messages' => $errorMessages]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request or no files uploaded.']);
}
?>