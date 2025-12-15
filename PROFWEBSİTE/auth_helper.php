<?php
// auth_helper.php

function sendJson($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

function requireLogin() {
    $userId = getUserId();
    if (!$userId) {
        sendJson(['error' => 'Bu işlem için giriş yapmalısınız.'], 401);
    }
    return $userId;
}
?>