<?php

?>

<!DOCTYPE ahtml>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SosyalAkış</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="./style.css">
</head>
<body class="min-h-screen flex flex-col lg:flex-row">
    
    <!-- 1. Masaüstü Yan Menü (Menu & Profil Kısmı) -->
    <aside id="desktop-nav" class="hidden lg:flex lg:w-64 bg-white border-r border-gray-200 fixed lg:sticky top-0 h-screen p-4 flex-col shadow-lg z-10">
        <!-- Logo artık tıklanabilir ve ana sayfaya yönlendiriyor -->
        <a href="#" onclick="changeView('feed'); return false;" class="text-2xl font-black text-blue-600 mb-8 p-2 cursor-pointer hover:opacity-80 transition duration-150">SosyalAkış</a>

        <nav id="sidebar-menu" class="flex flex-col space-y-3 flex-grow">
        </nav>

        <div id="sidebar-profile" class="mt-8">
        </div>

    </aside>

    <!-- 2. Ana İçerik Alanı -->
    <main class="flex-grow p-4 lg:p-8 pt-16 lg:pt-8 overflow-y-auto w-full pb-20">
        <div class="max-w-xl mx-auto">
            <h1 class="text-3xl font-extrabold text-gray-800 mb-6 hidden lg:block text-center" id="main-title">ANA AKIŞ</h1>

            <!-- Mobil Üst Başlık (lg:hidden) -->
            <header class="fixed top-0 left-0 right-0 bg-white border-b border-gray-200 p-3 flex justify-between items-center z-20 lg:hidden shadow-md">
                <div class="text-xl font-black text-blue-600">Sosyal Akış</div>
                <div class="flex items-center space-x-3">
                    <button id="mobile-profile-button" onclick="viewProfile(currentUser.id)" class="hidden w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold ring-2 ring-blue-500 ring-offset-2">
                    </button>
                    <!-- mobile-post-button kaldırıldı -->
                    <button id="mobile-auth-button" onclick="showAuthModal('login')" class="bg-blue-600 text-white text-sm font-semibold py-1 px-3 rounded-full hover:bg-blue-700 transition duration-150">
                      GİRİŞ
                    </button>
                </div>
            </header>
            
            <!-- İçerik Alanı (Akış veya Karşılama Ekranı) -->
            <div id="content-area" class="content-active">
            </div>
            
        </div>
    </main>

    <!-- 3. Mobil Alt Navigasyon (lg:hidden) -->
    <nav id="mobile-nav" class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-3 flex justify-around items-center z-30 lg:hidden shadow-[0_-5px_15px_rgba(0,0,0,0.05)]">
    </nav>
    
    <!-- 4. Giriş/Üye Ol Modal'ı -->
    <div id="auth-modal" class="fixed inset-0 bg-black bg-opacity-70 hidden items-center justify-center p-4 z-40 modal-bg" onclick="hideAuthModal(event)">
        <div class="bg-white rounded-xl w-full max-w-sm p-6 shadow-2xl modal-content-box" onclick="event.stopPropagation()">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-2xl font-bold text-gray-800" id="auth-modal-title">GİRİŞ YAP</h3>
                <button onclick="hideAuthModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            
            <!-- Giriş Formu -->
            <form id="login-form" class="space-y-4" action="">
                <input type="email" id="login-email" placeholder="E-posta" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-800">
                <input type="password" id="login-password" placeholder="Şifre" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-800" >
                <button type="submit" onclick="loginUser()" class="w-full bg-blue-600 text-white font-bold py-3 rounded-full hover:bg-blue-700 transition duration-150">
                 GİRİŞ YAP
                </button>
            </form>

            <!-- Kayıt Formu (Başlangıçta Gizli) -->
            <form id="signup-form" class="space-y-4 hidden">
                <input type="text" id="signup-username" placeholder="Kullanıcı Adı" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-800">
                <input type="email" id="signup-email" placeholder="E-posta" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-800">
                <input type="password" id="signup-password" placeholder="Şifre" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-800">
                <input type="text" id="signup-fullname" placeholder="Adınız Soyadınız" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-800">
                <button type="button" onclick="signupUser()" class="w-full bg-black text-white font-bold py-3 rounded-full hover:bg-gray-800 transition duration-150">
                   ÜYE OL
                </button>
            </form>

            <p class="text-center text-sm mt-4">
                <span id="auth-switch-text">Hesabın yok mu?</span>
                <button type="button" id="auth-switch-button" onclick="toggleAuthMode()" class="text-blue-600 font-semibold hover:underline transition">
                    ÜYE OL
                </button>
            </p>
        </div>
    </div>
    
    <!-- 5. Gönderi Atma Modal'ı (Sadece Giriş Yapınca Görünür) -->
    <div id="post-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center p-4 z-40 modal-bg" onclick="hidePostModal(event)">
        <div class="bg-white rounded-xl w-full max-w-md p-6 shadow-2xl modal-content-box" onclick="event.stopPropagation()">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800" id="post-modal-title">Yeni Gönderi Oluştur</h3>
                <button onclick="hidePostModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <textarea id="modal-post-input" class="w-full h-32 p-3 text-gray-700 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 resize-none" placeholder="Aklınızdakini yazın..."></textarea>
            
            <!-- Gönderi Resmi/Video Alanı -->
            <div id="post-media-preview-area" class="mt-3 p-2 border-dashed border-2 border-gray-300 rounded-lg hidden">
                <div id="media-preview-container" class="w-full h-auto max-h-48 mb-2">
                    <!-- Media preview goes here -->
                </div>
                <div class="flex justify-between items-center text-sm">
                    <button type="button" onclick="triggerPostFileSelect()" class="px-3 py-1 bg-gray-200 text-gray-700 rounded-full hover:bg-gray-300 transition flex items-center space-x-1">
                        <i data-lucide="image" class="w-4 h-4"></i>
                        <span>Medya Değiştir</span>
                    </button>
                    <button type="button" onclick="removePostMedia()" class="px-3 py-1 text-red-600 hover:text-red-700 transition">
                        <i data-lucide="trash" class="w-4 h-4 inline mr-1"></i> Kaldır
                    </button>
                </div>
            </div>
            
            <div class="flex justify-between items-center mt-4 space-x-3">
                <!-- Sol Tarafa Resim/Video Seçme Butonu ve Gizli Input -->
                <button type="button" id="select-post-image-button" onclick="triggerPostFileSelect()" class="p-2 text-blue-600 hover:bg-blue-50 rounded-full transition">
                    <i data-lucide="camera" class="w-6 h-6"></i>
                </button>
                <!-- DÜZELTME: accept="image/*,video/*" olarak güncellendi -->
                <input type="file" id="post-image-input" accept="image/*,video/*" class="hidden">


                <div class="flex space-x-3">
                    <button onclick="hidePostModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-full hover:bg-gray-200 transition">
                        İptal
                    </button>
                    <button id="modal-post-button" onclick="postFromModal()" class="px-6 py-2 text-white bg-blue-600 rounded-full font-semibold hover:bg-blue-700 transition">
                        Paylaş
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 6. Profil Düzenleme Modal'ı -->
    <div id="profile-edit-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center p-4 z-40 modal-bg" onclick="hideProfileEditModal(event)">
        <div class="bg-white rounded-xl w-full max-w-md p-6 shadow-2xl modal-content-box" onclick="event.stopPropagation()">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Profili Düzenle</h3>
                <button onclick="hideProfileEditModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            
            <!-- Fotoğraf Yükleme/Değiştirme Alanı -->
            <div class="flex items-center space-x-4 mb-6 p-3 bg-gray-50 rounded-lg">
                <div id="edit-pp-display" class="w-16 h-16 rounded-full flex items-center justify-center text-lg font-bold border-2 border-gray-300 overflow-hidden">
                    <!-- İçerik JS ile eklenecek -->
                </div>
                <button type="button" onclick="triggerFileSelect()" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-full hover:bg-blue-700 transition flex items-center space-x-1">
                    <i data-lucide="image" class="w-4 h-4"></i>
                    <span>Fotoğraf Seç / Değiştir</span>
                </button>
            </div>
            <!-- Gizli Dosya Girişi -->
            <input type="file" id="profile-picture-input" accept="image/*" class="hidden">
            
            <div class="space-y-4">
                <input type="text" id="edit-name" placeholder="Ad Soyad" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-800">
                <input type="text" id="edit-username" placeholder="Kullanıcı Adı (@ olmadan)" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-800">
                
                <!-- Biyografi Alanı -->
                <textarea id="edit-bio" placeholder="Biyografi (Maks 150 karakter)" rows="3" maxlength="150" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-800 resize-none"></textarea>

                <!-- Link Alanı -->
                <input type="url" id="edit-url" placeholder="Web Sitesi Linki (Örn: https://)" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-800">
            </div>
            
            <div class="flex justify-end mt-6 space-x-3">
                <button onclick="hideProfileEditModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-full hover:bg-gray-200 transition">
                    İptal
                </button>
                <button onclick="updateProfile()" class="px-6 py-2 text-white bg-blue-600 rounded-full font-semibold hover:bg-blue-700 transition">
                    Kaydet
                </button>
            </div>
        </div>
    </div>

    <!-- 7. Profil Fotoğrafı Büyütme Modal'ı -->
    <div id="picture-modal" class="fixed inset-0 bg-black bg-opacity-90 hidden items-center justify-center p-4 z-50 modal-bg" onclick="hidePictureModal(event)">
        <div class="modal-content-box" onclick="event.stopPropagation()">
            <button onclick="hidePictureModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 transition p-2 rounded-full bg-black/50">
                <i data-lucide="x" class="w-8 h-8"></i>
            </button>
            <img id="modal-picture-img" src="" alt="Profil Resmi" class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl">
        </div>
    </div>


    <script src="./app.js"></script>
</body>
</html>
