<?php
require_once 'inc/guvenlik.php';
require_once 'inc/db.php';

if (!isset($_COOKIE['goMobileuxlayoutmode'])) {
    setcookie('goMobileuxlayoutmode', 'dark-mode', time() + (86400 * 30), "/");
}

if (!isset($_COOKIE['goMobileuxtheme'])) {
    setcookie('goMobileuxtheme', 'theme-pink', time() + (86400 * 30), "/");
}

$defaultTitle = $db->query("SELECT site_title, site_logo FROM settings LIMIT 1")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="tr" class="dark-mode">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?= htmlspecialchars($defaultTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="description" content="Restoran QR Menü">
    <meta name="keywords" content="">
    <meta name="author" content="Restoran QR Menü">

    <!-- Favicons -->
    <link rel="icon" href="uploads/<?= htmlspecialchars($db->query("SELECT site_logo FROM settings LIMIT 1")->fetchColumn(), ENT_QUOTES, 'UTF-8') ?>" sizes="32x32" type="image/png">

    <!-- Google fonts-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">

    <!-- bootstrap icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

    <!-- chosen css -->
    <link rel="stylesheet" href="assets/vendor/chosen_v1.8.7/chosen.min.css">

    <!-- date range picker -->
    <link rel="stylesheet" href="assets/vendor/daterangepicker/daterangepicker.css">

    <!-- no ui slider -->
    <link rel="stylesheet" href="assets/vendor/nouislider/nouislider.min.css">

    <!-- swiper carousel css -->
    <link rel="stylesheet" href="assets/vendor/swiper/swiper-bundle.min.css">

    <!-- style css for this template -->
    <link href="assets/scss/style.css" rel="stylesheet">
</head>

<body class="d-flex flex-column h-100 sidebar-pushcontent theme-pink" data-theme="theme-pink">

    <img src="assets/img/banner.jpg" alt="" class="background-img">

    <!-- page loader -->
    <div class="container-fluid h-100 position-fixed loader-wrap bg-blur">
        <div class="row justify-content-center h-100">
            <div class="col-auto align-self-center text-center px-5 leaf">

                <div class="logo-square animated bg-gradient-theme-light mb-4">
                    <div class="icon-logo">
                        <img src="assets/img/logo.png" alt="">
                    </div>
                </div>
                <h4 class="mb-1">Restoran QR Menü</h4>
                <h6 class="mb-3 text-secondary">Online Menu</h6>
                <div class="dotslaoder">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
                <br>
            </div>
        </div>
    </div>

    <div class="sidebar-wrap ">
        <div class="sidebar">
            <div class="container">
              <div class="row align-items-center mb-4">
                    <div class="col-auto">
                        <a class="avatar avatar-44 rounded-circle coverimg"><img src="assets/img/logo2.png" alt=""></a>
                    </div>
                    <div class="col">
                        <h5 class="mb-0">Restoran QR Menü</h5>
                        <p class="text-muted">Kategoriler</p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-12 px-0">
                        <ul class="nav nav-pills" id="docmenu">
                            <li class="nav-item">
                                <a class="nav-link active" href="/">
                                     <div class="avatar avatar-40 icon"><i class="bi bi-house"></i></div>
                                    <div class="col">ANASAYFA</div>
                                    <div class="arrow"><i class="bi bi-chevron-right"></i></div>
                                </a>
                            </li>
                            <?php
                            $categories = $db->query("SELECT id, name FROM categories")->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($categories as $category): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="kategoriView.php?id=<?= $category['id'] ?>">
                                        <div class="avatar avatar-40 icon"><i class="bi bi-grid"></i></div>
                                        <div class="col"><?= $category['name'] ?></div>
                                        <div class="arrow"><i class="bi bi-chevron-right"></i></div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                
            </div>
            <button class="btn btn-white btn-sm btn-square-sm rounded-circle position-absolute start-0 end-0 bottom-0 mx-auto menu-btn mb-3">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>


