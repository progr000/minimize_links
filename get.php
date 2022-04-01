<?php
require_once __DIR__ . '/incl/db.php';

if (!empty($_POST['source_link'])) {
    $db = new sql();
    $data = $db->findOrCreateShortLink($_POST['source_link']);
    $data['result_url'] =
        "http" . (!empty($_SERVER['HTTPS'])?"s" : "") .
        "://". $_SERVER['SERVER_NAME'] . "/" . $data['hash'];
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
} else {
    //header("HTTP/1.0 404 Not Found");
    echo "Wrong parameters";
}
