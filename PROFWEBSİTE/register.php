<?php
// register.php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

// Input verilerini al
$username = trim($input['username'] ?? '');
$email    = trim($input['email'] ?? '');
$password = trim($input['password'] ?? '');
$fullname = trim($input['fullname'] ?? $username);

// Boş alan kontrolü
if (!$username || !$email || !$password) {
    sendJson(['error' => 'Lütfen tüm alanları doldurun.'], 400);
}

// E-posta veya Kullanıcı adı çakışması kontrolü
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
$stmt->execute([$email, $username]);
if ($stmt->rowCount() > 0) {
    sendJson(['error' => 'Bu e-posta veya kullanıcı adı zaten kullanımda.'], 409);
}

// Şifreyi Hashle
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// Veritabanına Ekle
$stmt = $pdo->prepare("INSERT INTO users (username, full_name, email, password) VALUES (?, ?, ?, ?)");
if ($stmt->execute([$username, $fullname, $email, $passwordHash])) {
    sendJson(['success' => true, 'message' => 'Kayıt başarılı. Giriş yapabilirsiniz.']);
} else {
    sendJson(['error' => 'Kayıt sırasında bir hata oluştu.'], 500);
}
?>