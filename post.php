<?php
// post.php

function createPost($pdo, $user_id, $data) {
    $content = $data['content'] ?? '';
    $media_url = $data['media_url'] ?? null;

    if (empty($content) && empty($media_url)) {
        throw new Exception("Gönderi içeriği boş olamaz.");
    }

    $stmt = $pdo->prepare("INSERT INTO posts (user_id, content, media_url) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $content, $media_url]);
    $post_id = $pdo->lastInsertId();

    // Yeni postu çekip döndür
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $new_post = $stmt->fetch();
    
    echo json_encode(['success' => true, 'post' => $new_post]);
    exit;
}

function updatePost($pdo, $user_id, $data) {
    $post_id = $data['post_id'] ?? 0;
    $content = $data['content'] ?? '';
    $media_url = $data['media_url'] ?? null;

    if (empty($content) && empty($media_url)) {
        throw new Exception("Gönderi içeriği boş olamaz.");
    }

    // Yetki kontrolü: Sadece kendi postunu düzenleyebilir
    $stmt_check = $pdo->prepare("SELECT user_id FROM posts WHERE id = ?");
    $stmt_check->execute([$post_id]);
    $post = $stmt_check->fetch();

    if (!$post || $post['user_id'] != $user_id) {
        throw new Exception("Yetkisiz erişim veya gönderi bulunamadı.", 403);
    }

    $stmt = $pdo->prepare("UPDATE posts SET content = ?, media_url = ? WHERE id = ?");
    $stmt->execute([$content, $media_url, $post_id]);

    echo json_encode(['success' => true, 'message' => 'Gönderi başarıyla güncellendi.']);
    exit;
}

function deletePost($pdo, $user_id, $data) {
    $post_id = $data['post_id'] ?? 0;

    // Yetki kontrolü: Sadece kendi postunu silebilir
    $stmt_check = $pdo->prepare("SELECT user_id FROM posts WHERE id = ?");
    $stmt_check->execute([$post_id]);
    $post = $stmt_check->fetch();
    $user_role = get_user_role($pdo, $user_id);

    // Admin/Editör herkesin postunu silebilir, diğerleri sadece kendininkini
    if (!$post || ($post['user_id'] != $user_id && $user_role !== 'admin' && $user_role !== 'editor')) {
        throw new Exception("Yetkisiz erişim veya gönderi bulunamadı.", 403);
    }
    
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);

    echo json_encode(['success' => true, 'message' => 'Gönderi başarıyla silindi.']);
    exit;
}

function getFeedPosts($pdo, $current_user_id, $data) {
    $sql = "SELECT p.*, u.full_name, u.username, u.profile_picture_url, 
            (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id) as likes_count
            FROM posts p
            JOIN users u ON u.id = p.user_id
            ORDER BY p.created_at DESC";
            
    $stmt = $pdo->query($sql);
    $posts = $stmt->fetchAll();

    // Beğeni/Kaydetme durumlarını ekle (Çoklu sorgu yavaşlatabilir, JOIN ile optimize edilebilir)
    foreach ($posts as &$post) {
        $post['is_liked'] = false;
        $post['is_saved'] = false;
        $post['initial'] = strtoupper(substr($post['full_name'], 0, 1));
        
        if ($current_user_id) {
            $stmt_like = $pdo->prepare("SELECT 1 FROM likes WHERE user_id = ? AND post_id = ?");
            $stmt_like->execute([$current_user_id, $post['id']]);
            $post['is_liked'] = (bool)$stmt_like->fetch();

            $stmt_save = $pdo->prepare("SELECT 1 FROM saved_posts WHERE user_id = ? AND post_id = ?");
            $stmt_save->execute([$current_user_id, $post['id']]);
            $post['is_saved'] = (bool)$stmt_save->fetch();
        }

        // JS'in beklediği formatları ekle (Mock uyumluluğu için)
        $post['author'] = $post['full_name'];
        $post['handle'] = '@' . $post['username'];
        $post['likes'] = $post['likes_count'];
        $post['created_at'] = strtotime($post['created_at']); // JS Unix Timestamp bekler
    }

    echo json_encode(['posts' => $posts]);
    exit;
}
?>