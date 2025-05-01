<?php
ob_start();
$sayfaTitle = "Ürünler";
include 'inc/main.php';

$successMessage = null;
$selectedCategoryId = $_POST['category_id'] ?? null;

// Fetch categories
$categories = $db->query("SELECT id, name FROM categories")->fetchAll(PDO::FETCH_ASSOC);

// Handle product addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addProduct'])) {
    $productName = sanitizeInput($_POST['productName']);
    $productDescription = sanitizeInput($_POST['productDescription']);
    $productPrice = sanitizeInput($_POST['productPrice']);
    $productCategory = intval($_POST['productCategory']);
    $productImage = $_FILES['productImage'];

    $imagePath = null;
    if ($productImage['error'] === UPLOAD_ERR_OK) {
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileMimeType = mime_content_type($productImage['tmp_name']);

        if (!in_array($fileMimeType, $allowedMimeTypes)) {
            $errorMessage = "Sadece JPEG, PNG ve GIF formatındaki resim dosyalarına izin verilmektedir.";
        } else {
            $uploadDir = '../uploads/';
            $fileExtension = pathinfo($productImage['name'], PATHINFO_EXTENSION); // Dosya uzantısını al
            $randomNumber = rand(10000, 99999); // 5 basamaklı rastgele sayı oluştur
            $imagePath = "URU_" . uniqid() . "_$randomNumber.$fileExtension"; // Dosya adını oluştur
            $fullImagePath = $uploadDir . $imagePath;

            if (!move_uploaded_file($productImage['tmp_name'], $fullImagePath)) {
                $errorMessage = "Ürün resmi yüklenirken bir hata oluştu.";
            }
        }
    }

    if (!isset($errorMessage)) {
        $stmt = $db->prepare("INSERT INTO products (name, description, price, category_id) VALUES (:name, :description, :price, :category_id)");
        $stmt->execute([
            ':name' => $productName,
            ':description' => $productDescription,
            ':price' => $productPrice,
            ':category_id' => $productCategory,
        ]);
        $productId = $db->lastInsertId();

        if ($imagePath) {
            $stmt = $db->prepare("INSERT INTO product_images (product_id, image_path, is_featured) VALUES (:product_id, :image_path, 1)");
            $stmt->execute([
                ':product_id' => $productId,
                ':image_path' => $imagePath,
            ]);
        }

        $successMessage = "<b>$productName</b> adlı ürün başarıyla eklendi.";
    }
}

// Handle product deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteProductId'])) {
    $deleteProductId = intval($_POST['deleteProductId']);

    try {
        // Fetch all image paths for the product
        $stmt = $db->prepare("SELECT image_path FROM product_images WHERE product_id = :product_id");
        $stmt->execute([':product_id' => $deleteProductId]);
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Delete each image file from the uploads directory
        foreach ($images as $image) {
            $imagePath = "../uploads/" . $image['image_path'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        // Delete all image records for the product from the database
        $db->prepare("DELETE FROM product_images WHERE product_id = :product_id")->execute([':product_id' => $deleteProductId]);

        // Delete the product itself
        $stmt = $db->prepare("DELETE FROM products WHERE id = :id");
        $stmt->execute([':id' => $deleteProductId]);

        $successMessage = "Ürün ve bağlı fotoğraflar başarıyla silindi.";
    } catch (PDOException $e) {
        $errorMessage = "Ürün silinirken bir hata oluştu: " . $e->getMessage();
    }
}

// Handle unsetting featured image
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['unsetFeaturedImageId'])) {
    $imageId = intval($_POST['unsetFeaturedImageId']);
    $productId = intval($_POST['productId']);

    try {
        // Reset the featured status of the selected image
        $db->prepare("UPDATE product_images SET is_featured = 0 WHERE id = :id")
            ->execute([':id' => $imageId]);

        $successMessage = "Öne çıkan fotoğraf başarıyla iptal edildi.";
    } catch (PDOException $e) {
        $errorMessage = "Öne çıkan fotoğraf iptal edilirken bir hata oluştu: " . $e->getMessage();
    }
}

