<?php
require_once __DIR__ . '/incl/Sql.php';

if (!empty($_GET['hash'])) {
    $db = new sql();
    $data = $db->getOriginalLink($_GET['hash']);

    if (is_array($data)) {
        header("Location: {$data['original_link']}");
        exit;
    }
}
header("HTTP/1.0 404 Not Found");
echo "Wrong url";
