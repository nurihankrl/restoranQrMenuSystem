<?php
ob_start();
require_once '../inc/db.php';

$maintenanceMode = $db->query("SELECT maintenance_mode FROM settings LIMIT 1")->fetchColumn();
if (!$maintenanceMode) {
    header("Location: /");
    exit;
}

$sayfaTitle = "Bakım Modu";
?>

<!DOCTYPE html>
<html lang="tr">

<head>
<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?= htmlspecialchars($sayfaTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="../assets/scss/style.css">
    <link rel="shortcut icon" href="bakimIcon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body {
    margin: 0;
    font-family: 'Poppins', Arial, sans-serif;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100vh;
    text-align: center;
    background: linear-gradient(135deg, #ff096d, #764ba2);
    background-size: cover;
    background-position: center;
    overflow: hidden;
}

h1 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    font-weight: 700;
}

p {
    font-size: 1.2rem;
    margin-bottom: 1.5rem;
    font-weight: 400;
}

img {
    max-width: 100%;
    height: auto;
}

.refresh-button {
    display: inline-block;
    margin-top: 20px;
    padding: 10px 20px;
    font-size: 1rem;
    font-weight: 600;
    color: #fff;
    background: linear-gradient(135deg, #667eea, #764ba2);
    border: none;
    border-radius: 5px;
    text-decoration: none;
    transition: background 0.3s ease;
}

.refresh-button:hover {
    background: linear-gradient(135deg, #764ba2, #667eea);
}

@media (max-width: 768px) {
    h1 {
        font-size: 2rem;
    }

    p {
        font-size: 1rem;
    }

    img {
        max-width: 85%;
    }

    .refresh-button {
        padding: 8px 16px;
        font-size: 0.9rem;
    }
}

@media (max-width: 480px) {
    h1 {
        font-size: 1.8rem;
    }

    p {
        font-size: 0.9rem;
    }

    img {
        max-width: 80%; 
    }

    .refresh-button {
        padding: 6px 12px;
        font-size: 0.8rem;
    }
}

@media (max-width: 320px) {
    h1 {
        font-size: 1.6rem;
    }

    p {
        font-size: 0.8rem;
    }

    .refresh-button {
        padding: 5px 10px;
        font-size: 0.7rem;
    }
}


</style>
</head>

<body>
    <div class="container text-center mt-5">
        <h1 class="display-4">Bakım Modu</h1>
        <p class="lead">Sitemiz şu anda bakım modunda. Lütfen daha sonra tekrar ziyaret edin.</p>
        <div class="display-8"><a href="" class="refresh-button">Yenile</a></div>
        <img src="indexImage.png" alt="Bakım Modu" class="img-fluid mt-4" style="max-width: 900px;">
        
    </div>
</body>
</html>

<?php ob_end_flush(); ?>
