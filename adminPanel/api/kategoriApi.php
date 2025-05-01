<?php
require_once '../../inc/db.php';

define('API_KEY', 'Q492ASDFQWE1234');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apiKey']) && $_POST['apiKey'] === API_KEY) {
    if (isset($_POST['categoryId'])) {
        $categoryId = intval($_POST['categoryId']);

        $stmt = $db->prepare("SELECT id, image_path, is_featured FROM category_images WHERE category_id = :category_id");
        $stmt->execute([':category_id' => $categoryId]);
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'images' => $images]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Kategori ID eksik.']);
    }
} else {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Yetkisiz erişim.']);
}
?>
