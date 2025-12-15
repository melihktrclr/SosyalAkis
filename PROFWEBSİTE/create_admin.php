<?php
// create_admin.php
require 'db.php';

$username = "admin";
$email = "admin@gmail.com";
$password = "12345"; // Şifren bu olacak
$fullname = "Süper Admin";

// Şifreyi güvenli hale getir (Hash'le)
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

try {
    // Önce varsa eski admini sil (Çakışma olmasın)
    $pdo->exec("DELETE FROM users WHERE email = '$email'");

    // Yeni kullanıcıyı ekle (Role: admin olarak)
    $stmt = $pdo->prepare("INSERT INTO users (username, full_name, email, password, role) VALUES (?, ?, ?, ?, 'admin')");
    $stmt->execute([$username, $fullname, $email, $passwordHash]);

    echo "<h1>Başarılı!</h1>";
    echo "Kullanıcı oluşturuldu.<br>";
    echo "Email: <b>$email</b><br>";
    echo "Şifre: <b>$password</b><br><br>";
    echo "<a href='index.html'>Giriş Yapmak İçin Tıkla</a>";

} catch (PDOException $e) {
    echo "Hata: " . $e->getMessage();
}
?>