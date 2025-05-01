<?php
ob_start();
$sayfaTitle = "Kategoriler";
include 'inc/main.php';

$successMessage = null;

// Handle category addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addCategory'])) {
    $categoryName = sanitizeInput($_POST['categoryName']);
    $categoryImage = $_FILES['categoryImage'];

    $imagePath = null;
    if ($categoryImage['error'] === UPLOAD_ERR_OK) {
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileMimeType = mime_content_type($categoryImage['tmp_name']);

        if (!in_array($fileMimeType, $allowedMimeTypes)) {
            $errorMessage = "Sadece JPEG, PNG ve GIF formatındaki resim dosyalarına izin verilmektedir.";
        } else {
            $uploadDir = '../uploads/';
            $fileExtension = pathinfo($categoryImage['name'], PATHINFO_EXTENSION); // Dosya uzantısını al
            $randomNumber = rand(10000, 99999); // 5 basamaklı rastgele sayı oluştur
            $imagePath = "KATE_" . uniqid() . "_$randomNumber.$fileExtension"; // Dosya adını oluştur
            $fullImagePath = $uploadDir . $imagePath;

            if (!move_uploaded_file($categoryImage['tmp_name'], $fullImagePath)) {
                $errorMessage = "Kategori resmi yüklenirken bir hata oluştu.";
            }
        }
    }

    if (!isset($errorMessage)) {
        $stmt = $db->prepare("INSERT INTO categories (name) VALUES (:name)");
        $stmt->execute([':name' => $categoryName]);
        $categoryId = $db->lastInsertId();

        if ($imagePath) {
            $stmt = $db->prepare("INSERT INTO category_images (category_id, image_path, is_featured) VALUES (:category_id, :image_path, 1)");
            $stmt->execute([
                ':category_id' => $categoryId,
                ':image_path' => $imagePath,
            ]);
        }

        $successMessage = "<b>$categoryName</b> adlı kategori başarıyla eklendi.";
    }
}

// Handle category update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['categoryId'])) {
    $categoryId = intval($_POST['categoryId']);
    $categoryName = sanitizeInput($_POST['categoryName']);

    $stmt = $db->prepare("UPDATE categories SET name = :name WHERE id = :id");
    $stmt->execute([
        ':name' => $categoryName,
        ':id' => $categoryId,
    ]);

    $successMessage = "<b>$categoryName</b> adlı kategori başarıyla güncellendi.";
}

// Handle category deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteCategoryId'])) {
    $deleteCategoryId = intval($_POST['deleteCategoryId']);

    try {
        // Fetch all image paths for the category
        $stmt = $db->prepare("SELECT image_path FROM category_images WHERE category_id = :category_id");
        $stmt->execute([':category_id' => $deleteCategoryId]);
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Delete each image file from the uploads directory
        foreach ($images as $image) {
            $imagePath = "../uploads/" . $image['image_path'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        // Delete all image records for the category from the database
        $db->prepare("DELETE FROM category_images WHERE category_id = :category_id")->execute([':category_id' => $deleteCategoryId]);

        // Delete the category itself
        $stmt = $db->prepare("DELETE FROM categories WHERE id = :id");
        $stmt->execute([':id' => $deleteCategoryId]);

        $successMessage = "Kategori ve bağlı fotoğraflar başarıyla silindi.";
    } catch (PDOException $e) {
        $errorMessage = "Kategori silinirken bir hata oluştu: " . $e->getMessage();
    }
}

// Handle category image addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addImageCategoryId'])) {
    $categoryId = intval($_POST['addImageCategoryId']);
    $categoryImage = $_FILES['categoryImage'];

    // `categoryId` kontrolü
    $stmt = $db->prepare("SELECT COUNT(*) FROM categories WHERE id = :id");
    $stmt->execute([':id' => $categoryId]);
    $categoryExists = $stmt->fetchColumn();

    if (!$categoryExists) {
        $errorMessage = "Geçersiz kategori ID'si.";
    } elseif ($categoryImage['error'] === UPLOAD_ERR_OK) {
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileMimeType = mime_content_type($categoryImage['tmp_name']);

        if (!in_array($fileMimeType, $allowedMimeTypes)) {
            $errorMessage = "Sadece JPEG, PNG ve GIF formatındaki resim dosyalarına izin verilmektedir.";
        } else {
            $uploadDir = '../uploads/';
            $fileExtension = pathinfo($categoryImage['name'], PATHINFO_EXTENSION); // Dosya uzantısını al
            $randomNumber = rand(10000, 99999); // 5 basamaklı rastgele sayı oluştur
            $imagePath = "KATE_" . $categoryId . "_$randomNumber.$fileExtension"; // Dosya adını oluştur
            $fullImagePath = $uploadDir . $imagePath;

            if (move_uploaded_file($categoryImage['tmp_name'], $fullImagePath)) {
                $stmt = $db->prepare("INSERT INTO category_images (category_id, image_path) VALUES (:category_id, :image_path)");
                $stmt->execute([
                    ':category_id' => $categoryId,
                    ':image_path' => $imagePath,
                ]);

                // Fetch the category name
                $categoryNameStmt = $db->prepare("SELECT name FROM categories WHERE id = :id");
                $categoryNameStmt->execute([':id' => $categoryId]);
                $categoryName = $categoryNameStmt->fetchColumn();

                $successMessage = "<b>$categoryName</b> adlı kategori fotoğrafı başarıyla eklendi.";
            } else {
                $errorMessage = "Fotoğraf yüklenirken bir hata oluştu.";
            }
        }
    } else {
        $errorMessage = "Fotoğraf yüklenirken bir hata oluştu.";
    }
}

