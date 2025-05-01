<?php
function sanitizeInput($data) {
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    $data = trim($data);
    return $data;
}

ob_start();
$sayfaTitle = "Ayarlar";
include 'inc/main.php';

$successMessage = null;
$errorMessage = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $siteTitle = sanitizeInput($_POST['siteTitle']);
    $maintenanceMode = isset($_POST['maintenanceMode']) ? 1 : 0; 

    $logoPath = null;
    if (isset($_FILES['siteLogo']) && $_FILES['siteLogo']['error'] === UPLOAD_ERR_OK) {
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileMimeType = mime_content_type($_FILES['siteLogo']['tmp_name']);

        if (!in_array($fileMimeType, $allowedMimeTypes)) {
            $errorMessage = "Sadece JPEG, PNG ve GIF formatındaki resim dosyalarına izin verilmektedir.";
        } else {
            $uploadDir = '../uploads/';
            $fileExtension = pathinfo($_FILES['siteLogo']['name'], PATHINFO_EXTENSION);
            $logoPath = "LOGO_" . uniqid() . ".$fileExtension";
            $fullLogoPath = $uploadDir . $logoPath;

            if (!move_uploaded_file($_FILES['siteLogo']['tmp_name'], $fullLogoPath)) {
                $errorMessage = "Logo yüklenirken bir hata oluştu.";
            }
        }
    }

    if (!isset($errorMessage)) {
        try {
            // Update settings in the database
            $stmt = $db->prepare("UPDATE settings SET site_title = :siteTitle, maintenance_mode = :maintenanceMode, site_logo = IF(:logoPath IS NOT NULL, :logoPath, site_logo) WHERE id = 1");
            $stmt->execute([
                ':siteTitle' => $siteTitle,
                ':maintenanceMode' => $maintenanceMode,
                ':logoPath' => $logoPath,
            ]);
            
            $successMessage = "Ayarlar başarıyla güncellendi.";

            // Redirect to avoid form resubmission on page refresh
            header("Location: ".$_SERVER['PHP_SELF']);
            exit;

        } catch (PDOException $e) {
            $errorMessage = "Veritabanı güncelleme hatası: " . $e->getMessage();
        }
    }
}

// Fetch current settings
$settings = $db->query("SELECT * FROM settings LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$siteTitle = $settings['site_title'] ?? '';
$siteLogo = $settings['site_logo'] ?? '';
?>

<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Ayarlar</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="./">Kontrol Paneli</a></li>
                            <li class="breadcrumb-item active">Ayarlar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <?php if ($successMessage): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $successMessage ?>
                    </div>
                <?php endif; ?>
                <?php if ($errorMessage): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $errorMessage ?>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Genel Ayarlar</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="siteTitle">Site Başlığı</label>
                                <input type="text" class="form-control" id="siteTitle" name="siteTitle" value="<?= htmlspecialchars($siteTitle) ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="siteLogo">Site Logosu</label>
                                <input type="file" class="form-control" id="siteLogo" name="siteLogo">
                                <?php if (!empty($siteLogo)): ?>
                                    <img src="../uploads/<?= htmlspecialchars($siteLogo) ?>" alt="Site Logo" style="max-width: 200px; margin-top: 10px;">
                                <?php endif; ?>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="maintenanceMode" name="maintenanceMode" <?= $settings['maintenance_mode'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="maintenanceMode">Bakım Modu</label>
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">Kaydet</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
</body>

<?php ob_end_flush(); ?>
