<?php
// login.php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

$email = trim($input['email'] ?? '');
$password = trim($input['password'] ?? '');

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    
    // Ban kontrolü
    if ($user['is_banned'] == 1) {
        if ($user['ban_expires_at'] > time()) {
             sendJson(['error' => 'Hesabınız yasaklanmıştır.'], 403);
        } else {
             // Ban süresi dolmuşsa kaldır
             $pdo->prepare("UPDATE users SET is_banned = 0 WHERE id = ?")->execute([$user['id']]);
        }
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];

    sendJson([
        'success' => true,
        'token' => session_id(),
        'user' => [
            'id' => $user['id'],
            'full_name' => $user['full_name'],
            'username' => $user['username'],
            'bio' => $user['bio'],
            'website_url' => $user['website_url'],
            'profile_picture_url' => $user['profile_picture_url'],
            'role' => $user['role']
        ]
    ]);
} else {
    sendJson(['error' => 'Hatalı e-posta veya şifre.'], 401);
}
?>