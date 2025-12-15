<?php
// save.php
// Beğeni ve Kaydetme işlemlerini yönetir

$userId = requireLogin(); // Giriş yapmış kullanıcı ID'si
$postId = $input['post_id'] ?? 0;
$subAction = $input['action'] ?? ''; // 'like', 'unlike', 'save', 'unsave'

if (!$postId) sendJson(['error' => 'Post ID gerekli'], 400);

// --- BEĞENİ İŞLEMİ ---
if ($action === 'toggle_like') {
    if ($subAction === 'like') {
        // Beğeni ekle (Çakışma varsa yoksay - IGNORE)
        $pdo->prepare("INSERT IGNORE INTO likes (user_id, post_id) VALUES (?, ?)")->execute([$userId, $postId]);
        // Post tablosundaki sayacı artır
        $pdo->prepare("UPDATE posts SET likes = likes + 1 WHERE id = ?")->execute([$postId]);
        $state = 'liked';
    } else {
        // Beğeni sil
        $pdo->prepare("DELETE FROM likes WHERE user_id = ? AND post_id = ?")->execute([$userId, $postId]);
        // Post tablosundaki sayacı azalt (0'ın altına düşmesin)
        $pdo->prepare("UPDATE posts SET likes = GREATEST(0, likes - 1) WHERE id = ?")->execute([$postId]);
        $state = 'unliked';
    }
    
    // Güncel beğeni sayısını çekip geri döndür
    $stmt = $pdo->prepare("SELECT likes FROM posts WHERE id = ?");
    $stmt->execute([$postId]);
    $count = $stmt->fetchColumn();
    
    sendJson(['success' => true, 'new_state' => $state, 'new_likes_count' => $count]);
}

// --- KAYDETME İŞLEMİ ---
if ($action === 'toggle_save') {
    if ($subAction === 'save') {
        $pdo->prepare("INSERT IGNORE INTO saved_posts (user_id, post_id) VALUES (?, ?)")->execute([$userId, $postId]);
        $state = 'saved';
    } else {
        $pdo->prepare("DELETE FROM saved_posts WHERE user_id = ? AND post_id = ?")->execute([$userId, $postId]);
        $state = 'unsaved';
    }
    sendJson(['success' => true, 'new_state' => $state]);
}
?>