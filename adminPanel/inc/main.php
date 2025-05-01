<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /adminpanel/login');
    exit;
}

require_once '../inc/db.php';

$stmt = $db->prepare("SELECT username FROM admins WHERE id = :id");
$stmt->bindParam(':id', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$username = $user['username'] ?? '';
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $sayfaTitle ?> | Admin Panel</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css?v=3.2.0">
    <!-- icon -->
    <link rel="icon" href="https://i.hizliresim.com/4zxqv98.png" type="image/png">
</head>

<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">

        <!-- Preloader -->
            <div class="preloader flex-column justify-content-center align-items-center d-none d-md-flex">
            <img class="animation__wobble" src="dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
        </div>

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-dark">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a class="brand-link">
                <span class="brand-text font-weight-light text-center d-block">QR Menü Admin Panel</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="/assets/img/profile.png" class="img-circle elevation-2" alt="User Image">
                    </div>
                    <div class="info">
                        <a class="d-block"><?php echo htmlspecialchars($username); ?></a>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <?php
                    $current_page = strtolower($_SERVER['REQUEST_URI']); // Küçük harfe çeviriyoruz

                    // Aktif menü kontrolü için yardımcı bir fonksiyon
                    function isActiveMenu($page) {
                        global $current_page;
                        return rtrim($current_page, '/') === rtrim(strtolower($page), '/') ? 'active' : ''; // Tam eşleşme kontrolü
                    }
                    ?>

                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-header">MENULER</li>
                        <li class="nav-item">
                            <a href="/adminpanel/" class="nav-link <?= isActiveMenu('/adminpanel/') ?>">
                                <i class="nav-icon fas fa-home"></i>
                                <p>
                                    Anasayfa
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/adminpanel/kategoriler" class="nav-link <?= isActiveMenu('/adminpanel/kategoriler') ?>">
                                <i class="nav-icon fas fa-th-list"></i>
                                <p>
                                    Kategoriler
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/adminpanel/urunler" class="nav-link <?= isActiveMenu('/adminpanel/urunler') ?>">
                                <i class="nav-icon fas fa-box"></i>
                                <p>
                                    Ürünler
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/adminpanel/ayarlar" class="nav-link <?= isActiveMenu('/adminpanel/settings') ?>">
                                <i class="nav-icon fas fa-cogs"></i>
                                <p>
                                    Ayarlar
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/adminpanel/sifreDegistir" class="nav-link <?= isActiveMenu('/adminpanel/sifreDegistir') ?>">
                                <i class="nav-icon fas fa-key"></i>
                                <p>
                                    Şifre Değiştir
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/adminpanel/logout" class="nav-link text-danger <?= isActiveMenu('/adminpanel/logout') ?>">
                                <i class="nav-icon fas fa-sign-out-alt"></i>
                                <p>
                                    Çıkış Yap
                                </p>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->

        <!-- Main Footer -->
        <!-- ./wrapper -->

        <!-- REQUIRED SCRIPTS -->
        <!-- jQuery -->
        <script src="plugins/jquery/jquery.min.js"></script>
        <!-- Bootstrap -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>        <!-- overlayScrollbars -->
        <script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
        <!-- AdminLTE App -->
        <script src="dist/js/adminlte.js?v=3.2.0"></script>

        <!-- PAGE PLUGINS -->
        <!-- jQuery Mapael -->
        <script src="plugins/jquery-mousewheel/jquery.mousewheel.js"></script>
        <script src="plugins/raphael/raphael.min.js"></script>
        <script src="plugins/jquery-mapael/jquery.mapael.min.js"></script>
        <script src="plugins/jquery-mapael/maps/usa_states.min.js"></script>
        <!-- ChartJS -->
        <script src="plugins/chart.js/Chart.min.js"></script>

        <!-- AdminLTE for demo purposes -->
        <script src="dist/js/demo.js"></script>
        <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
        <?php if ($sayfaTitle === 'Anasayfa'): ?>
        <script src="dist/js/pages/dashboard2.js"></script>
        <?php endif; ?>

</body>
</html>