<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SosyalAkış - Responsive Sosyal Medya</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        /* Ana yazı tipini ve varsayılan renk şemasını tanımla */
        :root {
            --color-primary: #3B82F6; /* Mavi (Varsayılan) */
            --color-secondary: #000000; /* Siyah */
            --color-background: #F8FAFC; /* Çok Açık Gri/Beyaz */
            --color-text: #1F2937; /* Koyu Gri/Siyah */
            --color-accent: #3B82F6; /* Mevcut Bölüm Rengi */
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--color-background);
            color: var(--color-text);
            /* GENEL YUMUŞAK GEÇİŞ */
            transition: background-color .6s ease, color .6s ease;
        }

        /* Sidebar Geçişleri */
        #desktop-nav {
            transition: width 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }

        /* Main İçerik Alanı Geçişleri */
        main {
            transition: margin-left 0.3s ease-in-out, padding-left 0.3s ease-in-out;
        }
        /* Sidebar açıkken (varsayılan) */
        @media (min-width: 1024px) {
            main.sidebar-open {
                margin-left: 256px; 
            }
            main.sidebar-closed {
                margin-left: 80px; 
            }
            #desktop-nav.sidebar-closed .nav-link span,
            #desktop-nav.sidebar-closed #sidebar-profile,
            #desktop-nav.sidebar-closed #post-button-text,
            #desktop-nav.sidebar-closed #sidebar-profile a > div,
            #desktop-nav.sidebar-closed #dark-mode-toggle span,
            #desktop-nav.sidebar-closed #sidebar-profile button:not(#dark-mode-toggle) i:not(#toggle-icon) + * { 
                display: none;
            }
            #desktop-nav.sidebar-closed #dark-mode-toggle { 
                justify-content: center;
                width: 56px;
                padding-left: 10px;
                padding-right: 10px;
            }
            #desktop-nav.sidebar-closed .nav-link {
                justify-content: center; 
                padding: 0.75rem;
            }
            #desktop-nav.sidebar-closed .nav-link i {
                margin-right: 0 !important;
            }
            #desktop-nav.sidebar-closed #sidebar-menu button {
                border-radius: 9999px; 
                width: 56px;
                padding: 0.75rem;
                margin-left: auto;
                margin-right: auto;
            }
            #desktop-nav.sidebar-closed #sidebar-menu button i {
                margin-right: 0 !important;
            }
            #desktop-nav.sidebar-closed #logo-full {
                display: none;
            }
            #desktop-nav.sidebar-closed #logo-icon {
                display: block;
            }
            #desktop-nav.sidebar-closed #sidebar-profile a.nav-link { 
                justify-content: center;
                padding-left: 10px;
                padding-right: 10px;
            }
        }

        /* Navigasyon: Seçili bölüme göre renk değiştirme */
        .nav-link.active i, .nav-link.active span {
            color: var(--color-accent) !important;
        }
        .nav-link:hover i, .nav-link:hover span {
            color: var(--color-accent);
        }

        /* Post aksiyonları */
        .post-action {
            transition: transform 0.1s ease-in-out;
        }
        .post-action:active {
            transform: scale(0.95);
        }

        /* Modal Geçişleri */
        .modal-bg {
            transition: opacity 0.3s ease-in-out;
            opacity: 0;
        }
        .modal-content-box {
            transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out;
            transform: translateY(20px);
            opacity: 0;
        }
        .modal-bg.active {
            opacity: 1;
        }
        .modal-bg.active .modal-content-box {
            transform: translateY(0);
            opacity: 1;
        }
        
        /* Dark Mode İyileştirmeleri */
        .dark {
            --color-background: #0F172A; 
            --color-text: #E2E8F0; 
        }
        .dark .bg-white, 
        .dark #desktop-nav, 
        .dark header, 
        .dark #mobile-nav {
            background-color: #1E293B !important; 
            color: #E2E8F0 !important;
            transition: background-color 0.3s ease, color 0.3s ease; 
        }
        .dark .bg-gray-50 {
            background-color: #334155 !important; 
        }
        .dark .border-gray-200 {
            border-color: #334155 !important; 
        }
        .dark .shadow-md, .dark .shadow-lg, .dark .shadow-xl, .dark .shadow-2xl {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.5), 0 2px 4px -2px rgba(0, 0, 0, 0.5) !important;
        }
        .dark .text-gray-800 {
            color: #F8FAFC !important; 
        }
        .dark .text-gray-700 {
            color: #CBD5E1 !important; 
        }
        .dark .text-gray-500 {
            color: #94A3B8 !important; 
        }
        .dark .hover\:bg-gray-100:hover {
            background-color: #334155 !important;
        }
        .dark input, .dark textarea {
            background-color: #1E293B !important;
            border-color: #475569 !important;
            color: #F8FAFC !important;
        }
        .fill-teal-600 { fill: #2DD4BF !important; } 
        .text-teal-600 { color: #2DD4BF !important; }
        .border-teal-600 { border-color: #2DD4BF !important; }
    </style>
</head>
<body class="min-h-screen flex flex-col lg:flex-row">
    
        <aside id="desktop-nav" class="hidden lg:flex lg:w-64 sidebar-open bg-white border-r border-gray-200 fixed lg:sticky top-0 h-screen p-4 flex-col shadow-lg z-10">
        <div class="flex items-center justify-between mb-8 p-2">
            <a href="#" onclick="changeView('feed'); return false;" class="text-3xl font-black text-black cursor-pointer hover:opacity-80 transition duration-150">
                <span id="logo-full">SosyalAkış</span>
                <span id="logo-icon" class="hidden text-xl">SA</span>
            </a>
            <button id="sidebar-toggle" onclick="toggleSidebar()" class="text-gray-500 hover:text-black p-2 rounded-full hover:bg-gray-100 transition duration-150 ml-auto flex-shrink-0">
                <i data-lucide="chevrons-left" class="w-6 h-6" id="toggle-icon"></i>
            </button>
        </div>

        <nav id="sidebar-menu" class="flex flex-col space-y-3 flex-grow">
            </nav>

        <div id="sidebar-profile" class="mt-8">
            </div>

    </aside>

        <main id="main-content" class="flex-grow p-4 lg:p-8 pt-16 lg:pt-8 overflow-y-auto w-full pb-20 sidebar-open">
        <div class="max-w-xl mx-auto">
            <h1 class="text-3xl font-extrabold text-gray-800 mb-6 hidden lg:block text-center" id="main-title">ANA AKIŞ</h1>

            <header class="fixed top-0 left-0 right-0 bg-white border-b border-gray-200 p-3 flex justify-between items-center z-20 lg:hidden shadow-md">
                <div class="text-xl font-black text-black">Sosyal Akış</div>
                <div class="flex items-center space-x-3">
                    <button id="mobile-profile-button" onclick="viewProfile(currentUser.id)" class="hidden w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold ring-2 ring-blue-500 ring-offset-2">
                        </button>
                    <button id="mobile-auth-button" onclick="showAuthModal('login')" class="bg-black text-white text-sm font-semibold py-1 px-3 rounded-full hover:bg-gray-800 transition duration-150">
                        GİRİŞ
                    </button>
                </div>
            </header>
            
            <div id="content-area" class="content-active">
                </div>
            
        </div>
    </main>

    <nav id="mobile-nav" class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-3 flex justify-around items-center z-30 lg:hidden shadow-[0_-5px_15px_rgba(0,0,0,0.05)]">
        </nav>
    
    <div id="auth-modal" class="fixed inset-0 bg-black bg-opacity-70 hidden items-center justify-center p-4 z-40 modal-bg" onclick="hideAuthModal(event)">
        <div class="bg-white rounded-xl w-full max-w-sm p-6 shadow-2xl modal-content-box" onclick="event.stopPropagation()">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-2xl font-bold text-gray-800" id="auth-modal-title">GİRİŞ YAP</h3>
                <button onclick="hideAuthModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            
            <form id="login-form" class="space-y-4" onsubmit="loginUser(event); return false;">
                <input type="email" id="login-email" placeholder="E-posta" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-black focus:border-black text-gray-800">
                <input type="password" id="login-password" placeholder="Şifre" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-black focus:border-black text-gray-800" >
                <button type="submit" class="w-full bg-black text-white font-bold py-3 rounded-full hover:bg-gray-800 transition duration-150">
                    GİRİŞ YAP
                </button>
            </form>

            <form id="signup-form" class="space-y-4 hidden" onsubmit="signupUser(event); return false;">
                <input type="text" id="signup-username" placeholder="Kullanıcı Adı..." class="w-full p-3 border border-gray-300 rounded-lg focus:ring-black focus:border-black text-gray-800" >
                <input type="email" id="signup-email" placeholder="E-posta...." class="w-full p-3 border border-gray-300 rounded-lg focus:ring-black focus:border-black text-gray-800" >
                <input type="password" id="signup-password" placeholder="Şifre..." class="w-full p-3 border border-gray-300 rounded-lg focus:ring-black focus:border-black text-gray-800" >
                <input type="text" id="signup-fullname" placeholder="Adınız Soyadınız..." class="w-full p-3 border border-gray-300 rounded-lg focus:ring-black focus:border-black text-gray-800">
                <button type="submit" class="w-full bg-black text-white font-bold py-3 rounded-full hover:bg-gray-800 transition duration-150">
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
    
    <div id="post-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center p-4 z-40 modal-bg" onclick="hidePostModal(event)">
        <div class="bg-white rounded-xl w-full max-w-md p-6 shadow-2xl modal-content-box" onclick="event.stopPropagation()">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800" id="post-modal-title">Yeni Gönderi Oluştur</h3>
                <button onclick="hidePostModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <textarea id="modal-post-input" class="w-full h-32 p-3 text-gray-700 border border-gray-300 rounded-lg focus:ring-[var(--color-accent)] focus:border-[var(--color-accent)] resize-none" placeholder="Aklınızdakini yazın..."></textarea>
            
            <div id="post-media-preview-area" class="mt-3 p-2 border-dashed border-2 border-gray-300 rounded-lg hidden">
                <div id="media-preview-container" class="w-full h-auto max-h-48 mb-2 overflow-hidden">
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
                <button type="button" id="select-post-image-button" onclick="triggerPostFileSelect()" class="p-2 text-[var(--color-accent)] hover:bg-gray-100 rounded-full transition">
                    <i data-lucide="camera" class="w-6 h-6"></i>
                </button>
                <input type="file" id="post-image-input" accept="image/*,video/*" class="hidden">


                <div class="flex space-x-3">
                    <button onclick="hidePostModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-full hover:bg-gray-200 transition">
                        İptal
                    </button>
                    <button id="modal-post-button" onclick="postFromModal()" class="px-6 py-2 text-white bg-[var(--color-accent)] rounded-full font-semibold hover:opacity-90 transition">
                        Paylaş
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div id="profile-edit-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center p-4 z-40 modal-bg" onclick="hideProfileEditModal(event)">
        <div class="bg-white rounded-xl w-full max-w-md p-6 shadow-2xl modal-content-box" onclick="event.stopPropagation()">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Profili Düzenle</h3>
                <button onclick="hideProfileEditModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            
            <div class="flex items-center space-x-4 mb-6 p-3 bg-gray-50 rounded-lg">
                <div id="edit-pp-display" class="w-16 h-16 rounded-full flex items-center justify-center text-lg font-bold border-2 border-gray-300 overflow-hidden">
                    </div>
                <button type="button" onclick="triggerFileSelect()" class="px-4 py-2 text-sm bg-black text-white rounded-full hover:bg-gray-800 transition flex items-center space-x-1">
                    <i data-lucide="image" class="w-4 h-4"></i>
                    <span>Fotoğraf Seç / Değiştir</span>
                </button>
            </div>
            <input type="file" id="profile-picture-input" accept="image/*" class="hidden">
            
            <div class="space-y-4">
                <input type="text" id="edit-name" placeholder="Ad Soyad" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-[var(--color-accent)] focus:border-[var(--color-accent)] text-gray-800">
                <input type="text" id="edit-username" placeholder="Kullanıcı Adı (@ olmadan)" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-[var(--color-accent)] focus:border-[var(--color-accent)] text-gray-800">
                
                <textarea id="edit-bio" placeholder="Biyografi (Maks 150 karakter)" rows="3" maxlength="150" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-[var(--color-accent)] focus:border-[var(--color-accent)] text-gray-800 resize-none"></textarea>

                <input type="url" id="edit-url" placeholder="Web Sitesi Linki (Örn: https://)" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-[var(--color-accent)] focus:border-[var(--color-accent)] text-gray-800">
            </div>
            
            <div class="flex justify-end mt-6 space-x-3">
                <button onclick="hideProfileEditModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-full hover:bg-gray-200 transition">
                    İptal
                </button>
                <button onclick="updateProfile()" class="px-6 py-2 text-white bg-black rounded-full font-semibold hover:bg-gray-800 transition">
                    Kaydet
                </button>
            </div>
        </div>
        
    </div>

    <div id="comments-modal" class="fixed inset-0 bg-black bg-opacity-70 hidden items-center justify-center p-4 z-40 modal-bg" onclick="hideCommentsModal(event)">
        <div class="bg-white rounded-xl w-full max-w-lg h-full max-h-[90vh] flex flex-col shadow-2xl modal-content-box" onclick="event.stopPropagation()">
            <div class="flex justify-between items-center p-4 border-b border-gray-200 flex-shrink-0">
                <h3 class="text-xl font-bold text-gray-800">Yorumlar</h3>
                <button onclick="hideCommentsModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            
            <div id="comments-list-container" class="flex-grow overflow-y-auto p-4 space-y-4">
                </div>

            <div class="p-4 border-t border-gray-200 flex-shrink-0">
                <form id="comment-form" onsubmit="postComment(event); return false;" class="flex space-x-3 items-center">
                    <input type="hidden" id="comment-post-id" value="">
                    <textarea id="comment-input" placeholder="Bir yorum yazın..." rows="1" maxlength="250" class="w-full p-3 border border-gray-300 rounded-full focus:ring-[var(--color-accent)] focus:border-[var(--color-accent)] text-gray-800 resize-none"></textarea>
                    <button type="submit" id="comment-button" class="bg-[var(--color-accent)] text-white p-3 rounded-full hover:opacity-90 transition duration-150 flex-shrink-0" disabled>
                        <i data-lucide="send" class="w-5 h-5"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div id="message-box" class="fixed top-4 right-4 bg-black text-white p-3 rounded-xl shadow-xl z-50 transition-opacity duration-300 opacity-0 pointer-events-none"></div>

<script>
    const API_URL = 'api.php';	
    const LS_STATE = 'sosyalakisAppState';
    
    let currentUserId = null;	 	
    let currentUser = {};	 	 	
    let posts = [];	 	 	 	 	
    let currentView = 'feed';	 	
    let viewingUserId = null;	 	
    let editingPostId = null;	 	
    let currentPostMedia = null;	
    let isDarkMode = false;
    let isSidebarOpen = true; 
    let currentCommentsPostId = null;	
    
    let profileFileInput = null;	 	
    let postFileInput = null;
    let rootElement = null;
    
    const viewColors = {
        'feed': { accent: '#3B82F6', title: 'ANA AKIŞ', icon: 'home' },	 	
        'explore': { accent: '#10B981', title: 'KEŞFET', icon: 'compass' },	
        'messages': { accent: '#8B5CF6', title: 'MESAJLAR', icon: 'send' },	
        'profile': { accent: '#F59E0B', title: 'PROFİLİM', icon: 'user' },	 	
        'liked': { accent: '#EF4444', title: 'BEĞENİLENLER', icon: 'heart' },	
        'saved': { accent: '#0D9488', title: 'KAYDEDİLENLER', icon: 'bookmark' },	
        'admin': { accent: '#EF4444', title: 'ADMİN PANELİ', icon: 'shield' },	 	
    };

    const initialCurrentUser = {
        id: null, name: "", username: "", initial: "U", bio: "", url: "", postCount: 0,	
        followers: 0, following: 0, profilePicture: null, followingUsers: [], role: 'user'
    };
    
    let MOCK_USERS = {};
    let MOCK_POSTS = []; 
    let MOCK_FOLLOWING = {};
    let MOCK_SAVED_POSTS = {};
    let MOCK_LIKED_POSTS = {};
    let MOCK_BANS = {};	
    let MOCK_COMMENTS = {}; 
    
    function getAuthToken() {
        return localStorage.getItem('authToken');
    }

    async function secureFetch(action, method = 'GET', data = null) {
        console.log(`[API İsteği] Action: ${action}, Method: ${method}`);
        
        const token = getAuthToken();
        let url = `${API_URL}?action=${action}`;
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}` 
            }
        };

        if (method === 'GET' && data) {
            url += '&' + new URLSearchParams(data).toString();
        } else if (method === 'POST' && data) {
            options.body = JSON.stringify(data);
        }

        try {
            const response = await fetch(url, options);
            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.error || `HTTP Hata: ${response.status}`);
            }

            if (action === 'login' && result.user) {
                const user_id = result.user.id;
                MOCK_USERS[user_id] = { 
                    ...MOCK_USERS[user_id], 
                    id: user_id, 
                    full_name: result.user.full_name, 
                    username: result.user.username,
                    role: result.user.role, 
                    bio: result.user.bio,
                    website_url: result.user.website_url,
                    profile_picture_url: result.user.profile_picture_url,
                };
            }
            return result;

        } catch (error) {
            console.error(`Secure Fetch Error for ${action}:`, error);
            throw error; 
        }
    }

    function saveState() {
        const state = {
            currentUserId, currentUser, isDarkMode, isSidebarOpen, 
            mockFollowing: MOCK_FOLLOWING, mockLiked: MOCK_LIKED_POSTS, mockSaved: MOCK_SAVED_POSTS, mockBans: MOCK_BANS,
        };
        localStorage.setItem(LS_STATE, JSON.stringify(state));
    }

    function loadState() {
        const savedState = localStorage.getItem(LS_STATE);
        if (savedState) {
            const state = JSON.parse(savedState);
            currentUserId = state.currentUserId ? Number(state.currentUserId) : null;
            currentUser = state.currentUser || {...initialCurrentUser};
            isDarkMode = state.isDarkMode || false;
            isSidebarOpen = state.isSidebarOpen !== undefined ? state.isSidebarOpen : true;
            MOCK_FOLLOWING = state.mockFollowing || MOCK_FOLLOWING;
            MOCK_LIKED_POSTS = state.mockLiked || MOCK_LIKED_POSTS;
            MOCK_SAVED_POSTS = state.mockSaved || MOCK_SAVED_POSTS;
            MOCK_BANS = state.mockBans || MOCK_BANS; 
            
            if(currentUserId && currentUser.username) {
                localStorage.setItem('authToken', `simulated_token_${currentUserId}`);
            }
        } else {
            currentUser = {...initialCurrentUser};
        }
    }

    function alertUser(message, isError = false) {
        const box = document.getElementById('message-box');
        box.textContent = message;
        box.style.backgroundColor = isError ? '#EF4444' : '#10B981';
        
        box.classList.add('opacity-100');
        box.classList.remove('opacity-0');

        setTimeout(() => {
            box.classList.add('opacity-0');
            box.classList.remove('opacity-100');
        }, 3000);
    }

    function setAccentColor(viewKey) {
        let colorData = viewColors[viewKey] || viewColors.feed;
        
        if (currentView === 'profile' && viewingUserId === currentUser.id) {
             const profileUser = getProfileData(viewingUserId);
             const currentTab = profileUser ? (profileUser.currentTab || 'posts') : 'posts';
             
             if (currentTab === 'liked') {
                 colorData = viewColors.liked;
             } else if (currentTab === 'saved') {
                 colorData = viewColors.saved;
             } else {
                 colorData = viewColors.profile;
             }
        } else if (viewKey === 'profile' && viewingUserId !== currentUser.id) {
            colorData = viewColors.feed;	
        } else if (viewColors[viewKey]) {
            colorData = viewColors[viewKey];
        } else {
            colorData = viewColors.feed;
        }

        rootElement.style.setProperty('--color-accent', colorData.accent);
        document.getElementById('main-title').textContent = colorData.title.toUpperCase();
        return colorData.title.toUpperCase();
    }

    function toggleSidebar() {
        isSidebarOpen = !isSidebarOpen;
        saveState();
        applySidebarState();
    }

    function applySidebarState() {
        const sidebar = document.getElementById('desktop-nav');
        const main = document.getElementById('main-content');
        const toggleIcon = document.getElementById('toggle-icon');

        if (!sidebar || !main || !toggleIcon) return;	

        if (isSidebarOpen) {
            sidebar.classList.remove('lg:w-20', 'sidebar-closed');
            sidebar.classList.add('lg:w-64', 'sidebar-open');
            main.classList.remove('sidebar-closed');
            main.classList.add('sidebar-open');
            toggleIcon.setAttribute('data-lucide', 'chevrons-left');
        } else {
            sidebar.classList.remove('lg:w-64', 'sidebar-open');
            sidebar.classList.add('lg:w-20', 'sidebar-closed');
            main.classList.remove('sidebar-open');
            main.classList.add('sidebar-closed');
            toggleIcon.setAttribute('data-lucide', 'chevrons-right');
        }
        updateNavigation();
    }

    function toggleDarkMode() {
        isDarkMode = !isDarkMode;
        saveState();

        const body = document.body;
        const icon = document.getElementById('dark-mode-icon');
        const toggleButton = document.getElementById('dark-mode-toggle');

        if (isDarkMode) {
            body.classList.add('dark');
            body.style.setProperty('--color-background', '#0F172A');	
            body.style.setProperty('--color-text', '#E2E8F0');	
            if(toggleButton) {
                toggleButton.classList.remove('bg-white', 'text-gray-800');
                toggleButton.classList.add('bg-gray-800', 'text-white');
            }
            if(icon) icon.setAttribute('data-lucide', 'sun');
            
        } else {
            body.classList.remove('dark');
            body.style.setProperty('--color-background', '#F8FAFC');
            body.style.setProperty('--color-text', '#1F2937');
            if(toggleButton) {
                toggleButton.classList.remove('bg-gray-800', 'text-white');
                toggleButton.classList.add('bg-white', 'text-gray-800');
            }
            if(icon) icon.setAttribute('data-lucide', 'moon');
            
            document.querySelectorAll('.dark\\:bg-gray-800, .dark\\:border-gray-700, .dark\\:text-gray-100, .dark\\:bg-gray-700, .dark\\:border-gray-600, .dark\\:text-white').forEach(el => {
                el.classList.remove('dark:bg-gray-800', 'dark:border-gray-700', 'dark:text-gray-100', 'dark:bg-gray-700', 'dark:border-gray-600', 'dark:text-white');
            });
        }
        lucide.createIcons();
    }

    async function loginUser(event) {
        event.preventDefault();	
        const email = document.getElementById('login-email').value.trim();
        const password = document.getElementById('login-password').value.trim();

        try {
            const result = await secureFetch('login', 'POST', { email, password });

            if (result.token && result.user) {
                localStorage.setItem('authToken', result.token);
                currentUserId = result.user.id;
                
                currentUser = {
                    id: result.user.id,
                    name: result.user.full_name,
                    username: '@' + result.user.username,
                    initial: result.user.full_name.charAt(0).toUpperCase(),
                    bio: result.user.bio || '',
                    url: result.user.website_url || '',
                    profilePicture: result.user.profile_picture_url,
                    role: result.user.role || 'user'
                };
                
                await fetchProfileStats(currentUserId);
                
                saveState();
                hideAuthModal();
                renderApp();
                alertUser("Başarıyla giriş yapıldı.");	
            } else {
                 throw new Error(result.error || "Giriş başarısız.");
            }
        } catch (error) {
            alertUser(`Giriş başarısız: ${error.message}`, true);
        }
    }

    // --- GÜNCELLENEN KAYIT FONKSİYONU ---
    async function signupUser(event) {
        event.preventDefault();	
        
        // Form verilerini al
        const username = document.getElementById('signup-username').value.trim();
        const email = document.getElementById('signup-email').value.trim();
        const password = document.getElementById('signup-password').value.trim();
        const fullname = document.getElementById('signup-fullname').value.trim();

        // Basit doğrulama
        if (!username || !email || !password) {
            alertUser("Lütfen tüm alanları doldurun.", true);
            return;
        }

        try {
            // Register API'sine istek at
            const result = await secureFetch('register', 'POST', { 
                username: username,
                email: email,
                password: password,
                fullname: fullname
            });

            if (result.success) {
                alertUser("Kayıt başarılı! Şimdi giriş yapabilirsiniz.");
                // Formu temizle
                document.getElementById('signup-form').reset();
                toggleAuthMode(); // Giriş ekranına geç
            } else {
                throw new Error(result.error || "Kayıt başarısız.");
            }

        } catch (error) {
            alertUser(`Kayıt hatası: ${error.message}`, true);
        }
    }

    function simulateLogout() {
        if (!confirm('Çıkış yapmak istediğinizden emin misiniz?')) {
            return;	 	
        }
        
        localStorage.removeItem('authToken');	 	
        currentUserId = null;
        currentView = 'feed';	 	
        viewingUserId = null;	 	
        if (currentUser.currentTab) delete currentUser.currentTab;

        currentUser = { ...initialCurrentUser };
        
        saveState();	 	
        renderApp();
        alertUser("Başarıyla çıkış yapıldı.");
    }

    async function fetchFeed() {
        try {
            const result = await secureFetch('feed', 'GET');
            posts = result.posts.map(p => ({
                ...p, id: Number(p.id), authorId: Number(p.user_id),	
                isLiked: p.is_liked || false, isSaved: p.is_saved || false
            }));
            MOCK_POSTS = posts; 
        } catch (error) {
            console.error("Akış çekilemedi:", error.message);
            posts = [];	
        }
    }

    async function fetchProfileStats(userId) {
        try {
            const targetId = Number(userId);
            const postCount = MOCK_POSTS.filter(p => p.user_id === targetId).length;
            const followersCount = Object.values(MOCK_FOLLOWING).filter(arr => arr.includes(targetId)).length;
            const followingCount = MOCK_FOLLOWING[targetId] ? MOCK_FOLLOWING[targetId].length : 0;
            const followingIds = MOCK_FOLLOWING[currentUserId] || [];

            if (targetId === currentUser.id) {
                currentUser.postCount = postCount;
                currentUser.followers = followersCount;
                currentUser.following = followingCount;
                currentUser.followingUsers = followingIds.map(id => Number(id)) || [];	 	
            }
        } catch (error) {
            console.warn("Profil istatistikleri çekilemedi (Simülasyon devam):", error.message);
        }
    }

    function getProfileData(userId) {
        const id = Number(userId);
        if (id === currentUser.id) {
            return currentUser;
        }
        
        const mockUser = MOCK_USERS[id];
        if (mockUser) {
             const postCount = MOCK_POSTS.filter(p => p.user_id === id).length;
             const followersCount = Object.values(MOCK_FOLLOWING).filter(arr => arr.includes(id)).length;
             const followingCount = MOCK_FOLLOWING[id] ? MOCK_FOLLOWING[id].length : 0;
             
             return {
                 id: id, name: mockUser.full_name, username: `@${mockUser.username}`, initial: mockUser.initial,
                 postCount: postCount, followers: followersCount, following: followingCount,
                 profilePicture: mockUser.profile_picture_url, bio: mockUser.bio || "Bu kullanıcı hakkında detaylı bilgiye API'den ulaşılamadı.", url: mockUser.website_url || ""
             };
        }

        const post = posts.find(p => p.user_id === id);
        if (post) {
            return {
                id: id, name: post.author, handle: post.handle, initial: post.initial,	
                postCount: posts.filter(p => p.user_id === id).length, followers: 0, following: 0,	
                profilePicture: post.profilePicture, bio: "Bu kullanıcı hakkında detaylı bilgiye API'den ulaşılamadı.", url: ""
            };
        }
        return null;
    }

    function getFeedContent() {
        if (posts.length === 0) {
            return `
                <div class="flex flex-col items-center justify-center min-h-[50vh] text-center bg-white p-8 rounded-xl shadow-lg border border-gray-200">
                    <i data-lucide="inbox" class="w-16 h-16 text-gray-400 mb-4"></i>
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Henüz Gönderi Yok</h2>
                    <p class="text-gray-600 mb-4">Yeni içerikleri görmek için sayfayı yenileyin veya bir gönderi paylaşın.</p>
                </div>
            `;
        }
        const feedHtml = posts.map(post => createPostCard(post)).join('');
        return `<div id="post-feed" class="space-y-6">${feedHtml}</div>`;
    }

    function getMessagesContent() {
        return `
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200 min-h-[60vh]">
                <h2 class="text-3xl font-extrabold text-gray-800 mb-6">Mesajlar</h2>
                <div class="text-center text-gray-500 mt-10">Bu özellik henüz aktif değil.</div>
            </div>
        `;
    }

    async function getAdminPanelContent() {
        if (currentUser.role !== 'admin' && currentUser.role !== 'editor') {
            return `<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg" role="alert">
                        <p class="font-bold">Erişim Reddedildi</p>
                        <p>Bu içeriği görüntülemek için Admin veya Editör yetkisine sahip olmanız gerekmektedir.</p>
                    </div>`;
        }
        
        try {
             const userList = Object.values(MOCK_USERS).map(u => ({
                  id: u.id, full_name: u.full_name, username: u.username, email: u.email, role: u.role,
                  is_banned: MOCK_BANS[u.id] > (Date.now() / 1000)	
             }));
            
            let userHtml = userList.map(user => {
                const canBan = user.id !== currentUser.id;
                const isBanned = user.is_banned;
                
                const banAction = isBanned ?
                    `<button onclick="unbanUser(${user.id})" class="text-xs text-green-600 bg-green-100 py-1 px-2 rounded-full hover:bg-green-200 transition font-semibold w-full">
                        <i data-lucide="shield-check" class="w-4 h-4 inline mr-1"></i> Ban Kaldır
                    </button>` :
                    `<button onclick="toggleBanUser(${user.id})" class="text-xs text-red-600 bg-red-100 py-1 px-2 rounded-full hover:bg-red-200 transition font-semibold w-full">
                        <i data-lucide="shield-off" class="w-4 h-4 inline mr-1"></i> Yasakla (1 Gün)
                    </button>`;
                
                const statusBadge = isBanned ?	
                    `<span class="text-xs text-white bg-red-500 py-0.5 px-2 rounded-full font-semibold">YASAKLI</span>` :
                    `<span class="text-xs text-white bg-green-500 py-0.5 px-2 rounded-full font-semibold">${user.role.toUpperCase()}</span>`;

                return `
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between p-4 bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="mb-2 md:mb-0 md:w-1/3">
                            <p class="font-bold text-gray-800">${user.full_name} (@${user.username})</p>
                            <p class="text-sm text-gray-500">ID: ${user.id} | ${statusBadge}</p>
                        </div>
                        <div class="flex flex-wrap gap-2 text-right md:w-2/3">
                            <button onclick="viewProfile(${user.id})" class="text-xs text-blue-600 bg-blue-100 py-1 px-2 rounded-full hover:bg-blue-200 transition font-semibold">
                                <i data-lucide="user" class="w-4 h-4 inline mr-1"></i> Profili Gör
                            </button>
                            ${canBan ?	
                                `<div class="w-32">
                                    <select onchange="changeUserRole(${user.id}, this.value)" class="text-xs bg-gray-100 py-1 px-2 rounded-full border border-gray-300 transition appearance-none">
                                        <option value="user" ${user.role === 'user' ? 'selected' : ''}>User Yap</option>
                                        <option value="editor" ${user.role === 'editor' ? 'selected' : ''}>Editor Yap</option>
                                        <option value="admin" ${user.role === 'admin' ? 'selected' : ''}>Admin Yap</option>
                                    </select>
                                </div>` : ''
                            }
                            ${canBan ? banAction : ''}
                        </div>
                    </div>
                `;
            }).join('');

            return `
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Kullanıcı Yönetimi (${userList.length})</h2>
                    <div class="space-y-3">
                        ${userHtml}
                    </div>
                </div>
            `;

        } catch (error) {
            return `<div class="bg-red-100 p-4 rounded-lg">Hata: ${error.message}</div>`;
        }
    }

    function changeView(view) {
        if (!currentUserId && view !== 'feed') {
            alertUser("Bu sayfayı görüntülemek için lütfen giriş yapın.", true);
            showAuthModal('login');
            return;
        }
        if (currentView === 'profile' && currentUser.currentTab) {
             delete currentUser.currentTab;
        }
        viewingUserId = null;
        currentView = view;
        renderApp();
    }

    function viewProfile(userId) {
        if (!currentUserId) {
            alertUser("Profil görüntülemek için lütfen giriş yapın.", true);
            showAuthModal('login');
            return;
        }
        viewingUserId = Number(userId);
        currentView = 'profile';
        renderApp();
    }
    
    function changeProfileTab(userId, tab) {
        const profileUser = getProfileData(userId);
        if (!profileUser || userId !== currentUser.id) return;
        currentUser.currentTab = tab;	
        renderApp();
    }

    async function renderApp() {
        if (currentView === 'profile' && !viewingUserId && currentUserId) {
             viewingUserId = currentUserId;
        }

        const title = setAccentColor(currentView === 'profile' ? 'profile' : currentView);

        if (currentView === 'feed' || currentView === 'explore' || 
            (currentView === 'profile' && (currentUser.currentTab === 'posts' || !currentUser.currentTab)) ||
            (currentView === 'profile' && currentUser.currentTab === 'liked') ||
            (currentView === 'profile' && currentUser.currentTab === 'saved')
           ) {
             await fetchFeed();
        }	
        if (currentView === 'profile') {
             await fetchProfileStats(viewingUserId);
        }
        
        let contentHTML = '';
        
        if (!currentUserId) {
            contentHTML = getWelcomeContent();
        } else if (currentView === 'feed' || currentView === 'explore') {
            contentHTML = getFeedContent();
        } else if (currentView === 'profile' && viewingUserId) { 
            contentHTML = await getProfileContent(viewingUserId);
        } else if (currentView === 'messages') { 
            contentHTML = getMessagesContent();
        }
        else if (currentView === 'admin') {
            contentHTML = await getAdminPanelContent();
        } else {
            contentHTML = `<div class="p-8 bg-white rounded-xl shadow-lg text-center text-gray-500">Bu alan (${title}) henüz hazır değil.</div>`;
        }

        const contentArea = document.getElementById('content-area');
        contentArea.innerHTML = contentHTML;
        
        applySidebarState(); 
    }

    function updateNavigation() {
        const isUserLoggedIn = !!currentUserId;
        const isAdminOrEditor = isUserLoggedIn && (currentUser.role === 'admin' || currentUser.role === 'editor');
        const isDarkModeActive = document.body.classList.contains('dark');
        
        const mobileNav = document.getElementById('mobile-nav');
        const sidebarMenu = document.getElementById('sidebar-menu');
        const sidebarProfile = document.getElementById('sidebar-profile');
        const mobileAuthButton = document.getElementById('mobile-auth-button');
        const mobileProfileButton = document.getElementById('mobile-profile-button');
        
        mobileNav.innerHTML = '';
        sidebarMenu.innerHTML = '';
        sidebarProfile.innerHTML = '';
        
        if (isUserLoggedIn) {
            mobileAuthButton.classList.add('hidden');
            mobileProfileButton.classList.remove('hidden');

            sidebarMenu.innerHTML += createNavLink('feed', 'Ana Sayfa', 'home');
            sidebarMenu.innerHTML += createNavLink('explore', 'Keşfet', 'compass');
            sidebarMenu.innerHTML += createNavLink('messages', 'Mesajlar', 'send');
            sidebarMenu.innerHTML += createNavLink('profile', 'Profil', 'user');
            if (isAdminOrEditor) {
                sidebarMenu.innerHTML += createNavLink('admin', 'Admin Paneli', 'shield');
            }
            sidebarMenu.innerHTML += `<button onclick="showPostModal()" class="w-full mt-4 bg-[var(--color-accent)] text-white p-3 rounded-full shadow-lg hover:opacity-90 transition duration-150 font-bold flex items-center justify-center">
                <i data-lucide="plus" class="w-5 h-5 ${isSidebarOpen ? 'mr-2' : ''}"></i> <span id="post-button-text">Gönderi At</span>
            </button>`;

            const darkModeToggleHTML = `
                <button id="dark-mode-toggle" onclick="toggleDarkMode()" class="mt-4 p-3 bg-gray-50 rounded-xl border border-gray-200 cursor-pointer hover:bg-gray-100 transition duration-150 flex items-center justify-between text-gray-700 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 dark:hover:bg-gray-600">
                    <span>${isDarkModeActive ? 'Aydınlık Tema' : 'Koyu Tema'}</span>
                    <i data-lucide="${isDarkModeActive ? 'sun' : 'moon'}" id="dark-mode-icon" class="w-5 h-5"></i>
                </button>
            `;
            
            sidebarProfile.innerHTML = `
                ${darkModeToggleHTML}
                <a href="#" onclick="viewProfile(${currentUser.id}); return false;" class="nav-link flex items-center p-3 rounded-xl hover:bg-gray-100 text-gray-700 mt-4 dark:hover:bg-gray-700">
                    ${createAvatar(currentUser, 40, 'mr-3')}
                    <div>
                        <p class="text-sm font-semibold">${currentUser.name}</p>
                        <p class="text-xs text-gray-500">${currentUser.username}</p>
                    </div>
                </a>
                <button onclick="simulateLogout()" class="mt-2 w-full text-center text-red-500 text-sm py-2 hover:bg-red-50 rounded-lg transition flex items-center justify-center">
                    <i data-lucide="log-out" class="w-4 h-4 ${isSidebarOpen ? 'mr-1' : ''}"></i> Çıkış Yap
                </button>
            `;

            mobileNav.innerHTML += createMobileNavLink('feed', 'Ana Sayfa', 'home');
            mobileNav.innerHTML += createMobileNavLink('explore', 'Keşfet', 'compass');
            mobileNav.innerHTML += `<button onclick="showPostModal()" class="flex flex-col items-center text-gray-700 p-1 nav-link hover:text-black dark:text-gray-400 dark:hover:text-gray-100">
                <i data-lucide="plus-circle" class="w-6 h-6"></i>
                <span class="text-xs">Gönderi</span>
            </button>`;
            mobileNav.innerHTML += createMobileNavLink('messages', 'Mesajlar', 'send');
            mobileNav.innerHTML += createMobileNavLink('profile', 'Profil', 'user', currentUser.id);
            
            const ppDisplay = currentUser.profilePicture && currentUser.profilePicture.length > 1 ? `<img src="${currentUser.profilePicture}" alt="PP" class="w-full h-full rounded-full object-cover">` : currentUser.initial;
            mobileProfileButton.innerHTML = ppDisplay;
            mobileProfileButton.classList.toggle('text-white', !currentUser.profilePicture);
            mobileProfileButton.classList.toggle('bg-blue-600', !currentUser.profilePicture);
            
        } else {
            mobileAuthButton.classList.remove('hidden');
            mobileProfileButton.classList.add('hidden');
            sidebarMenu.innerHTML += createNavLink('feed', 'Ana Sayfa', 'home');
            sidebarMenu.innerHTML += `<button id="dark-mode-toggle" onclick="toggleDarkMode()" class="mt-4 p-3 bg-gray-50 rounded-xl border border-gray-200 cursor-pointer hover:bg-gray-100 transition duration-150 flex items-center justify-between text-gray-700 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 dark:hover:bg-gray-600">
                <span>${isDarkModeActive ? 'Aydınlık Tema' : 'Koyu Tema'}</span>
                <i data-lucide="${isDarkModeActive ? 'sun' : 'moon'}" id="dark-mode-icon" class="w-5 h-5"></i>
            </button>`;
            mobileNav.innerHTML = `<button onclick="showAuthModal('login')" class="w-full flex justify-center py-2 bg-black text-white font-semibold rounded-lg">
                <i data-lucide="log-in" class="w-6 h-6 mr-2"></i> Giriş Yap
            </button>`;
        }
        lucide.createIcons(); 
    }

    function createNavLink(view, text, icon) {
        const isActive = currentView === view;
        return `
            <a href="#" onclick="changeView('${view}'); return false;"	
                class="nav-link flex items-center p-3 rounded-xl hover:bg-gray-100 transition duration-150 ${isActive ? 'active text-black font-semibold' : 'text-gray-700'} dark:hover:bg-gray-700">
                <i data-lucide="${icon}" class="w-5 h-5 ${isSidebarOpen ? 'mr-3' : 'mr-0'}"></i> <span>${text}</span>
            </a>
        `;
    }

    function createMobileNavLink(view, text, icon, userId = null) {
        const isActive = currentView === view || (view === 'profile' && currentView === 'profile' && Number(userId) === Number(currentUser.id));
        return `
            <a href="#" onclick="${userId ? `viewProfile(${userId})` : `changeView('${view}')`}; return false;"	
                class="flex flex-col items-center p-1 nav-link ${isActive ? 'active font-semibold' : 'text-gray-700 hover:text-black'} dark:text-gray-400 dark:hover:text-gray-100">
                <i data-lucide="${icon}" class="w-6 h-6"></i>
                <span class="text-xs">${text}</span>
            </a>
        `;
    }

    function createAvatar(user, size, classes = '') {
        const initial = user.initial || user.name.charAt(0).toUpperCase();
        const sizePx = size;
        if (user.profilePicture && user.profilePicture.startsWith('http')) {
            return `<div class="${classes} rounded-full overflow-hidden flex-shrink-0" style="width:${sizePx}px; height:${sizePx}px;">
                <img src="${user.profilePicture}" alt="PP" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='https://placehold.co/${sizePx}x${sizePx}/000000/ffffff?text=${initial}';">
            </div>`;
        }
        return `<div class="${classes} rounded-full bg-black flex items-center justify-center text-white font-bold flex-shrink-0" style="width:${sizePx}px; height:${sizePx}px; font-size:${sizePx/2}px;">
            ${initial}
        </div>`;
    }

    async function getProfileContent(userId) {
        const profileUser = getProfileData(userId);
        const isMyProfile = profileUser && profileUser.id === currentUser.id;

        if (!profileUser) {
            return `<div class="bg-white p-5 rounded-xl text-center text-red-500 shadow-md border border-gray-200">Kullanıcı bulunamadı :(</div>`;
        }
        
        const currentTab = isMyProfile ? (currentUser.currentTab || 'posts') : 'posts';
        
        let postsData = [];
        let postHeader = '';
        
        if (currentTab === 'posts') {
            postsData = posts.filter(p => p.user_id === profileUser.id);	 	
            postHeader = isMyProfile ? 'Gönderileriniz' : `${profileUser.name} Gönderileri`;
        } else if (currentTab === 'liked' && isMyProfile) {
            const likedIds = MOCK_LIKED_POSTS[userId] || [];
            postsData = MOCK_POSTS.filter(p => likedIds.includes(p.id));
            postHeader = 'Beğenilenler';
        } else if (currentTab === 'saved' && isMyProfile) {
            const savedIds = MOCK_SAVED_POSTS[userId] || [];
            postsData = MOCK_POSTS.filter(p => savedIds.includes(p.id));
            postHeader = 'Kaydedilenler';
        }
        
        const userPostsHtml = postsData.map(post => createPostCard(post, false)).join('');
        
        const actionButton = isMyProfile ?
            `<button onclick="showProfileEditModal()" class="bg-black text-white px-6 py-2 rounded-full font-semibold hover:bg-gray-800 transition">
                Profili Düzenle
            </button>` :
            (currentUserId ? createFollowButton(profileUser) :	
                `<button onclick="showAuthModal('login')" class="bg-[var(--color-accent)] text-white px-6 py-2 rounded-full font-semibold hover:opacity-90 transition">
                    Giriş Yap (Takip Et)
                </button>`);
        
        const profileHeaderHTML = `
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200 text-center">
                <div class="relative w-24 h-24 mx-auto mb-4">
                    ${createAvatar(profileUser, 96, 'mx-auto')}
                </div>

                <h2 class="text-2xl font-bold text-gray-800">${profileUser.name}</h2>
                <p class="text-md text-gray-500 mb-6">${profileUser.username}</p>
                
                <p class="text-gray-700 mb-2 whitespace-pre-line text-center">${profileUser.bio || 'Henüz bir biyografi eklenmedi.'}</p>
                
                ${profileUser.url ? `
                    <a href="${profileUser.url}" target="_blank" class="text-[var(--color-accent)] hover:underline transition duration-150 mb-8 flex items-center justify-center space-x-1 mx-auto max-w-fit">
                        <i data-lucide="link" class="w-4 h-4"></i>
                        <span>${profileUser.url.replace(/^https?:\/\//, '').split('/')[0]}</span>
                    </a>
                ` : `<p class="text-sm text-gray-500 mb-8 text-center">Henüz bir link eklenmedi.</p>`}


                <div class="flex justify-around border-t border-b py-4 mb-4">
                    <div>
                        <p class="text-xl font-bold text-gray-800">${profileUser.postCount}</p>
                        <p class="text-sm text-gray-500">Gönderi</p>
                    </div>
                    <div>
                        <p class="text-xl font-bold text-gray-800">${profileUser.followers}</p>
                        <p class="text-sm text-gray-500">Takipçi</p>
                    </div>
                    <div>
                        <p class="text-xl font-bold text-gray-800">${profileUser.following}</p>
                        <p class="text-sm text-gray-500">Takip</p>
                    </div>
                </div>
                
                <div class="flex justify-center space-x-4 mb-6 border-b border-gray-200">
                    <button onclick="changeProfileTab(${profileUser.id}, 'posts')" class="pb-2 text-sm font-semibold border-b-2 transition ${currentTab === 'posts' ? 'border-amber-500 text-gray-800' : 'border-transparent text-gray-500 hover:text-gray-700'}">Gönderileri</button>
                    ${isMyProfile ? `<button onclick="changeProfileTab(${profileUser.id}, 'liked')" class="pb-2 text-sm font-semibold border-b-2 transition ${currentTab === 'liked' ? 'border-red-500 text-red-500' : 'border-transparent text-gray-500 hover:text-gray-700'}">Beğenilenler</button>` : ''}
                    ${isMyProfile ? `<button onclick="changeProfileTab(${profileUser.id}, 'saved')" class="pb-2 text-sm font-semibold border-b-2 transition ${currentTab === 'saved' ? 'border-teal-600 text-teal-600' : 'border-transparent text-gray-500 hover:text-gray-700'}">Kaydedilenler</button>` : ''}
                </div>
                
                <div class="flex flex-col md:flex-row justify-center space-y-3 md:space-y-0 md:space-x-3 mt-4">
                    ${actionButton}
                </div>
            </div>`;
            
        const postSectionHTML = `
            <h3 class="text-xl font-bold text-gray-800 mt-10 mb-4">${postHeader} (${postsData.length})</h3>
            <div id="user-posts-feed" class="space-y-6">
                ${userPostsHtml.length > 0 ? userPostsHtml : `
                    <div class="bg-white p-5 rounded-xl text-center text-gray-500 shadow-md border border-gray-200">
                        Bu bölümde henüz içerik yok.
                    </div>
                `}
            </div>
        `;
        return profileHeaderHTML + postSectionHTML;
    }

    function createFollowButton(profileUser) {
        const isFollowing = currentUser.followingUsers.includes(profileUser.id);
        const buttonClass = isFollowing ? 'bg-gray-400 hover:bg-gray-500' : 'bg-[var(--color-accent)] hover:opacity-90';
        const buttonText = isFollowing ? 'Takibi Bırak' : 'Takip Et';
        
        return `
            <button onclick="toggleFollow(${profileUser.id})" class="${buttonClass} text-white px-6 py-2 rounded-full font-semibold transition flex items-center justify-center">
                <i data-lucide="${isFollowing ? 'user-x' : 'user-plus'}" class="w-4 h-4 mr-2"></i> ${buttonText}
            </button>
        `;
    }

    function createPostCard(post) {
        const isUserPost = currentUserId && currentUser.id === post.user_id;
        const isPostLiked = currentUserId && MOCK_LIKED_POSTS[currentUserId] && MOCK_LIKED_POSTS[currentUserId].includes(post.id);
        const isPostSaved = currentUserId && MOCK_SAVED_POSTS[currentUserId] && MOCK_SAVED_POSTS[currentUserId].includes(post.id);

        const likeIcon = isPostLiked ? 'heart' : 'heart';
        const likeColor = isPostLiked ? 'text-red-500 fill-red-500' : 'text-gray-500';
        
        const saveIcon = 'bookmark';
        const saveColor = isPostSaved ? 'text-teal-600 fill-teal-600' : 'text-gray-500';
        
        const authorData = getProfileData(post.user_id) || {
            name: post.author, handle: post.handle, profilePicture: post.profilePicture, initial: post.initial
        };
        
        const mediaTag = post.media_url ? `
            <img src="${post.media_url}"	
                onerror="this.onerror=null; this.src='https://placehold.co/600x300/F59E0B/ffffff?text=Medya+Yuklenemedi';"
                alt="Gönderi Resmi"	
                class="w-full h-auto rounded-lg mb-4 object-cover max-h-96">` : '';

        const optionsButton = isUserPost ? `
            <div class="relative">
                <button class="text-gray-500 hover:text-gray-800 p-1 rounded-full hover:bg-gray-100 transition z-30" onclick="togglePostOptions(${post.id}, this)">
                    <i data-lucide="more-horizontal" class="w-5 h-5"></i>
                </button>
                <div id="options-menu-${post.id}" class="hidden absolute right-0 mt-2 w-40 bg-white rounded-lg shadow-xl py-1 border border-gray-200 z-40">
                    <button onclick="prepareEditPost(${post.id})" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full">
                        <i data-lucide="edit-2" class="w-4 h-4 mr-2"></i> Düzenle
                    </button>
                    <button onclick="deletePost(${post.id})" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 w-full">
                        <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i> Sil
                    </button>
                </div>
            </div>
        ` : '';

        return `
            <div class="post-card bg-white p-5 rounded-xl shadow-md border border-gray-200" id="post-${post.id}">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-start cursor-pointer" onclick="viewProfile(${post.user_id})">
                        ${createAvatar(authorData, 40, 'mr-3')}
                        <div>
                            <p class="font-bold text-gray-800 hover:underline">${authorData.name}</p>
                            <p class="text-sm text-gray-500 hover:underline">${authorData.handle} • ${timeAgo(post.created_at)}</p>
                        </div>
                    </div>
                    ${optionsButton}
                </div>
                <p class="text-gray-900 mb-4 whitespace-pre-line">${post.content}</p>
                ${mediaTag}
                <div class="flex space-x-6 border-t pt-3 border-gray-100">
                    <div class="flex items-center space-x-2 ${likeColor} hover:text-red-500 hover:fill-red-500 post-action" onclick="toggleLike(this, ${post.id})">
                        <i data-lucide="${likeIcon}" class="w-5 h-5 ${isPostLiked ? 'fill-red-500' : ''}"></i>
                        <span class="text-sm">${post.likes} Beğeni</span>
                    </div>
                    
                    <div class="flex items-center space-x-2 ${saveColor} hover:text-teal-600 hover:fill-teal-600 post-action" onclick="toggleSave(this, ${post.id})">
                        <i data-lucide="${saveIcon}" class="w-5 h-5 ${isPostSaved ? 'fill-teal-600' : ''}"></i>
                        <span class="text-sm">${isPostSaved ? 'Kaydedildi' : 'Kaydet'}</span>
                    </div>

                    <div class="flex items-center space-x-2 text-gray-500 hover:text-blue-500 post-action" onclick="showCommentsModal(${post.id})">
                        <i data-lucide="message-circle" class="w-5 h-5"></i>
                        <span class="text-sm comment-count-${post.id}">${MOCK_COMMENTS[post.id] ? MOCK_COMMENTS[post.id].length : 0} Yorum</span>
                    </div>
                </div>
            </div>
        `;
    }
    
    function getWelcomeContent() {
        return `
            <div class="flex flex-col items-center justify-center min-h-[60vh] text-center bg-white p-8 rounded-xl shadow-lg border-2 border-[var(--color-accent)] border-solid">
                <i data-lucide="zap" class="w-16 h-16 text-[var(--color-accent)] mb-4"></i>
                <h2 class="text-3xl font-extrabold text-gray-800 mb-2">Sosyal Akış'a Hoş Geldiniz!</h2>
                <p class="text-lg text-gray-600 mb-6">Akışı görebilmek, gönderi oluşturabilmek ve Admin/Editör panellerini deneyimleyebilmek için giriş yapın.</p>
                <div class="flex space-x-4">
                    <button onclick="showAuthModal('login')" class="px-8 py-3 bg-black text-white font-bold rounded-full shadow-lg hover:bg-gray-800 transition duration-150">
                        Giriş Yap
                    </button>
            </div>
            </div>
        `;
    }

    function timeAgo(timestamp) {
        if (!timestamp) return 'Bilinmiyor';
        const now = Date.now() / 1000;
        const seconds = Math.floor(now - timestamp);
        if (seconds < 60) return `${seconds} saniye önce`;
        const minutes = Math.floor(seconds / 60);
        if (minutes < 60) return `${minutes} dakika önce`;
        const hours = Math.floor(minutes / 60);
        if (hours < 24) return `${hours} saat önce`;
        const days = Math.floor(hours / 24);
        return `${days} gün önce`;
    }

    function showAuthModal(mode = 'login') {
        const modal = document.getElementById('auth-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => { modal.classList.add('active'); }, 10);
        document.body.style.overflow = 'hidden';	 	
        
        const isSignup = mode === 'signup';
        document.getElementById('auth-modal-title').textContent = isSignup ? 'Üye Ol' : 'Giriş Yap';
        document.getElementById('login-form').classList.toggle('hidden', isSignup);
        document.getElementById('signup-form').classList.toggle('hidden', !isSignup);
        document.getElementById('auth-switch-text').textContent = isSignup ? 'Zaten hesabın var mı?' : 'Hesabın yok mu?';
        document.getElementById('auth-switch-button').textContent = isSignup ? 'Giriş Yap' : 'Üye Ol';
    }

    function hideAuthModal(event) {
        const modal = document.getElementById('auth-modal');
        if (event && event.target !== modal) return;
        modal.classList.remove('active');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }, 300);
    }
    
    function toggleAuthMode() {
        const isLoginVisible = !document.getElementById('login-form').classList.contains('hidden');
        showAuthModal(isLoginVisible ? 'signup' : 'login');
    }
    
    function showPostModal(isEditing = false) {
        if (!currentUserId) {
            alertUser("Gönderi atmak için önce giriş yapmalısınız.", true);
            showAuthModal('login');
            return;
        }
        const modal = document.getElementById('post-modal');
        
        if (!isEditing) {
            document.getElementById('post-modal-title').textContent = 'Yeni Gönderi Oluştur';
            document.getElementById('modal-post-button').textContent = 'Paylaş';
            document.getElementById('modal-post-button').setAttribute('onclick', 'postFromModal()');
            document.getElementById('modal-post-input').value = '';
            currentPostMedia = null;
        }
        
        updatePostModalMediaDisplay();

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => { modal.classList.add('active'); }, 10);
        document.body.style.overflow = 'hidden';
        lucide.createIcons({ parent: modal });
    }

    function hidePostModal(event) {
        const modal = document.getElementById('post-modal');
        if (event && event.target !== modal) return;
        
        modal.classList.remove('active');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }, 300);

        editingPostId = null;
        currentPostMedia = null;
        if (postFileInput) postFileInput.value = '';
    }

    function showProfileEditModal() {	 	
        if (!currentUserId) return;
        const modal = document.getElementById('profile-edit-modal');
        
        document.getElementById('edit-name').value = currentUser.name;
        document.getElementById('edit-username').value = currentUser.username.replace('@', '');
        document.getElementById('edit-bio').value = currentUser.bio;
        document.getElementById('edit-url').value = currentUser.url;

        updateEditModalPPCard();

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        setTimeout(() => { modal.classList.add('active'); }, 10);
        document.body.style.overflow = 'hidden';
        lucide.createIcons({ parent: modal });
    }

    function hideProfileEditModal(event) {
        const modal = document.getElementById('profile-edit-modal');
        if (event && event.target !== modal) return;
        modal.classList.remove('active');
        setTimeout(() => { modal.classList.add('hidden'); modal.classList.remove('flex'); document.body.style.overflow = ''; }, 300);
    }
    
    function showCommentsModal(postId) {
        if (!currentUserId) { alertUser("Yorumları görmek için lütfen giriş yapın.", true); showAuthModal('login'); return; }
        const modal = document.getElementById('comments-modal');
        currentCommentsPostId = postId;
        document.getElementById('comment-post-id').value = postId;
        document.getElementById('comment-input').value = '';
        document.getElementById('comment-input').addEventListener('input', checkCommentInput);
        checkCommentInput(); 

        loadComments(postId);

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => { modal.classList.add('active'); }, 10);
        document.body.style.overflow = 'hidden';
        lucide.createIcons({ parent: modal });
    }

    function hideCommentsModal(event) {
        const modal = document.getElementById('comments-modal');
        if (event && event.target !== modal) return;
        
        modal.classList.remove('active');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }, 300);
        currentCommentsPostId = null;
    }

    function checkCommentInput() {
        const input = document.getElementById('comment-input');
        const button = document.getElementById('comment-button');
        button.disabled = input.value.trim().length === 0;
    }

    async function loadComments(postId) {
        const container = document.getElementById('comments-list-container');
        container.innerHTML = '<div class="text-center p-4 text-gray-500"><i data-lucide="loader-circle" class="w-6 h-6 animate-spin inline mr-2"></i> Yorumlar yükleniyor...</div>';
        lucide.createIcons({ parent: container });

        try {
            const result = await secureFetch('get_comments', 'GET', { post_id: postId });
            
            if (result.success) {
                MOCK_COMMENTS[postId] = result.comments.map(c => ({
                    id: c.id, user_id: c.user_id, content: c.content, created_at: c.created_at	
                }));

                if (result.comments.length === 0) {
                    container.innerHTML = '<div class="text-center p-4 text-gray-500">Bu gönderiye henüz yorum yapılmamış. İlk yorumu sen yap!</div>';
                } else {
                    const commentsHtml = result.comments.map(comment => createCommentCard(comment)).join('');
                    container.innerHTML = commentsHtml;
                }
            } else {
                throw new Error(result.error || "Yorumlar yüklenemedi.");
            }
        } catch (error) {
            container.innerHTML = `<div class="bg-red-100 p-3 rounded-lg text-red-700">Hata: ${error.message}</div>`;
        }
        lucide.createIcons({ parent: container });
    }

    function createCommentCard(comment) {
        const authorData = {
            name: comment.author_name || (MOCK_USERS[comment.user_id] ? MOCK_USERS[comment.user_id].full_name : "Bilinmeyen Kullanıcı"),
            username: comment.author_handle || (MOCK_USERS[comment.user_id] ? `@${MOCK_USERS[comment.user_id].username}` : "@unknown"),
            profilePicture: comment.author_pp || (MOCK_USERS[comment.user_id] ? MOCK_USERS[comment.user_id].profile_picture_url : null),
            initial: comment.author_initial || (MOCK_USERS[comment.user_id] ? MOCK_USERS[comment.user_id].initial : "U")
        };
        
        return `
            <div class="flex space-x-3 bg-gray-50 p-3 rounded-xl border border-gray-200">
                <div onclick="viewProfile(${comment.user_id})" class="cursor-pointer flex-shrink-0">
                    ${createAvatar(authorData, 32, '')}
                </div>
                <div class="flex-grow">
                    <div class="flex items-center space-x-2 mb-1">
                        <p class="font-semibold text-gray-800 text-sm">${authorData.name}</p>
                        <p class="text-xs text-gray-500">${authorData.username}</p>
                        <p class="text-xs text-gray-400 ml-auto">${timeAgo(comment.created_at)}</p>
                    </div>
                    <p class="text-gray-700 text-sm whitespace-pre-line">${comment.content.replace(/<br>/g, '\n')}</p>
                </div>
            </div>
        `;
    }

    async function postComment(event) {
        event.preventDefault();
        if (!currentUserId) return;
        
        const postId = Number(document.getElementById('comment-post-id').value);
        const content = document.getElementById('comment-input').value.trim();

        if (!content) {
            alertUser("Yorum içeriği boş olamaz.", true);
            return;
        }
        
        try {
            const result = await secureFetch('add_comment', 'POST', { post_id: postId, content: content.replace(/\n/g, '<br>') });
            
            if (result.success) {
                document.getElementById('comment-input').value = '';
                checkCommentInput();	
                
                await loadComments(postId);
                
                const post = MOCK_POSTS.find(p => p.id === postId);
                if (post) post.commentCount = MOCK_COMMENTS[postId].length;
                document.querySelector(`.comment-count-${postId}`).textContent = `${MOCK_COMMENTS[postId].length} Yorum`;
                
                saveState();
                alertUser("Yorum başarıyla eklendi.");
            } else {
                throw new Error(result.error || "Yorum eklenemedi.");
            }
        } catch (error) {
            alertUser(`Yorum ekleme başarısız: ${error.message}`, true);
        }
    }
    
    async function postFromModal() {
        if (!currentUserId) return;

        const content = document.getElementById('modal-post-input').value.trim();
        if (!content && !currentPostMedia) {
            alertUser("Lütfen bir şeyler yazın veya bir resim/video ekleyin.", true);
            return;
        }

        try {
            const postData = {
                content: content.replace(/\n/g, '<br>'),
                media_url: currentPostMedia,	 	
            };

            const newPostResult = await secureFetch('create_post', 'POST', postData);
            
            if (newPostResult.success && newPostResult.post) {
                 MOCK_POSTS.unshift(newPostResult.post); 
            }
            
            await fetchFeed();
            await fetchProfileStats(currentUserId);
            saveState();	 	

            document.getElementById('modal-post-input').value = '';	 	
            currentPostMedia = null;
            hidePostModal();
            renderApp();
            alertUser("Gönderi başarıyla paylaşıldı!");

        } catch (error) {
            alertUser(`Paylaşım başarısız: ${error.message}`, true);
        }
    }
    
    async function toggleLike(element, postId) {
        if (!currentUserId) { alertUser("Beğenmek için lütfen giriş yapın.", true); showAuthModal('login'); return; }
        
        const postIndex = MOCK_POSTS.findIndex(p => p.id === postId);	
        if (postIndex === -1) return;
        const post = MOCK_POSTS[postIndex];

        const isCurrentlyLiked = MOCK_LIKED_POSTS[currentUserId]?.includes(postId) || false;

        try {
            const result = await secureFetch('toggle_like', 'POST', { post_id: postId, action: isCurrentlyLiked ? 'unlike' : 'like' });
            
            if (result.success) {
                
                post.likes = result.new_likes_count;
                post.isLiked = result.new_state === 'liked';	
                
                MOCK_LIKED_POSTS[currentUserId] = MOCK_LIKED_POSTS[currentUserId] || [];
                if (post.isLiked) {
                    if (!MOCK_LIKED_POSTS[currentUserId].includes(postId)) MOCK_LIKED_POSTS[currentUserId].push(postId);
                } else {
                    MOCK_LIKED_POSTS[currentUserId] = MOCK_LIKED_POSTS[currentUserId].filter(id => id !== postId);
                }

                const countElement = element.querySelector('span');
                const iconElement = element.querySelector('[data-lucide]');
                
                element.classList.remove('text-red-500', 'text-gray-500', 'fill-red-500');
                element.classList.add(post.isLiked ? 'text-red-500' : 'text-gray-500');
                
                iconElement.classList.toggle('fill-red-500', post.isLiked);
                if (countElement) countElement.textContent = post.likes + ' Beğeni';
                
                saveState();	 	
                alertUser(post.isLiked ? "Gönderi beğenildi!" : "Beğeni kaldırıldı.");
                
                if (currentView === 'profile') renderApp();

            } else {
                throw new Error(result.error || "Beğenme/Beğeni Kaldırma başarısız.");
            }
            
        } catch (error) {
            alertUser(`Beğenme işlemi başarısız: ${error.message}`, true);
        }
    }
    
    async function toggleSave(element, postId) {
        if (!currentUserId) { alertUser("Kaydetmek için lütfen giriş yapın.", true); showAuthModal('login'); return; }
        
        const postIndex = MOCK_POSTS.findIndex(p => p.id === postId);	
        if (postIndex === -1) return;
        const post = MOCK_POSTS[postIndex];

        const isCurrentlySaved = MOCK_SAVED_POSTS[currentUserId]?.includes(postId) || false;

        try {
            const action = isCurrentlySaved ? 'unsave' : 'save';
            const result = await secureFetch('toggle_save', 'POST', { post_id: postId, action: action });
            
            if (result.success) {
                post.isSaved = result.new_state === 'saved';	
                const isSaved = post.isSaved;
                
                MOCK_SAVED_POSTS[currentUserId] = MOCK_SAVED_POSTS[currentUserId] || [];
                 if (isSaved) {
                     if (!MOCK_SAVED_POSTS[currentUserId].includes(postId)) MOCK_SAVED_POSTS[currentUserId].push(postId);
                } else {
                    MOCK_SAVED_POSTS[currentUserId] = MOCK_SAVED_POSTS[currentUserId].filter(id => id !== postId);
                }

                const countElement = element.querySelector('span');
                const iconElement = element.querySelector('[data-lucide]');

                element.classList.remove('text-teal-600', 'text-gray-500');
                element.classList.add(isSaved ? 'text-teal-600' : 'text-gray-500');
                
                iconElement.classList.toggle('fill-teal-600', isSaved);
                
                if (countElement) countElement.textContent = isSaved ? 'Kaydedildi' : 'Kaydet';
                
                saveState();
                alertUser(isSaved ? "Gönderi kaydedildi!" : "Kaydedilenlerden kaldırıldı.");
                
                if (currentView === 'profile') renderApp();

            } else {
                throw new Error(result.error || "Kaydetme/Kaldırma başarısız.");
            }
            
        } catch (error) {
            alertUser(`Kaydetme işlemi başarısız: ${error.message}`, true);
        }
    }

    async function unbanUser(userId) {
        if (currentUser.role !== 'admin') {
            alertUser("Yasak kaldırma yetkiniz yok.", true);
            return;
        }
        
        const targetId = Number(userId);
        if (!confirm(`${MOCK_USERS[targetId]?.full_name} kullanıcısının yasağını kaldırmak istediğinizden emin misiniz?`)) {
            return;	 	
        }
        
        try {
             const result = await secureFetch('unban_user', 'POST', { user_id: targetId });	

             if (result.success) {
                 MOCK_BANS[targetId] = 0; 
                 alertUser(`${MOCK_USERS[targetId].full_name} kullanıcısının yasağı kaldırıldı.`);
                 saveState();
                 renderApp();
             } else {
                 throw new Error(result.error || "Yasak kaldırma işlemi başarısız oldu.");
             }
            
        } catch (error) {
            alertUser(`Yasak kaldırma işlemi başarısız: ${error.message}`, true);
        }
    }
    
    async function changeUserRole(userId, newRole) {
        if (currentUser.role !== 'admin') {
            alertUser("Rol değiştirme yetkiniz yok.", true);
            return;
        }
        
        const targetId = Number(userId);
        const targetUser = MOCK_USERS[targetId];

        if (!confirm(`${targetUser.full_name} kullanıcısının rolünü ${newRole.toUpperCase()} olarak değiştirmek istediğinizden emin misiniz?`)) {
            return;	 	
        }
        
        try {
             const result = await secureFetch('change_role', 'POST', { user_id: targetId, new_role: newRole });	

             if (result.success) {
                 targetUser.role = newRole;
                 MOCK_USERS[targetId] = targetUser;
                 alertUser(`${targetUser.full_name} rolü ${newRole.toUpperCase()} olarak güncellendi.`);
                 saveState();
                 renderApp();
             } else {
                 throw new Error(result.error || "Rol değiştirme işlemi başarısız oldu.");
             }
            
        } catch (error) {
            alertUser(`Rol değiştirme işlemi başarısız: ${error.message}`, true);
        }
    }


    async function toggleBanUser(userId) {
        if (currentUser.role !== 'admin') {
            alertUser("Yasaklama yetkiniz yok.", true);
            return;
        }
        
        const targetId = Number(userId);
        const user = MOCK_USERS[targetId];
        if (!user) {
            alertUser("Kullanıcı bulunamadı.", true);
            return;
        }
        
        if (!confirm(`${user.full_name} (@${user.username}) kullanıcısını 1 günlüğüne yasaklamak istediğinizden emin misiniz?`)) {
            return;	 	
        }
        
        try {
             const result = await secureFetch('ban_user', 'POST', { user_id: targetId, duration: 86400 });	

             if (result.success) {
                 const banUntil = (Date.now() / 1000) + 86400;
                 MOCK_BANS[targetId] = banUntil;
                 
                 alertUser(`${user.full_name} başarıyla 1 gün süreyle yasaklandı.`);
                 
                 if (targetId === currentUserId) {
                      simulateLogout();	
                      alertUser(`Hesabınız yasaklandı. Çıkış yapıldı.`, true);
                 }
                 
                 saveState();
                 renderApp();
             } else {
                 throw new Error(result.error || "Yasaklama işlemi başarısız oldu.");
             }
            
        } catch (error) {
            alertUser(`Yasaklama işlemi başarısız: ${error.message}`, true);
        }
    }


    async function toggleFollow(userId) {
        if (!currentUserId) { alertUser("Takip etmek için lütfen giriş yapın.", true); showAuthModal('login'); return; }
        const targetId = Number(userId);
        const isFollowing = currentUser.followingUsers.includes(targetId);	 	
        const action = isFollowing ? 'unfollow' : 'follow';
        
        try {
            const result = await secureFetch('follow_action', 'POST', { followed_id: targetId, action: action });	 	

            if (result.success) {
                 if (action === 'follow') {
                     if (!MOCK_FOLLOWING[currentUserId]) MOCK_FOLLOWING[currentUserId] = [];
                     MOCK_FOLLOWING[currentUserId].push(targetId);
                 } else {
                     MOCK_FOLLOWING[currentUserId] = MOCK_FOLLOWING[currentUserId].filter(id => id !== targetId);
                 }
                 
                 await fetchProfileStats(currentUserId);
                
                if (isFollowing) {
                    alertUser("Takibi bıraktınız.");
                } else {
                    alertUser("Takip etmeye başladınız.");
                }

                saveState();
                renderApp();
            } else {
                throw new Error(result.error || "Sunucudan beklenen yanıt alınamadı.");
            }
            
        } catch (error) {
            alertUser(`İşlem başarısız: ${error.message}`, true);
        }
    }
    
    function triggerFileSelect() {
        if (!currentUserId || !profileFileInput) return;
        profileFileInput.click();
    }

    function handleFileSelect(event) {
        const file = event.target.files[0];
        if (file) {
            if (!file.type.match('image.*')) {
                alertUser("Lütfen sadece bir resim dosyası seçin.", true);
                event.target.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = async (e) => {
                const base64Image = e.target.result;
                currentUser.profilePicture = base64Image;
                updateEditModalPPCard();
                event.target.value = '';	 	
                alertUser("Yeni fotoğraf seçildi. Kaydetmek için 'Kaydet' butonuna tıklayın.");
            };
            reader.readAsDataURL(file);
        }
    }
    
    function updateEditModalPPCard() {
        const ppDisplay = document.getElementById('edit-pp-display');
        if (!ppDisplay) return;
        ppDisplay.classList.remove('bg-black', 'text-white', 'text-lg', 'font-bold');
        ppDisplay.classList.add('bg-gray-200');

        if (currentUser.profilePicture && currentUser.profilePicture.startsWith('http')) {
             ppDisplay.innerHTML = `<img src="${currentUser.profilePicture}" alt="PP" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='https://placehold.co/96x96/000000/ffffff?text=${currentUser.initial}';">`;
        } else if (currentUser.profilePicture) {
            ppDisplay.innerHTML = `<img src="${currentUser.profilePicture}" alt="PP" class="w-full h-full object-cover">`;
        } else {
            ppDisplay.innerHTML = currentUser.initial;
            ppDisplay.classList.remove('bg-gray-200');
            ppDisplay.classList.add('bg-black', 'text-white', 'text-lg', 'font-bold');
        }
    }

    async function updateProfile() {
        if (!currentUserId) return;

        const newName = document.getElementById('edit-name').value.trim();
        const newUsername = document.getElementById('edit-username').value.trim();
        const newBio = document.getElementById('edit-bio').value.trim();
        const newUrl = document.getElementById('edit-url').value.trim();

        if (!newName || !newUsername) {
            alertUser("Lütfen ad ve kullanıcı adı alanlarını boş bırakmayın.", true);
            return;
        }
        
        try {
            const result = await secureFetch('update_profile', 'POST', {
                full_name: newName,
                username: newUsername.replace('@', ''),
                bio: newBio,
                website_url: newUrl,
                profile_picture_url: currentUser.profilePicture
            });

            if (result.success) {
                 currentUser.name = newName;
                 currentUser.username = `@${newUsername.replace('@', '')}`;
                 currentUser.initial = newName.charAt(0).toUpperCase(); 
                 currentUser.bio = newBio;
                 currentUser.url = newUrl;
                 
                 if (MOCK_USERS[currentUserId]) {
                     MOCK_USERS[currentUserId].full_name = newName;
                     MOCK_USERS[currentUserId].username = newUsername.replace('@', '');
                     MOCK_USERS[currentUserId].bio = newBio;
                     MOCK_USERS[currentUserId].website_url = newUrl;
                     MOCK_USERS[currentUserId].profile_picture_url = currentUser.profilePicture;
                     MOCK_USERS[currentUserId].initial = currentUser.initial;
                 }
                 
                 saveState();	 	
                 hideProfileEditModal();
                 renderApp();
                 alertUser("Profiliniz başarıyla güncellendi.");
            } else {
                 throw new Error(result.error || "Profil güncelleme başarısız.");
            }

        } catch (error) {
            alertUser(`Profil güncelleme başarısız: ${error.message}`, true);
        }
    }

    document.addEventListener('DOMContentLoaded', async () => {
        rootElement = document.documentElement;
        profileFileInput = document.getElementById('profile-picture-input');
        if (profileFileInput) { profileFileInput.addEventListener('change', handleFileSelect); }
        
        postFileInput = document.getElementById('post-image-input');
        if (postFileInput) { postFileInput.addEventListener('change', handlePostMediaSelect); }	 	

        loadState();
        
        if (isDarkMode) {
            toggleDarkMode();
        } else {
             lucide.createIcons();
        }

        await renderApp();
    });

    function triggerPostFileSelect() { if (!currentUserId || !postFileInput) return; postFileInput.click(); }
    
    function handlePostMediaSelect(event) {	
        const file = event.target.files[0];
        if (file) {
            if (!file.type.match('image.*')) {
                alertUser("Lütfen sadece bir resim dosyası seçin.", true);
                event.target.value = '';
                return;
            }
            
            const randomColor = Math.floor(Math.random()*16777215).toString(16).padStart(6, '0');
            currentPostMedia = `https://placehold.co/600x400/${randomColor}/ffffff?text=Yuklenen+Gorsel`;
            
            updatePostModalMediaDisplay(file.type.startsWith('video'));
            alertUser("Resim yüklendi (Mock URL). Gönderi oluşturabilirsiniz.");
        }
        event.target.value = ''; 
    }
    
    function removePostMedia() {	
        currentPostMedia = null;	
        if (postFileInput) postFileInput.value = ''; 
        updatePostModalMediaDisplay();	
        alertUser("Medya kaldırıldı.");
    }

    function updatePostModalMediaDisplay(isVideo = false) {	
        const previewArea = document.getElementById('post-media-preview-area');
        const previewContainer = document.getElementById('media-preview-container');
        const selectButton = document.getElementById('select-post-image-button');
        
        previewContainer.innerHTML = '';

        if (currentPostMedia) {
            if (isVideo) {
                 previewContainer.innerHTML = `<img src="${currentPostMedia}" alt="Video Preview" class="w-full h-full object-contain rounded-lg border border-red-500">`; 
            } else {
                 previewContainer.innerHTML = `<img src="${currentPostMedia}" alt="Preview" class="w-full h-full object-contain rounded-lg">`;
            }
            previewArea.classList.remove('hidden');
            selectButton.classList.add('hidden'); 
        } else {
            previewArea.classList.add('hidden');
            selectButton.classList.remove('hidden'); 
        }
    }

    function togglePostOptions(postId, buttonElement) {
        document.querySelectorAll('[id^="options-menu-"]').forEach(menu => { if (menu.id !== `options-menu-${postId}`) { menu.classList.add('hidden'); } });
        const menu = document.getElementById(`options-menu-${postId}`);
        if (menu) { menu.classList.toggle('hidden'); }
    }

    function hidePostOptions(postId) {
        const menu = document.getElementById(`options-menu-${postId}`);
        if (menu) { menu.classList.add('hidden'); }
    }

    function prepareEditPost(postId) {	
        const post = MOCK_POSTS.find(p => p.id === postId);
        if (!post) {
            alertUser("Gönderi bulunamadı.", true);
            return;
        }

        editingPostId = postId;
        document.getElementById('post-modal-title').textContent = 'Gönderiyi Düzenle';
        document.getElementById('modal-post-button').textContent = 'Kaydet';
        document.getElementById('modal-post-button').setAttribute('onclick', 'editPostFromModal()');
        document.getElementById('modal-post-input').value = post.content.replace(/<br>/g, '\n');
        currentPostMedia = post.media_url;
        updatePostModalMediaDisplay();
        showPostModal(true);
        hidePostOptions(postId);
    }

    async function editPostFromModal() {
        if (!currentUserId || !editingPostId) return;

        const content = document.getElementById('modal-post-input').value.trim();
        if (!content && !currentPostMedia) {
            alertUser("Lütfen bir şeyler yazın veya bir resim/video ekleyin.", true);
            return;
        }

        const postIndex = MOCK_POSTS.findIndex(p => p.id === editingPostId);
        if (postIndex === -1) {
            alertUser("Düzenlenecek gönderi bulunamadı.", true);
            return;
        }

        try {
            const result = await secureFetch('update_post', 'POST', {
                post_id: editingPostId,
                content: content.replace(/\n/g, '<br>'),
                media_url: currentPostMedia
            });

            if (result.success) {
                MOCK_POSTS[postIndex].content = content.replace(/\n/g, '<br>');
                MOCK_POSTS[postIndex].media_url = currentPostMedia;

                saveState();
                hidePostModal();
                renderApp();
                alertUser("Gönderi başarıyla düzenlendi.");
            } else {
                 throw new Error(result.error || "Gönderi güncelleme başarısız.");
            }
        } catch (error) {
             alertUser(`Düzenleme başarısız: ${error.message}`, true);
        }
    }

    function deletePost(postId) {	
        if (!confirm('Bu gönderiyi silmek istediğinizden emin misiniz?')) {
            return;	 	
        }

        const postIndex = MOCK_POSTS.findIndex(p => p.id === postId);
        if (postIndex !== -1) {
            secureFetch('delete_post', 'POST', { post_id: postId })
                .then(result => {
                    if (result.success) {
                        MOCK_POSTS.splice(postIndex, 1);
                        fetchProfileStats(currentUserId);
                        saveState();
                        renderApp();
                        alertUser("Gönderi başarıyla silindi.");
                    } else {
                         alertUser(result.error || "Gönderi silme başarısız.", true);
                    }
                })
                .catch(error => {
                    alertUser(`Silme işlemi başarısız: ${error.message}`, true);
                });
            
        } else {
            alertUser("Gönderi bulunamadı.", true);
        }
    }

    document.addEventListener('click', (event) => {
        if (!event.target.closest('[onclick^="togglePostOptions"]') && !event.target.closest('[id^="options-menu-"]')) {
            document.querySelectorAll('[id^="options-menu-"]').forEach(menu => { menu.classList.add('hidden'); });
        }
    });

</script>
    
</body>
</html>