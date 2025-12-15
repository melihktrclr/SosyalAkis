<?php
// comment.php
$method = $_SERVER['REQUEST_METHOD'];

if ($action === 'add_comment' && $method === 'POST') {
    $userId = requireLogin();
    $postId = $input['post_id'] ?? 0;
    $content = $input['content'] ?? '';

    if (!$postId || !$content) sendJson(['error' => 'Eksik veri.'], 400);

    $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
    if ($stmt->execute([$postId, $userId, $content])) {
        sendJson(['success' => true]);
    } else {
        sendJson(['error' => 'Yorum eklenemedi.'], 500);
    }
}

if ($action === 'get_comments' && $method === 'GET') {
    $postId = $_GET['post_id'] ?? 0;
    $stmt = $pdo->prepare("
        SELECT c.*, u.username, u.full_name, u.profile_picture_url 
        FROM comments c 
        JOIN users u ON c.user_id = u.id 
        WHERE c.post_id = ? 
        ORDER BY c.created_at ASC
    ");
    $stmt->execute([$postId]);
    $comments = $stmt->fetchAll();

    $formatted = array_map(function($c) {
        return [
            'id' => $c['id'],
            'user_id' => $c['user_id'],
            'content' => $c['content'],
            'author_name' => $c['full_name'] ?: $c['username'],
            'author_handle' => '@' . $c['username'],
            'author_pp' => $c['profile_picture_url'],
            'author_initial' => strtoupper(substr($c['username'], 0, 1)),
            'created_at' => strtotime($c['created_at'])
        ];
    }, $comments);

    sendJson(['success' => true, 'comments' => $formatted]);
}
?>