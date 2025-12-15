<?php
// user.php

// --- PROFİL GÜNCELLEME ---
if ($action === 'update_profile') {
    $userId = requireLogin();
    
    $fname = trim($input['full_name'] ?? '');
    $uname = trim($input['username'] ?? '');
    $bio   = trim($input['bio'] ?? '');
    $site  = trim($input['website_url'] ?? '');
    $pp    = $input['profile_picture_url'] ?? null;

    if (!$fname || !$uname) sendJson(['error' => 'İsim ve kullanıcı adı zorunludur.'], 400);

    $stmt = $pdo->prepare("UPDATE users SET full_name=?, username=?, bio=?, website_url=?, profile_picture_url=? WHERE id=?");
    if ($stmt->execute([$fname, $uname, $bio, $site, $pp, $userId])) {
        sendJson(['success' => true]);
    } else {
        sendJson(['error' => 'Güncelleme başarısız.'], 500);
    }
}

// --- TAKİP ETME / BIRAKMA ---
if ($action === 'follow_action') {
    $followerId = requireLogin();
    $followedId = $input['followed_id'] ?? 0;
    $subAction  = $input['action'] ?? 'follow';

    if ($followerId == $followedId) sendJson(['error' => 'Kendini takip edemezsin.'], 400);

    if ($subAction === 'follow') {
        $pdo->prepare("INSERT IGNORE INTO follows (follower_id, followed_id) VALUES (?, ?)")->execute([$followerId, $followedId]);
    } else {
        $pdo->prepare("DELETE FROM follows WHERE follower_id = ? AND followed_id = ?")->execute([$followerId, $followedId]);
    }
    sendJson(['success' => true]);
}
?>