<?php
require_once 'db.php';

$maintenanceMode = $db->query("SELECT maintenance_mode FROM settings LIMIT 1")->fetchColumn();
if ($maintenanceMode) {
    header("Location: /bakim/");
    exit;
}

if (!defined('GUVENLIK_PHP')) {
    define('GUVENLIK_PHP', true);

    function sanitizeInput($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    function validateId($id) {
        return is_numeric($id) ? intval($id) : null;
    }

    function preventDirectAccess() {
        if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
            header("HTTP/1.0 403 Forbidden");
            exit("Direct access to this file is not allowed.");
        }
    }

    function detectSqlInjection($input) {
        $decodedInput = urldecode($input);

        $patterns = [
            '/\b(UNION|SELECT|INSERT|UPDATE|DELETE|DROP|ALTER|CREATE|TRUNCATE|REPLACE|RENAME|SHOW|DESCRIBE|GRANT|REVOKE|EXEC)\b/i', 
            '/--/',
            '/;/', 
            '/\*/',
            '/\'|"/', 
            '/\bOR\b|\bAND\b/i', 
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $decodedInput)) {
                header("Location: /");
                exit;
            }
        }
    }

    preventDirectAccess();
}
?>