// Handle category image deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteImageId'])) {
    $imageId = intval($_POST['deleteImageId']);
    try {
        $stmt = $db->prepare("SELECT image_path, category_id FROM category_images WHERE id = :id");
        $stmt->execute([':id' => $imageId]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($image) {
            $imagePath = "../uploads/" . $image['image_path'];
            $categoryId = $image['category_id'];

            // Fetch the category name
            $categoryNameStmt = $db->prepare("SELECT name FROM categories WHERE id = :id");
            $categoryNameStmt->execute([':id' => $categoryId]);
            $categoryName = $categoryNameStmt->fetchColumn();

            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            $db->prepare("DELETE FROM category_images WHERE id = :id")->execute([':id' => $imageId]);
            $successMessage = "<b>$categoryName</b> adlı kategoriden fotoğraf başarıyla silindi.";
        } else {
            $errorMessage = "Fotoğraf bulunamadı.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Fotoğraf silinirken bir hata oluştu: " . $e->getMessage();
    }
}

// Handle setting featured image
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setFeaturedImageId'])) {
    $imageId = intval($_POST['setFeaturedImageId']);
    $categoryId = intval($_POST['categoryId']);
    $categoryName = htmlspecialchars($_POST['categoryName'] ?? 'Kategori', ENT_QUOTES, 'UTF-8'); // Varsayılan değer ekledik

    // Reset all images for the category to not featured
    $db->prepare("UPDATE category_images SET is_featured = 0 WHERE category_id = :category_id")
        ->execute([':category_id' => $categoryId]);

    // Set the selected image as featured
    $db->prepare("UPDATE category_images SET is_featured = 1 WHERE id = :id")
        ->execute([':id' => $imageId]);

    $successMessage = "<b>$categoryName</b> kategorisinin öne çıkan fotoğrafı başarıyla güncellendi.";
}

// Handle unsetting featured image
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['unsetFeaturedImageId'])) {
    $imageId = intval($_POST['unsetFeaturedImageId']);
    $categoryId = intval($_POST['categoryId']);
    $categoryName = htmlspecialchars($_POST['categoryName'] ?? 'Kategori', ENT_QUOTES, 'UTF-8'); // Varsayılan değer ekledik

    // Reset the featured status of the selected image
    $db->prepare("UPDATE category_images SET is_featured = 0 WHERE id = :id")
        ->execute([':id' => $imageId]);

    $successMessage = "<b>$categoryName</b> kategorisinin öne çıkan fotoğrafı başarıyla iptal edildi.";
}

// Fetch categories
try {
    $categories = $db->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categories = [];
    $errorMessage = "Kategoriler yüklenirken bir hata oluştu: " . $e->getMessage();
}

// Handle fetching category images for modal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fetchImagesCategoryId'])) {
    $categoryId = intval($_POST['fetchImagesCategoryId']);

    $stmt = $db->prepare("SELECT id, image_path, is_featured FROM category_images WHERE category_id = :category_id");
    $stmt->execute([':category_id' => $categoryId]);
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($images as $image) {
        echo '<div class="col-md-3 text-center">';
        echo '<img src="../uploads/' . htmlspecialchars($image['image_path']) . '" class="img-thumbnail mb-2" style="width: 100%; height: auto;">';
        echo '<form method="POST" style="display:inline;">';
        echo '<input type="hidden" name="deleteImageId" value="' . $image['id'] . '">';
        echo '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Bu fotoğrafı silmek istediğinize emin misiniz?\')">Sil</button>';
        echo '</form>';
        if ($image['is_featured']) {
            echo '<form method="POST" style="display:inline;">';
            echo '<input type="hidden" name="unsetFeaturedImageId" value="' . $image['id'] . '">';
            echo '<input type="hidden" name="categoryId" value="' . $categoryId . '">';
            echo '<input type="hidden" name="categoryName" value="' . htmlspecialchars($categoryName ?? 'Kategori', ENT_QUOTES, 'UTF-8') . '">';
            echo '<button type="submit" class="btn btn-secondary btn-sm">Anasayfada Göstermeyi İptal Et</button>';
            echo '</form>';
        } else {
            echo '<form method="POST" style="display:inline;">';
            echo '<input type="hidden" name="setFeaturedImageId" value="' . $image['id'] . '">';
            echo '<input type="hidden" name="categoryId" value="' . $categoryId . '">';
            echo '<input type="hidden" name="categoryName" value="' . htmlspecialchars($categoryName ?? 'Kategori', ENT_QUOTES, 'UTF-8') . '">';
            echo '<button type="submit" class="btn btn-primary btn-sm">Anasayfada Göster</button>';
            echo '</form>';
        }
        echo '</div>';
    }
    exit; // Sadece modal için yanıt döndürüyoruz
}