// Handle setting featured image
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setFeaturedImageId'])) {
    $imageId = intval($_POST['setFeaturedImageId']);
    $productId = intval($_POST['productId']);

    try {
        // Reset all images for the product to not featured
        $db->prepare("UPDATE product_images SET is_featured = 0 WHERE product_id = :product_id")
            ->execute([':product_id' => $productId]);

        // Set the selected image as featured
        $db->prepare("UPDATE product_images SET is_featured = 1 WHERE id = :id")
            ->execute([':id' => $imageId]);

        $successMessage = "Öne çıkan fotoğraf başarıyla ayarlandı.";
    } catch (PDOException $e) {
        $errorMessage = "Öne çıkan fotoğraf ayarlanırken bir hata oluştu: " . $e->getMessage();
    }
}

// Handle product image addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addImageProductId'])) {
    $productId = intval($_POST['addImageProductId']);
    $productImage = $_FILES['productImage'];

    // `productId` kontrolü
    $stmt = $db->prepare("SELECT COUNT(*) FROM products WHERE id = :id");
    $stmt->execute([':id' => $productId]);
    $productExists = $stmt->fetchColumn();

    if (!$productExists) {
        $errorMessage = "Geçersiz ürün ID'si.";
    } elseif ($productImage['error'] === UPLOAD_ERR_OK) {
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileMimeType = mime_content_type($productImage['tmp_name']);

        if (!in_array($fileMimeType, $allowedMimeTypes)) {
            $errorMessage = "Sadece JPEG, PNG ve GIF formatındaki resim dosyalarına izin verilmektedir.";
        } else {
            $uploadDir = '../uploads/';
            $fileExtension = pathinfo($productImage['name'], PATHINFO_EXTENSION); // Dosya uzantısını al
            $randomNumber = rand(10000, 99999); // 5 basamaklı rastgele sayı oluştur
            $imagePath = "URU_" . $productId . "_$randomNumber.$fileExtension"; // Dosya adını oluştur
            $fullImagePath = $uploadDir . $imagePath;

            if (move_uploaded_file($productImage['tmp_name'], $fullImagePath)) {
                // Tüm mevcut fotoğrafların `is_featured` değerini sıfırla
                $db->prepare("UPDATE product_images SET is_featured = 0 WHERE product_id = :product_id")
                    ->execute([':product_id' => $productId]);

                // Yeni fotoğrafı `is_featured = 1` olarak ekle
                $stmt = $db->prepare("INSERT INTO product_images (product_id, image_path, is_featured) VALUES (:product_id, :image_path, 1)");
                $stmt->execute([
                    ':product_id' => $productId,
                    ':image_path' => $imagePath,
                ]);

                $successMessage = "Fotoğraf başarıyla eklendi ve anasayfada göster olarak ayarlandı.";
            } else {
                $errorMessage = "Fotoğraf yüklenirken bir hata oluştu.";
            }
        }
    } else {
        $errorMessage = "Fotoğraf yüklenirken bir hata oluştu.";
    }
}

// Handle product image deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteImageId'])) {
    $imageId = intval($_POST['deleteImageId']);

    try {
        // Fetch the image path from the database
        $stmt = $db->prepare("SELECT image_path FROM product_images WHERE id = :id");
        $stmt->execute([':id' => $imageId]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($image) {
            $imagePath = "../uploads/" . $image['image_path'];

            // Delete the image file from the uploads directory
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            // Delete the image record from the database
            $db->prepare("DELETE FROM product_images WHERE id = :id")->execute([':id' => $imageId]);

            $successMessage = "Fotoğraf başarıyla silindi.";
        } else {
            $errorMessage = "Fotoğraf bulunamadı.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Fotoğraf silinirken bir hata oluştu: " . $e->getMessage();
    }
}

// Handle product update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editProductId'])) {
    $productId = intval($_POST['editProductId']);
    $productName = sanitizeInput($_POST['productName']);
    $productDescription = sanitizeInput($_POST['productDescription']);
    $productPrice = sanitizeInput($_POST['productPrice']);
    $productCategory = intval($_POST['productCategory']);

    try {
        $stmt = $db->prepare("UPDATE products SET name = :name, description = :description, price = :price, category_id = :category_id WHERE id = :id");
        $stmt->execute([
            ':name' => $productName,
            ':description' => $productDescription,
            ':price' => $productPrice,
            ':category_id' => $productCategory,
            ':id' => $productId,
        ]);

        $successMessage = "<b>$productName</b> adlı ürün başarıyla güncellendi.";
    } catch (PDOException $e) {
        $errorMessage = "Ürün güncellenirken bir hata oluştu: " . $e->getMessage();
    }
}

