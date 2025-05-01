<?php
ob_start();
require_once 'inc/main.php'; 

$categoryId = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$categoryId) {
    $errorMessage = "Geçersiz kategori ID.";
} else {
    $category = $db->prepare("SELECT * FROM categories WHERE id = :id");
    $category->execute(['id' => $categoryId]);
    $category = $category->fetch(PDO::FETCH_ASSOC);

    if (!$category) {
        $errorMessage = "Kategori bulunamadı.";
    } else {
        $products = $db->prepare("SELECT p.*, pi.image_path 
                                  FROM products p 
                                  LEFT JOIN product_images pi 
                                  ON p.id = pi.product_id AND pi.is_featured = 1 
                                  WHERE p.category_id = :id");
        $products->execute(['id' => $categoryId]);
        $products = $products->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<main class="main vh-100">
    <div class="container-fluid h-100">
        <div class="row h-100 flex-column">
            <div class="col-12 mb-0">
                <!-- header -->
                <header class="header row align-items-center">
                    <!-- search -->
                    <div class="search-wrapper">
                        <div class="row gx-2">
                            <div class="col">
                                <input type="text" class="form-control form-control-rounded border"
                                    placeholder="What are you looking for...">
                            </div>
                            <div class="col-auto">
                                <button class="btn btn btn-square btn-link rounded-circle search-btn" type="button">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- search ends -->

                    <div class="col-auto pe-0">
                        <button class="btn btn-link btn-square menu-btn" type="button">
                            <i class="bi bi-list fs-4"></i>
                        </button>
                    </div>
                    <div class="col">
                        <div class="row align-items-center gx-2">
                            <div class="col-auto">
                                <h5 class="mb-0 mt-1">Restoran QR Menü</h5>
                                <p class="text-secondary small">QR MENU</p>
                            </div>
                        </div>
                    </div>

                </header>
                <!-- header ends -->
            </div>

            <div class="col position-relative page-content">
                <!-- content page -->
                <div class="row justify-content-center">

                    <div class="col-12 col-md-10 col-lg-8 col-xxl-7 my-3">

                        <?php if (isset($errorMessage)): ?>
                            <div class="text-center py-5">
                                <h2 class="text-danger">Kategori Bulunamadı</h2>
                                <p class="text-muted">Aradığınız kategori mevcut değil veya kaldırılmış olabilir.</p>
                                <a href="/" class="btn btn-primary mt-3">Anasayfaya Dön</a>
                            </div>
                        <?php else: ?>
                            <h6 class="subtitle"><?= htmlspecialchars($category['name']) ?><a href="#" class="float-right small"></a></h6>

                            <div class="row">
                                <?php foreach ($products as $product): ?>
                                <div class="col-6 col-md-4 col-lg-3 mb-3">
                                    <div class="card border-0 mb-4">
                                        <div class="card-body position-relative">
                                            <figure class="h-90 text-center">
                                                <img src="uploads/<?= htmlspecialchars($product['image_path'] ?? 'default.jpg') ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="mh-100">
                                            </figure>

                                            <a href="urunView.php?id=<?= $product['id'] ?>" class="text-normal d-block mb-1">
                                                <h6 class="text-color-theme"><?= htmlspecialchars($product['name']) ?></h6>
                                            </a>
                                            <p class="text-green"><?= htmlspecialchars($product['price']) ?> TL</p>
                                            <div class="mbottom-35 position-relative">
                                                <div class="row gx-2">
                                                    <div class="col-auto ms-auto">
                                                        <a href="urunView.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-square-sm btn-white rounded-circle">
                                                            <i class="bi bi-arrow-right-circle"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

            <?php include 'inc/footer.php'; ?>

        </div>
    </div>
</main>
<?php ob_end_flush(); ?>