// Handle fetching category images for modal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['manageImagesCategoryId'])) {
    $categoryId = intval($_POST['manageImagesCategoryId']);

    $stmt = $db->prepare("SELECT id, image_path, is_featured FROM category_images WHERE category_id = :category_id");
    $stmt->execute([':category_id' => $categoryId]);
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Modal içeriğini döndür
    echo '<div class="row">';
    foreach ($images as $image) {
        echo '<div class="col-md-3 text-center">';
        echo '<img src="../uploads/' . htmlspecialchars($image['image_path']) . '" class="img-thumbnail mb-2" style="width: 100%; height: auto;">';
        echo '<form method="POST" style="display:inline;">';
        echo '<input type="hidden" name="deleteImageId" value="' . $image['id'] . '">';
        echo '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Bu fotoğrafı silmek istediğinize emin misiniz?\')">Sil</button>';
        echo '</form>';
        if ($image['is_featured']) {
            echo '<form method="POST" style="display:inline;">';
            echo '<input type="hidden" name="unsetFeaturedImageId" value="' . $image['id'] . '">';
            echo '<input type="hidden" name="categoryId" value="' . $categoryId . '">';
            echo '<input type="hidden" name="categoryName" value="' . htmlspecialchars($categoryName ?? 'Kategori', ENT_QUOTES, 'UTF-8') . '">';
            echo '<button type="submit" class="btn btn-secondary btn-sm">Anasayfada Göstermeyi İptal Et</button>';
            echo '</form>';
        } else {
            echo '<form method="POST" style="display:inline;">';
            echo '<input type="hidden" name="setFeaturedImageId" value="' . $image['id'] . '">';
            echo '<input type="hidden" name="categoryId" value="' . $categoryId . '">';
            echo '<input type="hidden" name="categoryName" value="' . htmlspecialchars($categoryName ?? 'Kategori', ENT_QUOTES, 'UTF-8') . '">';
            echo '<button type="submit" class="btn btn-primary btn-sm">Anasayfada Göster</button>';
            echo '</form>';
        }
        echo '</div>';
    }
    echo '</div>';
    exit; // Sadece modal için yanıt döndürüyoruz
}
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
                        <h1 class="m-0">Kategoriler</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="./">Kontrol Paneli</a></li>
                            <li class="breadcrumb-item active"><?php echo explode('|', $sayfaTitle)[0]; ?></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <?php if ($successMessage): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?= $successMessage ?>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($errorMessage)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= $errorMessage ?>
                            </div>
                        <?php endif; ?>
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Kategori Listesi</h3>
                                <div class="card-tools">
                                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addCategoryModal">Yeni Kategori Ekle</button>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Kategori Adı</th>
                                            <th>Anasayfada Gösterilen Resim</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($categories as $category): ?>
                                        <tr>
                                            <td><?= $category['id'] ?></td>
                                            <td><?= $category['name'] ?></td>
                                            <td>
                                                <?php
                                                $featuredImage = $db->query("SELECT image_path FROM category_images WHERE category_id = {$category['id']} AND is_featured = 1")->fetchColumn();
                                                if ($featuredImage): ?>
                                                    <img src="../uploads/<?= $featuredImage ?>" alt="<?= $category['name'] ?>" style="width: 200px; height: auto;">
                                                <?php else: ?>
                                                    <span class="text-muted">Resim Yok</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="deleteCategoryId" value="<?= $category['id'] ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bu kategoriyi silmek istediğinize emin misiniz? Silerseniz bu kategoriye bağlı ürünler de silinecek!')">Sil</button>
                                                </form>
                                                <button class="btn btn-warning btn-sm edit-category-btn" data-id="<?= $category['id'] ?>" data-name="<?= $category['name'] ?>">Düzenle</button>
                                                <button class="btn btn-info btn-sm manage-images-btn" data-id="<?= $category['id'] ?>" data-name="<?= $category['name'] ?>">Fotoğrafları Yönet</button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addCategoryForm" action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="addCategory" value="1">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCategoryModalLabel">Yeni Kategori Ekle</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="addCategoryName">Kategori Adı</label>
                            <input type="text" class="form-control" id="addCategoryName" name="categoryName" required>
                        </div>
                        <div class="form-group">
                            <label for="addCategoryImage">Kategori Resmi</label>
                            <input type="file" class="form-control" id="addCategoryImage" name="categoryImage" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                        <button type="submit" class="btn btn-primary">Ekle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /.modal -->

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editCategoryForm" action="" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editCategoryModalLabel">Kategori Düzenle</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="categoryId" id="editCategoryId">
                        <div class="form-group">
                            <label for="editCategoryName">Kategori Adı</label>
                            <input type="text" class="form-control" id="editCategoryName" name="categoryName" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /.modal -->

    <!-- Manage Category Images Modal -->
    <div class="modal fade" id="manageImagesModal" tabindex="-1" aria-labelledby="manageImagesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="manageImagesModalLabel">Kategori Fotoğrafları</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="imagesContainer" class="row">
                        <!-- Fotoğraflar burada yüklenecek -->
                    </div>
                    <form id="uploadImageForm" action="" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="addImageCategoryId" id="addImageCategoryId" value="">
                        <div class="form-group mt-3">
                            <label for="categoryImage">Yeni Fotoğraf Yükle</label>
                            <input type="file" class="form-control" id="categoryImage" name="categoryImage" required>
                        </div>
                        <button type="submit" class="btn btn-primary mt-2">Fotoğraf Yükle</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /.modal -->

