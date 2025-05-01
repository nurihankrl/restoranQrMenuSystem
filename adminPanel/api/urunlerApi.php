<?php
require_once '../../inc/db.php';

define('API_KEY', 'Q492ASDFQWE1234');

// POST isteği kontrolü
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apiKey']) && $_POST['apiKey'] === API_KEY) {
    if (isset($_POST['productId'])) {
        $productId = intval($_POST['productId']);

        // Veritabanından ürün resimlerini çek
        $stmt = $db->prepare("SELECT id, image_path, is_featured FROM product_images WHERE product_id = :product_id");
        $stmt->execute([':product_id' => $productId]);
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // JSON formatında döndür
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'images' => $images]);
    } else {
        // Hatalı istek
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Ürün ID eksik.']);
    }
} else {
    // Yetkisiz erişim
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Yetkisiz erişim.']);
}
?>
