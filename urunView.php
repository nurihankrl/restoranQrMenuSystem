<?php
ob_start();
require_once 'inc/main.php'; 


$productId = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$productId) {
    $errorMessage = "Geçersiz ürün ID.";
} else {
    $product = $db->prepare("SELECT * FROM products WHERE id = :id");
    $product->execute(['id' => $productId]);
    $product = $product->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        $errorMessage = "Ürün bulunamadı.";
    } else {
        $images = $db->prepare("SELECT image_path FROM product_images WHERE product_id = :id");
        $images->execute(['id' => $productId]);
        $images = $images->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<main class="main vh-100">
    <div class="container-fluid h-100">
        <div class="row h-100 flex-column">
            <div class="col-12 mb-0">
                <!-- header -->
                <header class="header row align-items-center">
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
                                <h2 class="text-danger">Ürün Bulunamadı</h2>
                                <p class="text-muted">Aradığınız ürün mevcut değil veya kaldırılmış olabilir.</p>
                                <a href="/" class="btn btn-primary mt-3">Anasayfaya Dön</a>
                            </div>
                        <?php else: ?>
                            <!-- product -->
                            <div class="card border-0 position-relative z-index-1">
                                <div class="card-body">

                                    <!-- Swiper -->
                                    <div class="swiper imageswiper">
                                        <div class="swiper-wrapper pb-5">
                                            <?php foreach ($images as $image): ?>
                                            <div class="swiper-slide">
                                                <figure class="h-250 text-center">
                                                    <img src="uploads/<?= htmlspecialchars($image['image_path']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="mh-100 rounded-image">
                                                </figure>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <!-- Swiper navigation buttons -->
                                        <div class="swiper-button-next"></div>
                                        <div class="swiper-button-prev"></div>
                                        <div class="swiper-pagination"></div>
                                    </div>

                                    <a class="text-normal d-block mb-2">
                                        <h5 class="text-color-theme"><?= htmlspecialchars($product['name']) ?></h5>
                                    </a>
                                    <p class="position-relative text-secondary">
                                        <?= htmlspecialchars($product['description']) ?>
                                    </p>
                                    <h4 class="text-green mb-3"><?= htmlspecialchars($product['price']) ?> TL</h4>
                                </div>
                            </div>
                            <!-- product listing ends -->
                        <?php endif; ?>

                    </div>
                </div>
            </div>

            <?php include 'inc/footer.php'; ?>

        </div>
    </div>
</main>

<!-- Swiper.js -->
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script>
    const swiper = new Swiper('.imageswiper', {
        loop: true,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
    });
</script>

<!-- Custom CSS for Swiper Navigation and Images -->
<style>
    .swiper-button-next,
    .swiper-button-prev {
        color: #ff5733; /* Daha estetik bir renk */
        font-size: 20px; /* Daha küçük boyut */
        width: 30px;
        height: 30px;
    }

    .swiper-button-next:hover,
    .swiper-button-prev:hover {
        color: #ff7849; /* Hover rengi */
    }

    .rounded-image {
        border-radius: 15px; /* Yuvarlak köşeler */
    }
</style>
<?php ob_end_flush(); ?>