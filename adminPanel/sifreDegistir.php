<?php
$sayfaTitle = "Kullanıcı Bilgilerini Güncelle";
include 'inc/main.php';

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = $_POST['new_username'] ?? '';
    $new_password = $_POST['new_password'] ?? '';

    if (!empty($new_username) && !empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $update_stmt = $db->prepare("UPDATE admins SET username = :username, password = :password WHERE id = :id");
        $update_stmt->bindParam(':username', $new_username);
        $update_stmt->bindParam(':password', $hashed_password);
        $update_stmt->bindParam(':id', $_SESSION['user_id']);
        $update_stmt->execute();

        $success = "Kullanıcı adı ve şifre başarıyla güncellendi.";
        $_SESSION['username'] = $new_username;
    } else {
        $error = "Lütfen tüm alanları doldurun.";
    }
}
?>

<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Kullanıcı Bilgilerini Güncelle</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="/adminpanel/">Kontrol Paneli</a></li>
                                <li class="breadcrumb-item active">Kullanıcı Bilgilerini Güncelle</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-body">
                            <?php if (!empty($error)): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>
                            <?php if (!empty($success)): ?>
                                <div class="alert alert-success"><?php echo $success; ?></div>
                            <?php endif; ?>
                            <form action="" method="POST">
                                <div class="input-group mb-3">
                                    <input type="text" name="new_username" class="form-control" placeholder="Yeni Kullanıcı Adı" required>
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <span class="fas fa-user"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="input-group mb-3">
                                    <input type="password" name="new_password" class="form-control" placeholder="Yeni Şifre" required>
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <span class="fas fa-lock"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary btn-block">Güncelle</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</body>
</html>