</div>
<!-- ./wrapper -->

<script>
    $(document).ready(function () {
        // Automatically close alerts after 7 seconds
        setTimeout(function () {
            $('.alert').alert('close');
        }, 5000);

        // Handle "Düzenle" button click
        $('.edit-category-btn').on('click', function () {
            const categoryId = $(this).data('id');
            const categoryName = $(this).data('name');

            $('#editCategoryId').val(categoryId);
            $('#editCategoryName').val(categoryName);

            $('#editCategoryModal').modal('show');
        });

        // Handle "Fotoğrafları Yönet" button click
        $('.manage-images-btn').on('click', function () {
            const categoryId = $(this).data('id');
            const categoryName = $(this).data('name');
            $('#manageImagesModalLabel').html(`Kategori Fotoğrafları: <b>${categoryName}</b>`);
            $('#addImageCategoryId').val(categoryId);

            // Fetch images for the selected category via kategoriApi.php
            $.ajax({
                url: 'api/kategoriApi.php', // Doğru URL
                method: 'POST',
                data: {
                    apiKey: 'Q492ASDFQWE1234', // Güvenlik anahtarı
                    categoryId: categoryId
                },
                success: function (response) {
                    if (response.success) {
                        let imagesHtml = '';
                        response.images.forEach(image => {
                            imagesHtml += `
                                <div class="col-md-3 text-center">
                                    <img src="../uploads/${image.image_path}" class="img-thumbnail mb-2" style="width: 100%; height: auto;">
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="deleteImageId" value="${image.id}">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bu fotoğrafı silmek istediğinize emin misiniz?')">Sil</button>
                                    </form>
                                    ${image.is_featured ? `
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="unsetFeaturedImageId" value="${image.id}">
                                            <input type="hidden" name="categoryId" value="${categoryId}">
                                            <input type="hidden" name="categoryName" value="${categoryName}">
                                            <button type="submit" class="btn btn-secondary btn-sm">Anasayfada Göstermeyi İptal Et</button>
                                        </form>
                                    ` : `
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="setFeaturedImageId" value="${image.id}">
                                            <input type="hidden" name="categoryId" value="${categoryId}">
                                            <input type="hidden" name="categoryName" value="${categoryName}">
                                            <button type="submit" class="btn btn-primary btn-sm">Anasayfada Göster</button>
                                        </form>
                                    `}
                                </div>
                            `;
                        });
                        $('#imagesContainer').html(imagesHtml);
                    } else {
                        $('#imagesContainer').html('<p class="text-danger">Fotoğraflar yüklenirken bir hata oluştu: ' + response.message + '</p>');
                    }
                },
                error: function () {
                    $('#imagesContainer').html('<p class="text-danger">Fotoğraflar yüklenirken bir hata oluştu.</p>');
                }
            });

            $('#manageImagesModal').modal('show');
        });

        // Ensure modal closes properly
        $('#editCategoryModal, #manageImagesModal').on('hidden.bs.modal', function () {
            $(this).find('form')[0].reset();
            $('#imagesContainer').html(''); // Clear images container
        });
    });
</script>
</body>

<?php ob_end_flush(); // Flush the output buffer ?>
