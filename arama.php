<?php
ob_start();
include 'inc/main.php';

$searchResults = [];
$searchQuery = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['arama'])) {
    $searchQuery = trim($_POST['arama']);
    if (!empty($searchQuery)) {
        $stmt = $db->prepare("
            SELECT p.id, p.name, p.price, pi.image_path 
            FROM products p 
            LEFT JOIN product_images pi 
            ON p.id = pi.product_id AND pi.is_featured = 1 
            WHERE p.name LIKE :query
        ");
        $stmt->execute([':query' => '%' . $searchQuery . '%']);
        $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<main class="main vh-100">
    <div class="container-fluid h-100">
        <div class="row h-100 flex-column">
            <div class="col-12 mb-0">
                <header class="header row align-items-center">
                    <div class="col-auto pe-0">
                        <button class="btn btn-link btn-square menu-btn" type="button">
                            <i class="bi bi-list fs-4"></i>
                        </button>
                    </div>
                    <div class="col">
                        <div class="row align-items-center gx-2">
                            <div class="col-auto">
                                <h5 class="mb-0 mt-1">Arama Sonuçları</h5>
                                <p class="text-secondary small">QR MENU</p>
                            </div>
                        </div>
                    </div>
                </header>
            </div>

            <div class="col position-relative page-content" style="min-height: 620.547px; max-height: 620.547px;">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-10 col-lg-8 col-xxl-7 my-3">
                        <div class="row mb-4">
                            <div class="col">
                                <form action="arama.php" method="POST">
                                    <input type="text" name="arama" class="form-control form-control-lg form-control-rounded border-0 shadow-sm" placeholder="Yeniden Aramaya Ne Dersin?" value="<?= htmlspecialchars($searchQuery) ?>">
                                </form>
                            </div>
                        </div>

                        <h6 class="subtitle"><a href="#" class="float-right small"></a></h6>
                        <ul class="list-group list-group-flush mb-4">
                            <?php if (!empty($searchResults)): ?>
                                <?php foreach ($searchResults as $product): ?>
                                    <li class="list-group-item">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <figure class="avatar avatar-80 text-center mb-0">
                                                    <img src="uploads/<?= htmlspecialchars($product['image_path'] ?? 'default.jpg') ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="mh-100">
                                                </figure>
                                            </div>
                                            <div class="col">
                                                <div class="row mb-1">
                                                    <div class="col">
                                                        <a href="urunView.php?id=<?= $product['id'] ?>" class="text-normal d-block">
                                                            <h6 class="text-color-theme"><?= htmlspecialchars($product['name']) ?></h6>
                                                        </a>
                                                    </div>
                                                    <div class="col-auto text-end">
                                                        <p class="text-green"><?= htmlspecialchars($product['price']) ?> TL</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p style="text-align: center;" class="text-muted">Sonuç bulunamadı.</p>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const loader = document.querySelector('.loader-wrap');
        if (loader) {
            loader.style.display = 'none';
        }
    });
</script>
<?php ob_end_flush(); ?>
