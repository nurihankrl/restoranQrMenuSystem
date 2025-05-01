<?php
// Bağlantı bilgileri
$servername = "localhost";
$username = "root";
$password = "";
$default_db = "test"; // varsayılan veritabanı

// İlk bağlantı (db seçmeden)
$conn = new mysqli($servername, $username, $password);

// Hata kontrolü
if ($conn->connect_error) {
    die("Bağlantı Hatası: " . $conn->connect_error);
}

// Tüm veritabanlarını çek
$databases = [];
$db_result = $conn->query("SHOW DATABASES");
if ($db_result) {
    while ($row = $db_result->fetch_assoc()) {
        $databases[] = $row['Database'];
    }
}

$conn->close();

// Form gönderildi mi kontrolü
$resultMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["sql_query"])) {
    $selected_db = isset($_POST["database"]) && !empty($_POST["database"]) ? $_POST["database"] : $default_db;

    // Seçilen veritabanına bağlan
    $conn = new mysqli($servername, $username, $password, $selected_db);

    if ($conn->connect_error) {
        die("Veritabanı Bağlantı Hatası: " . $conn->connect_error);
    }

    $sql = $_POST["sql_query"];

    if ($conn->multi_query($sql)) {
        do {
            if ($result = $conn->store_result()) {
                $resultMessage .= "<h3>Sorgu Sonucu:</h3><table border='1' cellpadding='5' cellspacing='0'><tr>";

                while ($field = $result->fetch_field()) {
                    $resultMessage .= "<th>" . htmlspecialchars($field->name) . "</th>";
                }
                $resultMessage .= "</tr>";

                while ($row = $result->fetch_assoc()) {
                    $resultMessage .= "<tr>";
                    foreach ($row as $data) {
                        $resultMessage .= "<td>" . htmlspecialchars($data) . "</td>";
                    }
                    $resultMessage .= "</tr>";
                }
                $resultMessage .= "</table>";
                $result->free();
            } else {
                $resultMessage .= "<p>Sorgu başarılı: " . htmlspecialchars($sql) . "</p>";
            }
        } while ($conn->more_results() && $conn->next_result());
    } else {
        $resultMessage = "<div style='color:red;'>Hata oluştu: " . $conn->error . "</div>";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>SQL Sorgu Çalıştırıcı</title>

    <!-- CodeMirror CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/theme/eclipse.min.css">

    <!-- CodeMirror JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/mode/sql/sql.min.js"></script>

    <script>
    function toggleDatabaseSelect() {
        var checkbox = document.getElementById('use_db');
        var dbSelect = document.getElementById('database_select');
        if (checkbox.checked) {
            dbSelect.style.display = 'block';
        } else {
            dbSelect.style.display = 'none';
        }
    }

    window.onload = function() {
        window.editor = CodeMirror.fromTextArea(document.getElementById("sql_query"), {
            mode: "text/x-sql",
            theme: "eclipse",
            lineNumbers: true,
            matchBrackets: true,
            autofocus: true
        });
    };
    </script>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
        }
        .CodeMirror {
            border: 1px solid #ccc;
            height: auto;
        }
    </style>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 30px;
    }
    .CodeMirror {
        border: 1px solid #ccc;
        height: auto;
    }
    /* SQL anahtar kelimelerini belirgin yap */
    .cm-keyword {
        color: #d60000 !important; /* parlak kırmızı */
        font-weight: bold;
    }
</style>

</head>
<body>
    <h1>SQL Sorgusu Çalıştır</h1>

    <form method="post" onsubmit="document.getElementById('sql_query').value = editor.getValue();">
        <label>
            <input type="checkbox" id="use_db" onchange="toggleDatabaseSelect()"> Database Seç
        </label><br><br>

        <div id="database_select" style="display:none;">
            <label for="database">Veritabanı Seçin:</label>
            <select name="database" id="database">
                <?php foreach ($databases as $db): ?>
                    <option value="<?php echo htmlspecialchars($db); ?>"><?php echo htmlspecialchars($db); ?></option>
                <?php endforeach; ?>
            </select><br><br>
        </div>

        <textarea id="sql_query" name="sql_query" rows="10" cols="80" placeholder="SQL sorgunuzu buraya yazın..."></textarea><br><br>
        <button type="submit">Sorguyu Çalıştır</button>
    </form>

    <div style="margin-top:20px;">
        <?php echo $resultMessage; ?>
    </div>
</body>
</html>
