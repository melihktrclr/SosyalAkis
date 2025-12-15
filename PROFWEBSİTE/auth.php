<?php
// auth.php

require_once 'auth_helper.php'; 

function loginUser($pdo, $data) {
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    // Kullanıcının yasaklanma bitiş zamanını tutacak değişkeni tanımla
    $user_ban_end_time = 0; 

    if (empty($email) || empty($password)) {
        throw new Exception("E-posta ve şifre gerekli.");
    }

    $stmt = $pdo->prepare("SELECT id, full_name, username, password_hash, bio, website_url, profile_picture_url, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        
        // Ban kontrolü yap
        // is_banned fonksiyonu artık $user_ban_end_time değişkenini referans (&) ile alıyor
        if (is_banned($pdo, $user['id'], $user_ban_end_time)) {
            throw new Exception("Hesabınız geçici olarak yasaklanmıştır.");
        }
        
        // Simülasyon için oturum tokeni oluştur
        $token = generate_auth_token($user['id']);
        $_SESSION['auth_token'] = $token;
        
        echo json_encode([
            'token' => $token,
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
        exit;
    }
    
    throw new Exception("Geçersiz e-posta veya şifre.");
}

// Bu fonksiyonu kullanmayacağız, JS'teki simulateLogout yeterli
function logoutUser() {
    session_destroy();
    header("Location: index.php"); 
    exit;
}

// NOT: Kayıt (signup) mantığı da buraya eklenebilir.
?>