<?php
require_once '../../inc/db.php';

define('API_KEY', 'Q492ASDFQWE1234');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apiKey']) && $_POST['apiKey'] === API_KEY) {
    if (isset($_POST['productId'])) {
        $productId = intval($_POST['productId']);

        $stmt = $db->prepare("SELECT id, image_path, is_featured FROM product_images WHERE product_id = :product_id");
        $stmt->execute([':product_id' => $productId]);
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'images' => $images]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Ürün ID eksik.']);
    }
} else {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Yetkisiz erişim.']);
}
?>
