<?php
ob_start();
include 'inc/main.php';
include 'inc/guvenlik.php';

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

    <!-- Begin page content -->
    <div class="container-fluid h-100">
        <div class="row h-100 flex-column">

            <div class="col-12 mb-0">
                <!-- header -->
                <header class="header row align-items-center">

                    <!-- search -->
                    <div class="search-wrapper">
                        <div class="row gx-2">
                            <div class="col">
                                <input type="text" id="searchInput" class="form-control form-control-rounded border" placeholder="Bunu mu arıyorsun?">
                            </div>
                            <div class="col-auto">
                                <button class="btn btn btn-square btn-link rounded-circle search-btn" type="button">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>
                        <div id="searchResults" class="mt-2"></div>
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

                        <!-- search filter  -->
                        <div class="row mb-4">
                            <div class="col">
                                <form action="arama.php" method="POST">
                                    <input type="text" name="arama" class="form-control form-control-lg form-control-rounded border-0 shadow-sm" id="search" placeholder="Bunu mu arıyorsun?" value="<?= htmlspecialchars($searchQuery) ?>">
                                </form>
                            </div>

                        </div>
                        <!-- search filter ends-->

                        <!-- search results -->
                        <?php if (!empty($searchResults)): ?>
                            <div class="row">
                                <?php foreach ($searchResults as $product): ?>
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
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                            <p class="text-muted">Sonuç bulunamadı.</p>
                        <?php endif; ?>
                        <!-- search results ends -->

                        <div class="row mb-3">
                            <div class="col">
                                <h5>Popüler Kategoriler</h5>
                            </div>

                        </div>
                        <!-- categories -->
                        <div class="row mb-3">
                            <div class="col-12 px-0">
                                <div class="swiper categories">
                                    <div class="swiper-wrapper pb-2">
                                        <?php
                                        $categories = $db->query("SELECT c.id, c.name, ci.image_path FROM categories c LEFT JOIN category_images ci ON c.id = ci.category_id AND ci.is_featured = 1")->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($categories as $category): ?>
                                        <div class="swiper-slide w-auto ps-3">
                                            <div class="card border-0 text-center">
                                                <div class="card-body">
                                                    <figure class="coverimg avatar avatar-110 mb-1">
                                                        <img src="../uploads/<?= $category['image_path'] ?>" alt="<?= $category['name'] ?>" class="mw-100">
                                                    </figure>
                                                    <a href="kategoriView.php?id=<?= $category['id'] ?>" class="text-normal d-block mb-1">
                                                        <h6 class="text-color-theme"><?= $category['name'] ?></h6>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- categories ends -->

                        <!-- offer banner -->
                        <div class="card border-0 position-relative overflow-hidden mb-4">
                            <figure class="coverimg position-absolute w-100 h-100 start-0 top-0 m-0">
                                <img src="https://atisbutikrestaurant.com.tr/wp-content/uploads/2021/06/2021-atis-serpme-kahvalti-1.jpg" class="mw-100" alt="">
                            </figure>
                            <div class="card-body bg-none">
                                <div class="row">
                                    <div class="col-5">
                                        <div class="bg-radial-gradient text-white text-center p-2 rounded">
                                            <h3>Serpme Kahvaltı</h3>

                                            <p class="text-muted small">
                                                <p>Haftasonları Sadece 99.00₺&nbsp;</p>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- offer banner ends -->

                        <!-- color choices -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <ul class="personalise-color-list mb-1">
                                    <li class="d-inline w-auto p-1" data-title="theme-blue">
                                        <div class="avatar avatar-24 rounded-circle bg-blue"></div>
                                    </li>
                                    <li class="d-inline w-auto p-1" data-title="theme-indigo">
                                        <div class="avatar avatar-24 rounded-circle bg-indigo"></div>
                                    </li>
                                    <li class="d-inline w-auto p-1" data-title="theme-purple">
                                        <div class="avatar avatar-24 rounded-circle bg-purple"></div>
                                    </li>
                                    <li class="d-inline w-auto p-1" data-title="theme-pink">
                                        <div class="avatar avatar-24 rounded-circle bg-pink"></div>
                                    </li>
                                    <li class="d-inline w-auto p-1" data-title="theme-red">
                                        <div class="avatar avatar-24 rounded-circle bg-red"></div>
                                    </li>
                                    <li class="d-inline w-auto p-1" data-title="theme-orange">
                                        <div class="avatar avatar-24 rounded-circle bg-orange"></div>
                                    </li>
                                    <li class="d-inline w-auto p-1" data-title="theme-yellow">
                                        <div class="avatar avatar-24 rounded-circle bg-yellow"></div>
                                    </li>
                                    <li class="d-inline w-auto p-1" data-title="theme-green">
                                        <div class="avatar avatar-24 rounded-circle bg-green"></div>
                                    </li>
                                    <li class="d-inline w-auto p-1" data-title="theme-teal">
                                        <div class="avatar avatar-24 rounded-circle bg-teal"></div>
                                    </li>
                                    <li class="d-inline w-auto p-1" data-title="theme-cyan">
                                        <div class="avatar avatar-24 rounded-circle bg-cyan"></div>
                                    </li>
                                </ul>
                                <div class="row justify-content-center align-items-center no-rtl d-none d-md-flex">
                                    <div class="col-auto text-theme">Light</div>
                                    <div class="col-auto px-0 ps-1">
                                        <div class="form-check form-switch mt-1">
                                            <input class="form-check-input" type="checkbox" id="btn-layout-modes-dark">
                                            <label class="form-check-label" for="btn-layout-modes-dark"></label>
                                        </div>
                                    </div>
                                    <div class="col-auto text-theme">Dark</div>
                                </div>
                            </div>
                        </div>
                        <!-- color choices ends -->

                        <!-- Popular Products -->
                        <div class="row mb-3">
                            <div class="col">
                                <h5>En Çok Tercih Edilenler</h5>
                            </div>

                        </div>
                        <div class="row">
                            <?php
                            $products = $db->query("
                                SELECT p.id, p.name, p.price, pi.image_path 
                                FROM products p 
                                LEFT JOIN product_images pi 
                                ON p.id = pi.product_id AND pi.is_featured = 1
                                ORDER BY RAND() -- Ürünleri rastgele sırala
                                LIMIT 8
                            ")->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($products as $index => $product): ?>
                                <div class="col-6 col-md-3 mb-3">
                                    <div class="card border-0 mb-4">
                                        <div class="card-body position-relative">
                                            <figure class="h-120 text-center"> <!-- Fotoğraf yüksekliği artırıldı -->
                                                <img src="../uploads/<?= $product['image_path'] ?>" alt="<?= $product['name'] ?>" class="mh-100 mw-100 rounded">
                                            </figure>
                                            <a href="urunView.php?id=<?= $product['id'] ?>" class="text-normal d-block mb-1">
                                                <h6 class="text-color-theme"><?= $product['name'] ?></h6>
                                            </a>
                                            <p class="text-green"><?= number_format($product['price'], 2) ?> TL</p>
                                        </div>
                                    </div>
                                </div>
                                <?php if (($index + 1) % 4 === 0): ?>
                                    <div class="w-100"></div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                        <!-- Popular Products ends -->

                    </div>
                </div>
            </div>
            
<?php include 'inc/footer.php'; ?>

        </div>
    </div>
    <!-- page content ends -->
</main>

<?php ob_end_flush(); ?>