// Fetch products with category names
try {
    $query = "
        SELECT p.*, c.name AS category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id
    ";
    if ($selectedCategoryId) {
        $query .= " WHERE p.category_id = :category_id";
        $stmt = $db->prepare($query);
        $stmt->execute([':category_id' => $selectedCategoryId]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $products = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $products = [];
    $errorMessage = "Ürünler yüklenirken bir hata oluştu: " . $e->getMessage();
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
                        <h1 class="m-0">Ürünler</h1>
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
                                <h3 class="card-title">Ürün Listesi</h3>
                                <div class="card-tools">
                                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addProductModal">Yeni Ürün Ekle</button>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <!-- Kategori Seçimi -->
                                <form method="POST" action="">
                                    <div class="form-group">
                                        <select class="form-control" id="categoryFilter" name="category_id" onchange="this.form.submit()">
                                            <option value="" <?= is_null($selectedCategoryId) ? 'selected' : '' ?>>Tüm Ürünler</option>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?= $category['id'] ?>" <?= $selectedCategoryId == $category['id'] ? 'selected' : '' ?>><?= $category['name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </form>

                                <!-- Ürün Tablosu -->
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Ürün Adı</th>
                                            <th>Fiyat</th>
                                            <th>Kategori</th>
                                            <th>Anasayfada Gösterilen Resim</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td><?= $product['id'] ?></td>
                                            <td><?= $product['name'] ?></td>
                                            <td><?= $product['price'] ?></td>
                                            <td><?= $product['category_name'] ?? 'Kategori Yok' ?></td>
                                            <td>
                                                <?php
                                                $featuredImage = $db->query("SELECT image_path FROM product_images WHERE product_id = {$product['id']} AND is_featured = 1")->fetchColumn();
                                                if ($featuredImage): ?>
                                                    <img src="../uploads/<?= $featuredImage ?>" alt="<?= $product['name'] ?>" style="width: 200px; height: auto;">
                                                <?php else: ?>
                                                    <span class="text-muted">Resim Yok</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="deleteProductId" value="<?= $product['id'] ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bu ürünü silmek istediğinize emin misiniz?')">Sil</button>
                                                </form>
                                                <button class="btn btn-warning btn-sm edit-product-btn" data-id="<?= $product['id'] ?>" data-name="<?= $product['name'] ?>" data-description="<?= $product['description'] ?>" data-price="<?= $product['price'] ?>" data-category="<?= $product['category_id'] ?>">Düzenle</button>
                                                <button class="btn btn-info btn-sm manage-images-btn" data-id="<?= $product['id'] ?>" data-name="<?= $product['name'] ?>">Fotoğrafları Yönet</button>
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

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addProductForm" action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="addProduct" value="1">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addProductModalLabel">Yeni Ürün Ekle</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="addProductName">Ürün Adı</label>
                            <input type="text" class="form-control" id="addProductName" name="productName" required>
                        </div>
                        <div class="form-group">
                            <label for="addProductDescription">Açıklama</label>
                            <textarea class="form-control" id="addProductDescription" name="productDescription" rows="5"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="addProductPrice">Fiyat</label>
                            <input type="number" class="form-control" id="addProductPrice" name="productPrice" required>
                        </div>
                        <div class="form-group">
                            <label for="addProductCategory">Kategori</label>
                            <select class="form-control" id="addProductCategory" name="productCategory" required>
                                <option value="" disabled selected>Kategori Seçin</option>
                                <?php
                                $categories = $db->query("SELECT id, name FROM categories")->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="addProductImage">Ürün Resmi</label>
                            <input type="file" class="form-control" id="addProductImage" name="productImage" required>
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

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editProductForm" action="" method="POST">
                    <input type="hidden" name="editProductId" id="editProductId">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProductModalLabel">Ürün Düzenle</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="editProductName">Ürün Adı</label>
                            <input type="text" class="form-control" id="editProductName" name="productName" required>
                        </div>
                        <div class="form-group">
                            <label for="editProductDescription">Açıklama</label>
                            <textarea class="form-control" id="editProductDescription" name="productDescription" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="editProductPrice">Fiyat</label>
                            <input type="number" class="form-control" id="editProductPrice" name="productPrice" required>
                        </div>
                        <div class="form-group">
                            <label for="editProductCategory">Kategori</label>
                            <select class="form-control" id="editProductCategory" name="productCategory" required>
                                <option value="" disabled selected>Kategori Seçin</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
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

    <!-- Manage Product Images Modal -->
    <div class="modal fade" id="manageImagesModal" tabindex="-1" aria-labelledby="manageImagesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="manageImagesModalLabel">Ürün Fotoğrafları</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="imagesContainer" class="row">
                        <!-- Fotoğraflar burada yüklenecek -->
                    </div>
                    <form id="uploadImageForm" action="" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="addImageProductId" id="addImageProductId" value="">
                        <div class="form-group mt-3">
                            <label for="productImage">Yeni Fotoğraf Yükle</label>
                            <input type="file" class="form-control" id="productImage" name="productImage" required>
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
        // Handle "Fotoğrafları Yönet" button click
        $('.manage-images-btn').on('click', function () {
            const productId = $(this).data('id');
            const productName = $(this).data('name');
            $('#manageImagesModalLabel').html(`Ürün Fotoğrafları: <b>${productName}</b>`);
            $('#addImageProductId').val(productId);

            // Fetch images for the selected product via urunlerApi.php
            $.ajax({
                url: 'api/urunlerApi.php',
                method: 'POST',
                data: {
                    apiKey: 'Q492ASDFQWE1234',
                    productId: productId
                },
                success: function (response) {
                    if (response.success) {
                        let imagesHtml = '';
                        if (response.images.length > 0) {
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
                                                <input type="hidden" name="productId" value="${productId}">
                                                <button type="submit" class="btn btn-secondary btn-sm">Anasayfada Göstermeyi İptal Et</button>
                                            </form>
                                        ` : `
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="setFeaturedImageId" value="${image.id}">
                                                <input type="hidden" name="productId" value="${productId}">
                                                <button type="submit" class="btn btn-primary btn-sm">Anasayfada Göster</button>
                                            </form>
                                        `}
                                    </div>
                                `;
                            });
                        } else {
                            imagesHtml = '<p class="text-center text-muted">Bu ürüne ait fotoğraf bulunamadı. Yeni bir fotoğraf ekleyebilirsiniz.</p>';
                        }
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
        $('#manageImagesModal').on('hidden.bs.modal', function () {
            $(this).find('form')[0].reset();
            $('#imagesContainer').html(''); // Clear images container
        });

        // Handle image upload form submission
        $('#uploadImageForm').on('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            $.ajax({
                url: '', // Current page URL
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function () {
                    // Reload the page after successful upload
                    location.reload();
                },
                error: function () {
                    alert('Fotoğraf yüklenirken bir hata oluştu.');
                }
            });
        });

        // Handle "Düzenle" button click
        $('.edit-product-btn').on('click', function () {
            const productId = $(this).data('id');
            const productName = $(this).data('name');
            const productDescription = $(this).data('description');
            const productPrice = $(this).data('price');
            const productCategory = $(this).data('category');

            $('#editProductId').val(productId);
            $('#editProductName').val(productName);
            $('#editProductDescription').val(productDescription);
            $('#editProductPrice').val(productPrice);
            $('#editProductCategory').val(productCategory);

            $('#editProductModal').modal('show');
        });

        // Automatically close alerts after 5 seconds
        setTimeout(function () {
            $('.alert').alert('close');
        }, 5000);
    });
</script>
</body>

<?php ob_end_flush(); // Flush the output buffer ?>