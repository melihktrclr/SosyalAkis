<?php
// api.php
header('Content-Type: application/json; charset=utf-8');
session_start();

// Hata gösterimini kapat (JSON çıktısını bozmamak için)
ini_set('display_errors', 0);
error_reporting(E_ALL);

require 'db.php';
require 'auth_helper.php';

// Frontend'den gelen JSON verisini al
$input = json_decode(file_get_contents('php://input'), true);
$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'login':
            require 'login.php';
            break;
            
        case 'register': // İsteğe bağlı, frontend kullanırsa
            require 'register.php';
            break;

        case 'feed':
            $userId = getUserId();
            // Feed SQL sorgusu
            $sql = "SELECT p.*, u.username, u.full_name, u.profile_picture_url,
                    (SELECT COUNT(*) FROM likes WHERE post_id = p.id AND user_id = ?) as is_liked,
                    (SELECT COUNT(*) FROM saved_posts WHERE post_id = p.id AND user_id = ?) as is_saved
                    FROM posts p 
                    JOIN users u ON p.user_id = u.id 
                    ORDER BY p.created_at DESC";
            
            $stmt = $pdo->prepare($sql);
            // Eğer giriş yapılmamışsa user_id yerine 0 gönder (hata almamak için)
            $checkId = $userId ?: 0;
            $stmt->execute([$checkId, $checkId]);
            $posts = $stmt->fetchAll();

            $formatted = [];
            foreach($posts as $p) {
                $formatted[] = [
                    'id' => $p['id'],
                    'user_id' => $p['user_id'],
                    'author' => $p['full_name'] ?: $p['username'],
                    'handle' => '@' . $p['username'],
                    'profilePicture' => $p['profile_picture_url'],
                    'initial' => strtoupper(substr($p['username'], 0, 1)),
                    'content' => $p['content'],
                    'media_url' => $p['media_url'],
                    'likes' => $p['likes'],
                    'is_liked' => (bool)$p['is_liked'],
                    'is_saved' => (bool)$p['is_saved'],
                    'created_at' => strtotime($p['created_at'])
                ];
            }
            sendJson(['success' => true, 'posts' => $formatted]);
            break;

        case 'create_post':
            $userId = requireLogin();
            $content = $input['content'] ?? '';
            $media   = $input['media_url'] ?? null;
            
            $stmt = $pdo->prepare("INSERT INTO posts (user_id, content, media_url) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $content, $media]);
            $postId = $pdo->lastInsertId();
            
            // Yeni post verisini hemen geri dön
            $stmt = $pdo->prepare("SELECT username, full_name, profile_picture_url FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $u = $stmt->fetch();
            
            sendJson([
                'success' => true,
                'post' => [
                    'id' => $postId,
                    'user_id' => $userId,
                    'author' => $u['full_name'] ?: $u['username'],
                    'handle' => '@' . $u['username'],
                    'profilePicture' => $u['profile_picture_url'],
                    'initial' => strtoupper(substr($u['username'], 0, 1)),
                    'content' => $content,
                    'media_url' => $media,
                    'likes' => 0,
                    'created_at' => time()
                ]
            ]);
            break;
            
        case 'delete_post':
            $userId = requireLogin();
            $postId = $input['post_id'] ?? 0;
            // Sadece kendi postunu silebilir
            $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
            if ($stmt->execute([$postId, $userId])) {
                 sendJson(['success' => true]);
            } else {
                 sendJson(['error' => 'Silme yetkiniz yok veya hata oluştu.'], 403);
            }
            break;

        case 'add_comment':
        case 'get_comments':
            require 'comment.php';
            break;

        case 'toggle_like':
        case 'toggle_save':
            require 'save.php';
            break;
            
        case 'update_profile':
        case 'follow_action':
            require 'user.php';
            break;
            
        // Admin işlemleri (Basit Örnek)
        case 'ban_user':
        case 'unban_user':
            $userId = requireLogin();
            if ($_SESSION['role'] !== 'admin') sendJson(['error' => 'Yetkisiz erişim.'], 403);
            // Ban logic buraya eklenebilir veya ayrı bir admin.php çağrılabilir
            break;

        default:
            sendJson(['error' => 'Geçersiz İşlem: ' . $action], 404);
            break;
    }

} catch (Exception $e) {
    sendJson(['error' => $e->getMessage()], 500);
}
?>