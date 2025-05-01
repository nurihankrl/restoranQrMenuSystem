<?php
$sayfaTitle = "Anasayfa";
include 'inc/main.php';

$totalCategories = $db->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$totalProducts = $db->query("SELECT COUNT(*) FROM products")->fetchColumn();

$recentProducts = $db->query("SELECT name, price, created_at FROM products ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

$phpVersion = phpversion();
$diskUsage = round(disk_free_space("/") / 1024 / 1024, 2) . " MB boş alan";
?>

<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Anasayfa</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Kontrol Paneli</a></li>
                                <li class="breadcrumb-item active">Anasayfa</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <!-- Info boxes -->
                    <div class="row">
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-th-list"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Kategoriler</span>
                                    <span class="info-box-number"><?= $totalCategories ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-box"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Ürünler</span>
                                    <span class="info-box-number"><?= $totalProducts ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.row -->

                    <!-- Son Eklenen Ürünler -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Son Eklenen Ürünler</h3>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group">
                                        <?php foreach ($recentProducts as $product): ?>
                                            <li class="list-group-item">
                                                <?= htmlspecialchars($product['name']) ?> - <?= htmlspecialchars($product['price']) ?> TL
                                                <span class="float-right text-muted"><?= htmlspecialchars($product['created_at']) ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Sistem Durumu -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Sistem Durumu</h3>
                                </div>
                                <div class="card-body">
                                    <p>PHP Versiyonu: <?= $phpVersion ?></p>
                                    <p>Disk Kullanımı: <?= $diskUsage ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.row -->

                    <!-- Hızlı Erişim Butonları -->
                    <div class="row">
                        <div class="col-lg-4 col-md-6">
                            <a href="/adminpanel/kategoriler" class="btn btn-primary btn-block">Yeni Kategori Ekle</a>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <a href="/adminpanel/urunler" class="btn btn-success btn-block">Yeni Ürün Ekle</a>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <a href="/adminpanel/ayarlar" class="btn btn-warning btn-block">Ayarlar</a>
                        </div>
                    </div>
                </div>
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
    </div>
</body>
</html>