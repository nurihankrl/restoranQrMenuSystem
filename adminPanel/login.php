<?php
session_start();
include '../inc/db.php';

// Redirect to admin panel if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ./');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';

  if (!empty($username) && !empty($password)) {
    $stmt = $db->prepare("SELECT id, username, password FROM admins WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
      session_name('SESSİONDİYORUMSANAKARULQROIJPQRKŞLQW');
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['username'] = $user['username'];
      header('Location: ./');
      exit;
    } else {
      $error = "Yanlış kullanıcı adı veya şifre.";
    }
  } else {
    $error = "Lütfen tüm alanları doldurun.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Panel | Restoran QR Menü</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css?v=3.2.0">
  <link rel="icon" href="https://i.hizliresim.com/4zxqv98.png" type="image/png">
</head>
<body class="hold-transition login-page dark-mode">
<div class="login-box">
  <div class="login-logo">
    <b style="color: #ff5733;">Restoran QR Menü </b><br>
    <small>Admin Panel</small>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <p class="login-box-msg">Oturum açmak için giriş yapın</p>

      <form action="" method="POST">
        <div class="input-group mb-3">
          <input type="text" name="username" class="form-control" placeholder="Kullanıcı Adı" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="Şifre" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="remember">
              <label for="remember">
                Beni Hatırla
              </label>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Giriş Yap</button>
          </div>
          <!-- /.col -->
        </div>
      </form>


    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js?v=3.2.0"></script>
</body>
</html>
