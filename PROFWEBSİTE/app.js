  // --- LOCAL STORAGE VE SİMÜLASYON YÖNETİMİ ---
        const LS_STATE = 'socialAppState'; // Local Storage anahtarı

        let currentUserId = null; 
        let currentUser = {}; 
        let posts = []; 
        let currentView = 'feed'; 
        let editingPostId = null; 
        let profileFileInput = null; 
        let currentPostMedia = null; // GÜNCELLENDİ: Resmi ve Videoyu tutar
        let postFileInput = null; 
        
        let viewingUserId = null; 
        let chattingWithId = 2; // YENİ: Hangi kullanıcıyla sohbet ettiğimizi tutar (Varsayılan: Mert Yılmaz - ID: 2)
        
        // Başlangıç Verileri (Yalnızca ilk yüklemede kullanılır)
        const initialCurrentUser = {
            id: 1, // Kendi kullanıcımızın ID'si
            name: "",
            username: "",
            initial: "0",
            bio: "", 
            url: "", 
            postCount: 0, 
            followers: 0,
            following: 0,
            profilePicture: null,
            followingUsers: [] // Takip edilen kullanıcı ID'leri
        };

        // Simüle Edilen Dış Kullanıcı Verileri (authorId'leri postlarda kullanılacak)
        const externalUsersMap = {
            2: { 
                id: 2, 
                name: "Mert Yılmaz", 
                username: "@mertyilmaz", 
                initial: "M", 
                postCount: 0, 
                followers: 0, 
                following: 0, 
                profilePicture: null, 
                bio: "Front-end developer. Tasarım ve kodlama.", 
                url: "mertyilmaz.dev",
                followersList: [1, 3, 10, 20, 50], // Simülasyon listesi
                followingList: [3, 10, 100],        // Simülasyon listesi
                chatHistory: [
                    { senderId: 2, content: "Hey, yeni projen nasıl gidiyor? Tasarım konusunda takıldığın bir yer var mı?", time: "10:30" },
                    { senderId: 1, content: "Merhaba Mert! Responsive kısım bitti, şimdi dark mode'a geçiyorum. Çok iyi ilerliyor.", time: "10:35" },
                    { senderId: 2, content: "Harika! Başarılar dilerim. Bitince bir demo görmek isterim.", time: "10:40" },
                ]
            },
            3: { 
                id: 3, 
                name: "Tasarımcı Dükkanı", 
                username: "@design_hub", 
                initial: "T", 
                postCount: 0, 
                followers: 0, 
                following: 0, 
                profilePicture: "https://placehold.co/100x100/1F2937/F8FAFC?text=T", 
                bio: "Günün renk paletleri ve minimalizm. Bizi takip edin!", 
                url: "designhub.com",
                followersList: [1, 2, 5, 8], // Simülasyon listesi
                followingList: [2],
                chatHistory: [] // Bu kullanıcıyla daha önce sohbet yok
            }
        };

        const initialPosts = [
            // authorId 2 (Mert)
            { id: 1, authorId: 2, author: "Mert Yılmaz", handle: "@mertyilmaz", initial: "M", time: "2 saat önce", content: "Yeni bir front-end projesine başladım, responsive tasarımlar için Tailwind CSS'i tekrar kullandım. Mavi, siyah, beyaz çok sade ama şık durdu! #frontend #webdev", likes: 0, isLiked: false, isSaved: false, color: 'bg-black', imageUrl: null, profilePicture: null },
            // authorId 3 (Tasarımcı Dükkanı)
            { id: 2, authorId: 3, author: "Tasarımcı Dükkanı", handle: "@design_hub", initial: "T", time: "45 dakika önce", content: "Günün renk paleti: #3B82F6 (Mavi), #1F2937 (Koyu Gri/Siyah) ve #F8FAFC (Beyaz). Minimalizm her zaman kazanır! 🎨", likes: 0, isLiked: false, isSaved: false, color: 'bg-blue-500', imageUrl: "https://placehold.co/600x300/3B82F6/ffffff?text=Minimal+Tasarım", profilePicture: "https://placehold.co/100x100/1F2937/F8FAFC?text=T" },
        ];
        
        // Simüle Edilmiş Sohbet Verisi (Simülasyon kolaylığı için Mert'in geçmişini kullanacağız)
        let allChatHistories = {
            1: externalUsersMap[2].chatHistory, // Varsayılan sohbet ID 2 ile
        };


        // Durumu kaydeder (Local Storage)
        function saveState() {
            const state = {
                currentUserId,
                currentUser,
                posts,
                // YENİ: Sohbet geçmişini kaydet
                allChatHistories: allChatHistories
            };
            localStorage.setItem(LS_STATE, JSON.stringify(state));
        }

        // Başlangıçta durumu yükler (Local Storage)
        function loadState() {
            const savedState = localStorage.getItem(LS_STATE);
            if (savedState) {
                const state = JSON.parse(savedState);
                
                currentUserId = state.currentUserId ? Number(state.currentUserId) : null;
                currentUser = state.currentUser;
                if (!currentUser.followingUsers) {
                    currentUser.followingUsers = [];
                }
                
                posts = state.posts.map(p => ({
                    ...p,
                    id: Number(p.id),
                    authorId: p.authorId ? Number(p.authorId) : null,
                    isLiked: p.isLiked || false,
                    isSaved: p.isSaved || false
                }));

                // YENİ: Sohbet geçmişini yükle
                if (state.allChatHistories) {
                    allChatHistories = state.allChatHistories;
                }

            } else {
                // İlk yükleme
                posts = [...initialPosts];
                currentUser = {...initialCurrentUser};
            }
        }
        
        // YENİ POZİSYON: Basit bir uyarı mekanizması
        function alertUser(message) {
            const alertBox = document.createElement('div');
            alertBox.className = 'fixed top-4 right-4 bg-black text-white p-3 rounded-lg shadow-xl z-50 transition-opacity duration-300';
            alertBox.textContent = message;
            document.body.appendChild(alertBox);

            setTimeout(() => {
                alertBox.style.opacity = '0';
                setTimeout(() => alertBox.remove(), 300);
            }, 3000);
        }

        // YENİ POZİSYON: Şifre doğrulama
        function validatePassword(password) {
            if (password.length < 8) {
                return "Şifre en az 8 karakter uzunluğunda olmalıdır.";
            }
            if (!/[A-Z]/.test(password)) {
                return "Şifre en az bir büyük harf içermelidir.";
            }
            if (!/[0-9]/.test(password)) {
                return "Şifre en az bir sayı içermelidir.";
            }
            return null;
        }
        
        // YENİ: Tüm kullanıcıları birleştirir (Simülasyon amaçlı)
        function getAllUsers() {
            return [
                currentUser,
                ...Object.values(externalUsersMap)
            ].filter(u => u.id !== null); // ID'si olmayanları filtrele
        }

        // YENİ: Başka kullanıcının verisini getirir
        function getProfileData(userId) {
            const id = Number(userId); // Gelen userId'yi sayıya dönüştür
            if (id === Number(currentUser.id)) {
                return currentUser;
            }
            return externalUsersMap[id];
        }

        // YENİ: Belirli bir kullanıcının takipçi/takip ettikleri listesini getirir
        function getFollowList(userId, type) {
            const user = getProfileData(userId);
            if (!user) return [];

            let listIds = [];
            const allUsers = getAllUsers();

            if (type === 'followers') {
                // Kendi takipçilerimizi hesapla (Dış kullanıcılar içinde bizi takip edenler)
                if (user.id === currentUser.id) {
                    listIds = Object.values(externalUsersMap)
                        .filter(extUser => extUser.followingUsers && extUser.followingUsers.includes(currentUser.id))
                        .map(extUser => extUser.id);
                } else {
                    // Dış kullanıcının simüle edilmiş takipçi listesi
                    listIds = user.followersList || [];
                }
            } else if (type === 'following') {
                // Kendi takip ettiklerimiz veya dış kullanıcının simüle edilmiş takip edilen listesi
                listIds = user.followingUsers || user.followingList || [];
            }

            // ID listesini kullanıcı objelerine dönüştür
            return listIds.map(id => allUsers.find(u => u.id === Number(id))).filter(u => u);
        }


        // YENİ: Profil fotoğrafı modalını açar
        function showPictureModal(url) {
            if (!url) {
                alertUser("Bu kullanıcının profil fotoğrafı yok.");
                return;
            }
            const modal = document.getElementById('picture-modal');
            document.getElementById('modal-picture-img').src = url;
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => { modal.classList.add('active'); }, 10);
            document.body.style.overflow = 'hidden';
        }
        
        function hidePictureModal(event) {
            const modal = document.getElementById('picture-modal');
            if (event && event.target !== modal) return;
            
            modal.classList.remove('active');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }, 300);
            document.getElementById('modal-picture-img').src = '';
        }

        // KİMLİK DOĞRULAMA (Giriş/Kayıt) YÖNETİMİ
        
        // --- MODAL YÖNETİMİ ---
        function showAuthModal(mode = 'login') {
            const modal = document.getElementById('auth-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => { modal.classList.add('active'); }, 10);
            document.body.style.overflow = 'hidden'; 
            
            if (mode === 'signup') {
                document.getElementById('auth-modal-title').textContent = 'Üye Ol';
                document.getElementById('login-form').classList.add('hidden');
                document.getElementById('signup-form').classList.remove('hidden');
                document.getElementById('auth-switch-text').textContent = 'Zaten hesabın var mı?';
                document.getElementById('auth-switch-button').textContent = 'Giriş Yap';
            } else {
                document.getElementById('auth-modal-title').textContent = 'Giriş Yap';
                document.getElementById('login-form').classList.remove('hidden');
                document.getElementById('signup-form').classList.add('hidden');
                document.getElementById('auth-switch-text').textContent = 'Hesabın yok mu?';
                document.getElementById('auth-switch-button').textContent = 'Üye Ol';
            }
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
        // MODAL YÖNETİMİ SONU 

        // Giriş Formu (Simülasyon)
        function loginUser() {
            const email = document.getElementById('login-email').value.trim();
            const password = document.getElementById('login-password').value.trim();
            
            if (!email || !password) {
                alertUser("Lütfen e-posta ve şifre alanlarını boş bırakmayın.");
                return;
            }
            
            const passwordError = validatePassword(password);
            if (passwordError) {
                alertUser(`Giriş başarısız: Şifre gereksinimleri karşılanmıyor.`);
                return;
            }
            
            // Başarılı giriş
            simulateLogin();
        }

        // Üye Ol Formu 
        function signupUser() {
            const username = document.getElementById('signup-username').value.trim();
            const email = document.getElementById('signup-email').value.trim();
            const password = document.getElementById('signup-password').value.trim();
            const full_name = document.getElementById('signup-fullname').value.trim();

            if (!username || !email || !password || !full_name) {
                alertUser("Lütfen tüm alanları doldurun.");
                return;
            }
            
            const passwordError = validatePassword(password);
            if (passwordError) {
                alertUser(`Kayıt başarısız: ${passwordError}`);
                return;
            }
            
            // Kullanıcıyı güncelle
            currentUser = {
                id: initialCurrentUser.id, // Kendi kullanıcımız ID 1 olarak kalır
                name: full_name,
                username: `@${username}`,
                initial: full_name.charAt(0).toUpperCase(),
                bio: "", 
                url: "", 
                postCount: 0,
                followers: 0,
                following: 0,
                profilePicture: null,
                followingUsers: []
            };
            
            handleSignupSuccess(); 
        }

        // Başarılı Kayıt Sonrası Giriş Ekranına Yönlendirme
        function handleSignupSuccess() {
            document.getElementById('signup-username').value = '';
            document.getElementById('signup-email').value = '';
            document.getElementById('signup-password').value = '';
            document.getElementById('signup-fullname').value = '';
            
            document.getElementById('auth-modal-title').textContent = 'Giriş Yap';
            document.getElementById('login-form').classList.remove('hidden');
            document.getElementById('signup-form').classList.add('hidden');
            document.getElementById('auth-switch-text').textContent = 'Hesabın yok mu?';
            document.getElementById('auth-switch-button').textContent = 'Üye Ol';

            alertUser("Üyeliğiniz başarıyla oluşturuldu! Şimdi giriş yapabilirsiniz.");
        }

        // Giriş Bölümü
        function simulateLogin() {
            currentUserId = currentUser.id;
            saveState(); // Oturumu kaydet
            hideAuthModal();
            renderApp(); // UI'ı güncelle
            alertUser("Başarıyla giriş yapıldı. Akışa hoş geldiniz!");
        }

        // Çıkış Bölümü
        function simulateLogout() {
            if (!confirm('Çıkış yapmak istediğinizden emin misiniz?')) {
                return; 
            }

            currentUserId = null;
            currentView = 'feed'; 
            viewingUserId = null; // Profil görüntüsünü sıfırla
            
            saveState(); // Oturumu temizle
            
            renderApp();
            alertUser("Başarıyla çıkış yapıldı.");
        }
        
        
        // KULLANICI/PROFİL GÖRÜNÜMÜ YÖNETİM KISMI 
       
        function changeView(view) {
            // Başka bir profile bakıyorsak, ana menüye dönerken sıfırla
            if (view !== 'profile' && view !== 'followers' && view !== 'following') {
                viewingUserId = null;
            }

            if (!currentUserId && (view === 'profile' || view === 'saved' || view === 'liked' || view === 'messages' || view === 'search' || view === 'followers' || view === 'following')) {
                currentView = 'feed';
                alertUser("Bu sayfayı görüntülemek için lütfen giriş yapın.");
            } else {
                currentView = view;
            }
            renderApp();
        }
        
        // Takipçi Veya Takip Edilen Listesine git
        function viewFollowList(userId, type) {
            if (!currentUserId) {
                alertUser("Listeyi görüntülemek için lütfen giriş yapın.");
                showAuthModal('login');
                return;
            }
            viewingUserId = userId; // Hangi kullanıcının listesine baktığımızı ayarla
            currentView = type; // 'followers' veya 'following'
            renderApp();
        }


        // Başka kullanıcıların profilini gösteme kısmı
        function viewProfile(userId) {
            if (!currentUserId) {
                alertUser("Profil görüntülemek için lütfen giriş yapın.");
                showAuthModal('login');
                return;
            }
            viewingUserId = Number(userId);
            currentView = 'profile';
            renderApp();
        }
        
        // Sohbeti ile ilgili kısım
        function startChat(userId) {
             if (!currentUserId) {
                alertUser("Mesaj göndermek için lütfen giriş yapın.");
                showAuthModal('login');
                return;
            }
            chattingWithId = Number(userId);
            currentView = 'messages';
            renderApp();
        }


        //  Takip Etme Ve Takibi Bırakma Kısmı
        function toggleFollow(userId) {
            const isFollowing = currentUser.followingUsers.includes(Number(userId)); // ID'yi sayıya çevir
            const externalUser = externalUsersMap[userId];

            if (isFollowing) {
                // Takibi Bırak
                currentUser.followingUsers = currentUser.followingUsers.filter(id => id !== Number(userId)); // ID'yi sayıya çevir
                currentUser.following--;
                externalUser.followers = Math.max(0, externalUser.followers - 1);
                alertUser(`@${externalUser.username.replace('@', '')} adlı kişiyi takibi bıraktınız.`);
            } else {
                // Takip Et
                currentUser.followingUsers.push(Number(userId)); // ID'yi sayıya çevir
                currentUser.following++;
                externalUser.followers = externalUser.followers + 1;
                alertUser(`@${externalUser.username.replace('@', '')} adlı kişiyi takip etmeye başladınız.`);
            }

            saveState();
            renderApp(); // Sayaçları ve butonu güncelle
        }


        //  PROFİL DÜZENLEME YÖNETİM Kısmı
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
        
        function updateEditModalPPCard() {
            const ppDisplay = document.getElementById('edit-pp-display');
            if (!ppDisplay) return;
            ppDisplay.classList.remove('bg-blue-600', 'bg-gray-200');
            if (currentUser.profilePicture) {
                ppDisplay.innerHTML = `<img src="${currentUser.profilePicture}" alt="PP" class="w-full h-full rounded-full object-cover">`;
                ppDisplay.classList.remove('text-white', 'text-lg', 'font-bold');
            } else {
                ppDisplay.innerHTML = currentUser.initial;
                ppDisplay.classList.add('bg-blue-600', 'text-white', 'text-lg', 'font-bold');
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
                    alertUser("Lütfen sadece bir resim dosyası seçin.");
                    event.target.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = (e) => {
                    currentUser.profilePicture = e.target.result;
                    updateEditModalPPCard();
                    event.target.value = ''; 
                    alertUser("Yeni fotoğraf seçildi. Kaydetmek için 'Kaydet' butonuna tıklayın.");
                };
                reader.readAsDataURL(file);
            }
        }

        // Profil Güncelleme İşleminin Yapılması
        function updateProfile() {
            const newName = document.getElementById('edit-name').value.trim();
            const newUsername = document.getElementById('edit-username').value.trim();
            const newBio = document.getElementById('edit-bio').value.trim();
            const newUrl = document.getElementById('edit-url').value.trim();

            if (!newName || !newUsername) {
                alertUser("Lütfen ad ve kullanıcı adı alanlarını boş bırakmayın.");
                return;
            }
            
            currentUser.name = newName;
            currentUser.username = `@${newUsername.replace('@', '')}`;
            currentUser.initial = newName.charAt(0).toUpperCase();
            currentUser.bio = newBio;
            currentUser.url = newUrl;
            
            saveState(); 
            hideProfileEditModal();
            renderApp();
            alertUser("Profiliniz başarıyla güncellendi.");
        }

        
        // İÇERİK Ve GÖNDERİ OLUŞTURMA  FONKSİYONLARI
       
        
        function getLikedContent() {
            const likedPosts = posts.filter(p => p.isLiked);
            const likedPostsHtml = likedPosts.map(post => createPostCard(post, false)).join('');
            
            if (likedPosts.length === 0) {
                 return `
                    <div class="flex flex-col items-center justify-center min-h-[50vh] text-center bg-white p-8 rounded-xl shadow-lg border border-gray-200">
                        <i data-lucide="heart-off" class="w-16 h-16 text-gray-400 mb-4"></i>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Beğenilen Gönderi Yok</h2>
                        <p class="text-gray-600 mb-4">Akışta gördüğünüz kartları beğenerek buraya ekleyebilirsiniz.</p>
                        <button onclick="changeView('feed')" class="px-6 py-2 bg-blue-600 text-white font-bold rounded-full hover:bg-blue-700 transition duration-150">
                            Akışa Göz At
                        </button>
                    </div>
                `;
            }

            return `
                <h3 class="text-xl font-bold text-gray-800 mb-4">Beğendiğiniz Gönderiler (${likedPosts.length})</h3>
                <div id="liked-feed" class="space-y-6">
                    ${likedPostsHtml}
                </div>
            `;
        }
        
        // Mesajlaşma Sayfası İçeriği Kısmı
        function getMessagesContent() {
            // Hangi kullanıcıyla sohbet ettiğimizi bul
            const partnerId = chattingWithId;
            const partnerData = getProfileData(partnerId);
            
            // Eğer partner yoksa, sadece listeyi göster (Bu simülasyon basit olduğu için listeyi göstermiyoruz)
            if (!partnerData) {
                 return `<div class="bg-white p-5 rounded-xl text-center text-gray-500 shadow-md border border-gray-200">Sohbet başlatılacak kullanıcı bulunamadı.</div>`;
            }

            const chatHistory = allChatHistories[partnerId] || partnerData.chatHistory || [];
            
            const chatHtml = chatHistory.map(msg => {
                const isMe = msg.senderId === currentUser.id;
                const align = isMe ? 'justify-end' : 'justify-start';
                const bgColor = isMe ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-900';
                
                const senderHandle = isMe ? currentUser.username : partnerData.username;
                const senderName = isMe ? 'Sen' : partnerData.name;


                return `
                    <div class="flex ${align} mb-4">
                        <div class="flex flex-col max-w-xs md:max-w-md">
                            <p class="text-xs text-gray-500 mb-1 ${isMe ? 'text-right' : 'text-left'}">${senderName}</p>
                            <div class="p-3 rounded-xl ${bgColor} shadow-md">
                                <p class="text-sm">${msg.content}</p>
                            </div>
                            <p class="text-xs text-gray-400 mt-1 ${isMe ? 'text-right' : 'text-left'}">${msg.time}</p>
                        </div>
                    </div>
                `;
            }).join('');

            // Avatar HTML'i Kısmı
            const avatarHtml = partnerData.profilePicture 
                ? `<img src="${partnerData.profilePicture}" alt="PP" class="w-10 h-10 rounded-full object-cover mr-3">`
                : `<div class="w-10 h-10 rounded-full bg-blue-600 mr-3 flex items-center justify-center text-white font-bold">${partnerData.initial}</div>`;


            return `
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 min-h-[70vh] flex flex-col">
                    <!-- Chat Başlık (Tıklanabilir) -->
                    <div class="p-4 border-b border-gray-200 flex items-center space-x-3 cursor-pointer hover:bg-gray-50 transition" onclick="viewProfile(${partnerData.id})">
                        ${avatarHtml}
                        <div>
                            <h4 class="font-bold text-gray-800 hover:underline">${partnerData.name}</h4>
                            <p class="text-sm text-gray-500 hover:underline">${partnerData.username}</p>
                        </div>
                    </div>

                    <!-- Mesaj Akışı -->
                    <div class="flex-grow p-4 space-y-4 overflow-y-auto" id="chat-feed" style="max-height: calc(70vh - 120px);">
                        ${chatHistory.length > 0 ? chatHtml : `<p class="text-gray-500 text-center p-8">Sohbeti başlatın.</p>`}
                    </div>

                    <!-- Mesaj Gönderme Alanı -->
                    <div class="p-4 border-t border-gray-200">
                        <div class="flex space-x-2">
                            <input type="text" id="chat-input" placeholder="Mesaj yaz..." class="flex-grow p-3 border border-gray-300 rounded-full focus:ring-blue-500 focus:border-blue-500 text-gray-800" onkeypress="if(event.key === 'Enter') simulateSendMessage()">
                            <button onclick="simulateSendMessage()" class="bg-blue-600 text-white p-3 rounded-full hover:bg-blue-700 transition duration-150">
                                <i data-lucide="send" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }
        
        function simulateSendMessage() {
            if (!currentUserId) { return; }

            const input = document.getElementById('chat-input');
            const content = input.value.trim();
            const partnerId = chattingWithId;

            if (content) {
                const newMessage = {
                    senderId: currentUser.id,
                    content: content,
                    time: new Date().toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit' })
                };
                
                // Mesajı geçmişe ekle
                if (!allChatHistories[partnerId]) {
                    allChatHistories[partnerId] = [];
                }
                allChatHistories[partnerId].push(newMessage);
                
                input.value = '';
                saveState();
                renderApp(); // UI'ı hemen güncelle
                
                // Simülasyon olarak karşı taraftan hemen cevap gelsin(Random mesaj)
                setTimeout(() => {
                    const autoReply = {
                        senderId: partnerId,
                        content: "Anladım, teşekkürler! 👋",
                        time: new Date().toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit' })
                    };
                    allChatHistories[partnerId].push(autoReply);
                    saveState();
                    renderApp(); // UI'ı yeniden çiz
                    const chatFeed = document.getElementById('chat-feed');
                    if (chatFeed) { chatFeed.scrollTop = chatFeed.scrollHeight; } // En alta kaydır
                }, 1500);

            }
        }


        function getFeedContent() {
            const feedHtml = posts.map(post => createPostCard(post, false)).join('');

            return `
                <div id="post-feed" class="space-y-6">
                    ${feedHtml}
                </div>
                <div class="text-center text-gray-500 py-10">
                    Daha fazla gönderi yükle...
                </div>
            `;
        }
        
        // Arama Sayfası İçeriği Kısmı
        function getSearchContent() {
            if (!currentUserId) return getFeedContent(); 

            return `
                <div class="bg-white p-4 rounded-xl shadow-lg border border-gray-200 sticky top-0 z-10 lg:static">
                    <div class="flex space-x-2">
                        <!-- handleSearch fonksiyonu, klavyeden her karakter girildiğinde sonuçları filtreler -->
                        <input type="text" id="search-input" placeholder="Kullanıcı adı veya isim ara..." 
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-800" 
                               onkeyup="handleSearch(this.value)">
                    </div>
                </div>
                
                <div id="search-results" class="space-y-4 mt-6">
                    <p class="text-gray-500 text-center p-8">Aramaya başlamak için en az 2 karakter girin.</p>
                </div>
            `;
        }

        function handleSearch(query) {
            const resultsDiv = document.getElementById('search-results');
            const normalizedQuery = query.toLowerCase().trim();

            if (normalizedQuery.length < 2) {
                resultsDiv.innerHTML = '<p class="text-gray-500 text-center p-8">Aramaya başlamak için en az 2 karakter girin.</p>';
                return;
            }

            // Tüm kullanıcıları birleştir 
            const allUsers = [
                currentUser,
                ...Object.values(externalUsersMap)
            ].filter(user => user.id !== currentUserId); // Kendimizi hariç tut

            const filteredUsers = allUsers.filter(user => {
                const nameMatch = user.name.toLowerCase().includes(normalizedQuery);
                const handleMatch = user.username.toLowerCase().includes(normalizedQuery);
                return nameMatch || handleMatch;
            });

            let resultsHtml = '';
            if (filteredUsers.length > 0) {
                resultsHtml = filteredUsers.map(user => {
                    // Avatar HTML'i oluştur
                    const avatarHtml = user.profilePicture 
                        ? `<img src="${user.profilePicture}" alt="PP" class="w-12 h-12 rounded-full object-cover">`
                        : `<div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-lg">${user.initial}</div>`;
                    
                    return `
                        <div onclick="viewProfile(${user.id})" class="flex items-center justify-between p-4 bg-white rounded-xl shadow-sm border border-gray-100 cursor-pointer hover:bg-gray-50 transition">
                            <div class="flex items-center space-x-3">
                                ${avatarHtml}
                                <div>
                                    <p class="font-bold text-gray-800">${user.name}</p>
                                    <p class="text-sm text-gray-500">${user.username}</p>
                                </div>
                            </div>
                            <!-- Takipçi ve Gönderi Sayıları -->
                            <div class="flex space-x-4 text-center text-sm text-gray-600">
                                <div><p class="font-bold text-gray-800">${user.followers}</p> <p class="text-xs">Takipçi</p></div>
                                <div><p class="font-bold text-gray-800">${user.postCount}</p> <p class="text-xs">Gönderi</p></div>
                                <div><p class="font-bold text-gray-800">${user.following}</p> <p class="text-xs">Takip</p></div>
                            </div>
                        </div>
                    `;
                }).join('');
            } else {
                resultsHtml = `<p class="text-gray-500 text-center p-8">Aradığınız kritere uygun kullanıcı bulunamadı.</p>`;
            }

            resultsDiv.innerHTML = resultsHtml;
            lucide.createIcons({ parent: resultsDiv });
        }
        
        function getProfileContent() {
            // Hangi profili görüntülediğimizi belirle
            const profileUser = viewingUserId ? getProfileData(viewingUserId) : currentUser;
            const isMyProfile = profileUser.id === currentUser.id;

            if (!profileUser) {
                return `<div class="bg-white p-5 rounded-xl text-center text-red-500 shadow-md border border-gray-200">Kullanıcı bulunamadı.</div>`;
            }

            const userPosts = posts.filter(p => p.authorId === profileUser.id); // DÜZELTME: authorId ile filtrele
            const userPostsHtml = userPosts.map(post => createPostCard(post, false)).join('');
            
            const displayUrl = profileUser.url && profileUser.url.startsWith('http') 
                               ? profileUser.url 
                               : (profileUser.url ? `http://${profileUser.url}` : '');
            const displayUrlText = displayUrl.replace(/^https?:\/\//, '').split('/')[0];
            
            // Profil Resmi Alanı
            const avatarHtml = profileUser.profilePicture 
                ? `<img src="${profileUser.profilePicture}" alt="Profil Resmi" class="w-full h-full rounded-full object-cover border-4 border-white shadow-md cursor-pointer" onclick="showPictureModal('${profileUser.profilePicture}')">`
                : `<div class="w-full h-full rounded-full bg-blue-600 flex items-center justify-center text-white text-3xl font-bold border-4 border-white shadow-md cursor-pointer" onclick="showPictureModal(null)">${profileUser.initial}</div>`;


            // Takip Et Butonu (Sadece başka bir kullanıcıysa göster)
            let actionButton = '';
            let messageButton = ''; // YENİ: Mesaj Gönder butonu
            
            if (!isMyProfile) {
                const isFollowing = currentUser.followingUsers.includes(profileUser.id);
                const buttonClass = isFollowing ? 'bg-gray-400 hover:bg-gray-500' : 'bg-blue-600 hover:bg-blue-700';
                const buttonText = isFollowing ? 'Takibi Bırak' : 'Takip Et';
                
                actionButton = `
                    <button onclick="toggleFollow(${profileUser.id})" class="${buttonClass} text-white px-6 py-2 rounded-full font-semibold transition flex items-center justify-center">
                        <i data-lucide="${isFollowing ? 'user-x' : 'user-plus'}" class="w-4 h-4 mr-2"></i> ${buttonText}
                    </button>
                `;

                messageButton = `
                    <button onclick="startChat(${profileUser.id})" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-full font-semibold transition flex items-center justify-center">
                        <i data-lucide="send" class="w-4 h-4 mr-2"></i> Mesaj Gönder
                    </button>
                `;
            }

            // Profil içi navigasyon (Sadece kendi profilinizde görünür)
            let profileSubNav = '';
            if (isMyProfile) {
                profileSubNav = `
                    <div class="flex justify-center space-x-4 mb-8 pt-4 border-t border-b border-gray-100">
                        <button onclick="changeView('liked')" class="flex flex-col items-center p-3 rounded-xl hover:bg-red-50 text-gray-700 hover:text-red-600 transition">
                            <i data-lucide="heart" class class="w-5 h-5"></i>
                            <span class="text-xs mt-1">Beğenilenler</span>
                        </button>
                        <button onclick="changeView('saved')" class="flex flex-col items-center p-3 rounded-xl hover:bg-blue-50 text-gray-700 hover:text-blue-600 transition">
                            <i data-lucide="bookmark" class="w-5 h-5"></i>
                            <span class="text-xs mt-1">Kaydedilenler</span>
                        </button>
                    </div>
                `;
            }


            return `
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200 text-center">
                    <div class="relative w-24 h-24 mx-auto mb-4">
                        ${avatarHtml}
                    </div>

                    <h2 class="text-2xl font-bold text-gray-800">${profileUser.name}</h2>
                    <p class="text-md text-gray-500 mb-6">${profileUser.username}</p>
                    
                    <p class="text-gray-700 mb-2 whitespace-pre-line text-left">${profileUser.bio || 'Henüz bir biyografi eklenmedi.'}</p>

                    ${profileUser.url ? `
                        <a href="${displayUrl}" target="_blank" class="text-blue-600 hover:text-blue-500 hover:underline transition duration-150 mb-8 flex items-center justify-start space-x-1 mx-auto max-w-fit">
                            <i data-lucide="link" class="w-4 h-4"></i>
                            <span>${displayUrlText}</span>
                        </a>
                    ` : `<p class="text-sm text-gray-500 mb-8 text-left">Henüz bir link eklenmedi.</p>`}


                    <div class="flex justify-around border-t border-b py-4 ${isMyProfile ? 'mb-0' : 'mb-8'}">
                        <div>
                            <p class="text-xl font-bold text-gray-800">${profileUser.postCount}</p>
                            <p class="text-sm text-gray-500">Gönderi</p>
                        </div>
                        <!-- Takipçi sayacını tıklanabilir yap -->
                        <div onclick="viewFollowList(${profileUser.id}, 'followers')" class="cursor-pointer hover:bg-gray-50 p-2 -my-2 rounded-lg transition duration-150">
                            <p class="text-xl font-bold text-gray-800">${profileUser.followers}</p>
                            <p class="text-sm text-gray-500">Takipçi</p>
                        </div>
                        <!-- Takip edilen sayacını tıklanabilir yap -->
                        <div onclick="viewFollowList(${profileUser.id}, 'following')" class="cursor-pointer hover:bg-gray-50 p-2 -my-2 rounded-lg transition duration-150">
                            <p class="text-xl font-bold text-gray-800">${profileUser.following}</p>
                            <p class="text-sm text-gray-500">Takip Edilen</p>
                        </div>
                    </div>
                    
                    ${profileSubNav}

                    <div class="flex flex-col md:flex-row justify-center space-y-3 md:space-y-0 md:space-x-3 mt-8">
                        ${isMyProfile ? 
                            `<button onclick="showProfileEditModal()" class="bg-black text-white px-6 py-2 rounded-full font-semibold hover:bg-gray-800 transition">
                                Profili Düzenle
                            </button>
                            <button onclick="simulateLogout()" class="bg-red-500 text-white px-6 py-2 rounded-full font-semibold hover:bg-red-600 transition flex items-center justify-center">
                                 <i data-lucide="log-out" class="w-4 h-4 mr-2"></i> Çıkış Yap
                            </button>`
                            : `
                            ${actionButton}
                            ${messageButton}
                            `
                        }
                    </div>
                </div>

                <h3 class="text-xl font-bold text-gray-800 mt-10 mb-4">${isMyProfile ? 'Kendi Gönderileriniz' : `${profileUser.name} Gönderileri`} (${userPosts.length})</h3>
                <div id="user-posts-feed" class="space-y-6">
                    ${userPostsHtml.length > 0 ? userPostsHtml : `
                        <div class="bg-white p-5 rounded-xl text-center text-gray-500 shadow-md border border-gray-200">
                            Henüz gönderi yok.
                        </div>
                    `}
                </div>
            `;
        }
        
        // YENİ: Takipçi/Takip Edilen Listesi İçeriği
        function getFollowListContent(type) {
            if (!viewingUserId) return getFeedContent(); 

            const profileUser = getProfileData(viewingUserId);
            const list = getFollowList(viewingUserId, type);
            const title = type === 'followers' ? 'Takipçiler' : 'Takip Edilenler';
            const subtitle = profileUser.id === currentUser.id ? 'Sizin' : `${profileUser.name}'in`;

            const listHtml = list.map(user => {
                const isCurrentlyFollowing = currentUser.followingUsers.includes(user.id);
                const buttonText = isCurrentlyFollowing ? 'Takibi Bırak' : 'Takip Et';
                const buttonClass = isCurrentlyFollowing ? 'bg-gray-400 hover:bg-gray-500' : 'bg-blue-600 hover:bg-blue-700';
                
                // Avatar HTML
                const avatarHtml = user.profilePicture 
                    ? `<img src="${user.profilePicture}" alt="PP" class="w-12 h-12 rounded-full object-cover">`
                    : `<div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-lg">${user.initial}</div>`;
                
                // Takip butonu sadece listeyi görüntüleyen kişi (currentUser) ise ve kendi profili değilse gösterilir.
                const actionButton = (user.id !== currentUser.id) ? `
                    <button onclick="toggleFollow(${user.id})" class="text-white px-4 py-1 rounded-full text-sm font-semibold transition ${buttonClass}">
                        ${buttonText}
                    </button>` : '';

                return `
                    <div class="flex items-center justify-between p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="flex items-center space-x-3 cursor-pointer" onclick="viewProfile(${user.id})">
                            ${avatarHtml}
                            <div>
                                <p class="font-bold text-gray-800 hover:underline">${user.name}</p>
                                <p class="text-sm text-gray-500">${user.username}</p>
                            </div>
                        </div>
                        ${actionButton}
                    </div>
                `;
            }).join('');

            return `
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">${title} (${list.length})</h2>
                    <p class="text-md text-gray-500 mb-6">${subtitle} ${title} listesi.</p>
                    <button onclick="viewProfile(${viewingUserId})" class="text-blue-600 hover:underline text-sm font-semibold">
                         <i data-lucide="arrow-left" class="w-4 h-4 inline mr-1"></i> Profile Geri Dön
                    </button>
                </div>

                <div class="space-y-4 mt-6">
                    ${listHtml.length > 0 ? listHtml : `<p class="text-gray-500 text-center p-8">Bu listede kimse bulunmamaktadır.</p>`}
                </div>
            `;
        }

        function getSavedContent() {
            const savedPosts = posts.filter(p => p.isSaved);
            const savedPostsHtml = savedPosts.map(post => createPostCard(post, false)).join('');
            if (savedPosts.length === 0) {
                 return `
                    <div class="flex flex-col items-center justify-center min-h-[50vh] text-center bg-white p-8 rounded-xl shadow-lg border border-gray-200">
                        <i data-lucide="bookmark-x" class="w-16 h-16 text-gray-400 mb-4"></i>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Kaydedilen Gönderi Yok</h2>
                        <p class="text-gray-600 mb-4">Henüz kaydettiğiniz bir gönderi bulunmamaktadır.</p>
                        <button onclick="changeView('feed')" class="px-6 py-2 bg-blue-600 text-white font-bold rounded-full hover:bg-blue-700 transition duration-150">
                            Akışa Göz At
                        </button>
                    </div>
                `;
            }

            return `
                <h3 class="text-xl font-bold text-gray-800 mb-4">Kaydettiğiniz Gönderiler (${savedPosts.length})</h3>
                <div id="saved-feed" class="space-y-6">
                    ${savedPostsHtml}
                </div>
            `;
        }
        
        // --- RENDER FONKSİYONU ---
        function renderApp() {
            const contentArea = document.getElementById('content-area');
            const isUserLoggedIn = !!currentUserId; 
            
            const sidebarMenu = document.getElementById('sidebar-menu');
            const sidebarProfile = document.getElementById('sidebar-profile');
            const mobileNav = document.getElementById('mobile-nav');
            const mobileAuthButton = document.getElementById('mobile-auth-button');
            const mobilePostButton = document.getElementById('mobile-post-button');
            const mobileProfileButton = document.getElementById('mobile-profile-button');

            // Temizle ve Gizle
            sidebarMenu.innerHTML = '';
            sidebarProfile.innerHTML = '';
            mobileNav.innerHTML = '';
            mobileAuthButton.classList.add('hidden');
            if (mobilePostButton) mobilePostButton.classList.add('hidden'); 
            mobileProfileButton.classList.add('hidden');


            if (isUserLoggedIn) {
                // GİRİŞ YAPILMIŞ DURUM
                if (mobilePostButton) mobilePostButton.classList.remove('hidden'); 
                mobileProfileButton.classList.remove('hidden');
                
                // Mobil Profil Butonu
                if (currentUser.profilePicture) {
                    mobileProfileButton.innerHTML = `<img src="${currentUser.profilePicture}" alt="PP" class="w-full h-full rounded-full object-cover">`;
                    mobileProfileButton.classList.remove('text-white', 'text-xs', 'font-bold', 'bg-blue-600');
                } else {
                    mobileProfileButton.innerHTML = currentUser.initial;
                    mobileProfileButton.classList.add('text-white', 'text-xs', 'font-bold', 'bg-blue-600');
                }


                // Masaüstü Menü İçerikleri
                sidebarMenu.innerHTML = `
                    <a href="#" onclick="changeView('feed'); return false;" class="nav-link flex items-center p-3 rounded-xl hover:bg-blue-50 ${currentView === 'feed' && !viewingUserId ? 'bg-blue-100 text-blue-600 font-semibold' : 'text-gray-700'}">
                        <i data-lucide="home" class="w-5 h-5 mr-3"></i> Ana Sayfa
                    </a>
                    <a href="#" onclick="changeView('search'); return false;" class="nav-link flex items-center p-3 rounded-xl hover:bg-gray-100 ${currentView === 'search' ? 'bg-gray-100 text-gray-900 font-semibold' : 'text-gray-700'}">
                        <i data-lucide="search" class="w-5 h-5 mr-3"></i> Ara
                    </a>
                    <a href="#" onclick="changeView('messages'); return false;" class="nav-link flex items-center p-3 rounded-xl hover:bg-purple-50 ${currentView === 'messages' ? 'bg-purple-100 text-purple-600 font-semibold' : 'text-gray-700'}">
                        <i data-lucide="message-square" class="w-5 h-5 mr-3"></i> Mesajlar
                    </a>
                    <a href="#" onclick="viewProfile(${currentUser.id}); return false;" class="nav-link flex items-center p-3 rounded-xl hover:bg-gray-100 ${currentView === 'profile' && viewingUserId === currentUser.id ? 'bg-gray-100 text-gray-900 font-semibold' : 'text-gray-700'}">
                        <i data-lucide="user" class="w-5 h-5 mr-3"></i> Profil
                    </a>
                    <button onclick="showPostModal()" class="w-full mt-4 bg-blue-600 text-white p-3 rounded-full shadow-lg hover:bg-blue-700 transition duration-150 font-bold flex items-center justify-center">
                        <i data-lucide="plus" class="w-5 h-5 mr-2"></i> Gönderi At
                    </button>
                `;

                // Masaüstü Profil Alanı
                sidebarProfile.innerHTML = `
                    <a href="#" onclick="viewProfile(${currentUser.id}); return false;" class="nav-link flex items-center p-3 rounded-xl hover:bg-gray-100 text-gray-700">
                        <div class="w-8 h-8 rounded-full bg-blue-600 mr-3 flex items-center justify-center text-white text-xs font-bold">
                            ${currentUser.profilePicture ? `<img src="${currentUser.profilePicture}" alt="PP" class="w-full h-full rounded-full object-cover">` : currentUser.initial}
                        </div>
                        <div>
                            <p class="text-sm font-semibold">${currentUser.name}</p>
                            <p class="text-xs text-gray-500">${currentUser.username}</p>
                        </div>
                    </a>
                    <button onclick="simulateLogout()" class="mt-2 w-full text-center text-red-500 text-sm py-2 hover:bg-red-50 rounded-lg transition">
                        <i data-lucide="log-out" class="w-4 h-4 inline mr-1"></i> Çıkış Yap
                    </button>
                `;

                //  Ana Akış İçeriği (İçerik anahtarlama)
                let contentHTML = '';
                let titleText = '';

                if (currentView === 'profile') {
                    contentHTML = getProfileContent();
                    const profile = getProfileData(viewingUserId);
                    titleText = viewingUserId === currentUser.id ? 'Profilim' : `${profile ? profile.name : 'Profil'} Sayfası`;
                } else if (currentView === 'saved') {
                    contentHTML = getSavedContent();
                    titleText = 'Kaydedilenler';
                } else if (currentView === 'liked') {
                    contentHTML = getLikedContent();
                    titleText = 'Beğenilenler';
                } else if (currentView === 'messages') {
                    contentHTML = getMessagesContent();
                    titleText = 'Mesajlar';
                } else if (currentView === 'search') {
                    contentHTML = getSearchContent();
                    titleText = 'Kullanıcı Ara';
                } else if (currentView === 'followers') {
                    contentHTML = getFollowListContent('followers');
                    const profile = getProfileData(viewingUserId);
                    titleText = profile.id === currentUser.id ? 'Takipçilerim' : `${profile.name} Takipçileri`;
                } else if (currentView === 'following') {
                    contentHTML = getFollowListContent('following');
                    const profile = getProfileData(viewingUserId);
                    titleText = profile.id === currentUser.id ? 'Takip Ettiklerim' : `${profile.name} Takip Ettikleri`;
                } else { 
                    contentHTML = getFeedContent();
                    titleText = 'Ana Akış';
                }
                
                document.getElementById('main-title').textContent = titleText;
                
                // İçerik geçişini başlat
                contentArea.classList.remove('content-active');
                contentArea.classList.add('content-fading');

                setTimeout(() => {
                    contentArea.innerHTML = contentHTML;
                    // Yeni içeriği yükledikten sonra fade-in tetikle
                    contentArea.classList.remove('content-fading');
                    contentArea.classList.add('content-active');
                    lucide.createIcons();
                }, 300); // CSS transition süresi kadar bekler

                //  Mobil Alt Navigasyon
                mobileNav.innerHTML = `
                    <a href="#" onclick="changeView('feed'); return false;" class="flex flex-col items-center p-1 nav-link ${currentView === 'feed' ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600'}">
                        <i data-lucide="home" class="w-6 h-6"></i>
                        <span class="text-xs">Ana Sayfa</span>
                    </a>
                    <a href="#" onclick="changeView('search'); return false;" class="flex flex-col items-center p-1 nav-link ${currentView === 'search' ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600'}">
                        <i data-lucide="search" class="w-6 h-6"></i>
                        <span class="text-xs">Ara</span>
                    </a>
                    <button onclick="showPostModal()" class="flex flex-col items-center text-gray-700 p-1 nav-link hover:text-blue-600">
                        <i data-lucide="plus-circle" class="w-6 h-6"></i>
                        <span class="text-xs">Gönderi</span>
                    </button>
                    <a href="#" onclick="changeView('messages'); return false;" class="flex flex-col items-center p-1 nav-link ${currentView === 'messages' ? 'text-purple-600 font-semibold' : 'text-gray-700 hover:text-purple-600'}">
                        <i data-lucide="message-square" class="w-6 h-6"></i>
                        <span class="text-xs">Mesaj</span>
                    </a>
                    <!-- BEĞENİ VE KAYDET PROFİL İÇİNE TAŞINDI -->
                    <a href="#" onclick="viewProfile(${currentUser.id}); return false;" class="flex flex-col items-center p-1 nav-link ${currentView === 'profile' || currentView === 'followers' || currentView === 'following' || currentView === 'saved' || currentView === 'liked' ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600'}">
                        <i data-lucide="user" class="w-6 h-6"></i>
                        <span class="text-xs">Profil</span>
                    </a>
                `;

            } else {
                // ÇIKIŞ YAPILMIŞ DURUM
                document.getElementById('main-title').textContent = 'Hoş Geldiniz';
                mobileAuthButton.classList.remove('hidden');
    
                //  Karşılama Ekranı
                contentArea.innerHTML = `
                    <div class="flex flex-col items-center justify-center min-h-[60vh] text-center bg-white p-8 rounded-xl shadow-lg border-2 border-blue-500 border-solid">
                        <i data-lucide="zap" class="w-16 h-16 text-blue-600 mb-4"></i>
                        <h2 class="text-3xl font-extrabold text-gray-800 mb-2">Sosyal Akış'a Hoş Geldiniz!</h2>
                        <p class="text-lg text-gray-600 mb-6">Akışı görebilmek, gönderi oluşturabilmek ve diğer kullanıcılarla etkileşim kurabilmek için lütfen giriş yapın veya hemen yeni bir hesap oluşturun.</p>
                        <div class="flex space-x-4">
                            <button onclick="showAuthModal('login')" class="px-8 py-3 bg-green-600 text-white font-bold rounded-full shadow-lg hover:bg-blue-700 transition duration-150">
                                Giriş Yap
                            </button>
                            <button onclick="showAuthModal('signup')" class="px-8 py-3 bg-blue-600 text-white font-bold rounded-full shadow-lg hover:bg-gray-800 transition duration-150">
                                Üye Ol
                            </button>
                        </div>
                    </div>
                `;
                contentArea.classList.remove('content-fading');
                contentArea.classList.add('content-active');

                // 3. Mobil Alt Navigasyon (Sadece Giriş Butonu)
                mobileNav.innerHTML = `
                    <button onclick="showAuthModal('login')" class="w-full flex justify-center py-2 bg-blue-50 text-blue-600 font-semibold rounded-lg">
                        <i data-lucide="log-in" class="w-6 h-6 mr-2"></i> Giriş Yap
                    </button>
                `;
            }

            // Yeni eklenen ikonları tekrar yükle
            lucide.createIcons(); 
        }

        // --- GÖNDERİ VE ETKİLEŞİM İŞLEMLERİ ---

        // YENİ: Gönderi için dosya girişini tetikler
        function triggerPostFileSelect() {
            if (!currentUserId || !postFileInput) return;
            postFileInput.click();
        }

        // YENİ: Gönderi için dosya seçimi gerçekleştiğinde çalışır (İmaj ve Video)
        function handlePostMediaSelect(event) {
            const file = event.target.files[0];
            if (file) {
                if (!file.type.match('image.*|video.*')) {
                    alertUser("Lütfen sadece bir resim veya video dosyası seçin.");
                    event.target.value = ''; 
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = (e) => {
                    currentPostMedia = e.target.result;
                    updatePostModalMediaDisplay(file.type.startsWith('video/')); // Dosya tipini kontrol et
                };
                reader.readAsDataURL(file);
            }
        }

        // YENİ: Gönderi Resmini/Videosunu Modal'dan kaldırır
        function removePostMedia() {
            currentPostMedia = null;
            if (postFileInput) postFileInput.value = '';
            updatePostModalMediaDisplay();
        }

        // YENİ: Gönderi modalındaki medya önizlemesini günceller
        function updatePostModalMediaDisplay(isVideo = false) {
            const previewArea = document.getElementById('post-media-preview-area');
            const previewContainer = document.getElementById('media-preview-container');
            
            if (currentPostMedia) {
                if (isVideo) {
                     previewContainer.innerHTML = `<video src="${currentPostMedia}" controls class="w-full h-full object-contain rounded-md" style="max-height: 192px;"></video>`;
                } else {
                     previewContainer.innerHTML = `<img src="${currentPostMedia}" alt="Resim Önizlemesi" class="w-full h-auto max-h-48 object-contain rounded-md mb-2">`;
                }
                
                previewArea.classList.remove('hidden');
                document.getElementById('select-post-image-button').classList.add('hidden');
            } else {
                previewArea.classList.add('hidden');
                previewContainer.innerHTML = '';
                document.getElementById('select-post-image-button').classList.remove('hidden');
            }
        }

        async function postFromModal() {
            const content = document.getElementById('modal-post-input').value.trim();
            
            if (!content && !currentPostMedia) {
                alertUser("Lütfen bir şeyler yazın veya bir resim/video ekleyin.");
                return;
            }

            const newPostId = Date.now();
            const now = new Date();
            const timeString = `${now.toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit' })} • Şimdi`;

            const newPostObject = {
                id: newPostId,
                authorId: currentUser.id, // Kendi ID'mizi ekledik
                author: currentUser.name,
                handle: currentUser.username,
                initial: currentUser.initial,
                time: timeString,
                content: content.replace(/\n/g, '<br>'),
                likes: 0,
                isLiked: false,
                isSaved: false,
                color: 'bg-blue-600',
                imageUrl: currentPostMedia, // Medya Base64/URL'sini tutar
                profilePicture: currentUser.profilePicture 
            };
            
            // Yeni gönderiyi listeye ekle
            posts.unshift(newPostObject);
            currentUser.postCount++;
            saveState(); 

            // Akışı yeniden çiz
            document.getElementById('modal-post-input').value = ''; 
            hidePostModal();
            renderApp();
            alertUser("Gönderi başarıyla paylaşıldı!");

            currentPostMedia = null; // Resmi/Videoyu temizle
        }
        
        // Gönderi Kartını oluşturur
        function createPostCard(post, isSavedPage = false) {
            const isUserPost = currentUserId && currentUser.id === post.authorId;
            const likeIcon = post.isLiked ? 'heart-fill' : 'heart';
            const likeColor = post.isLiked ? 'text-red-500' : 'text-gray-500';
            const saveIcon = post.isSaved ? 'bookmark-check' : 'bookmark';
            const saveColor = post.isSaved ? 'text-blue-500' : 'text-gray-500';
            const saveText = post.isSaved ? 'Kaydedildi' : 'Kaydet';
            
            const saveAction = isSavedPage 
                ? `<div class="flex items-center space-x-2 text-red-500 hover:text-red-600 post-action" onclick="toggleSave(this, ${post.id}, true)">
                    <i data-lucide="bookmark-minus" class="w-5 h-5"></i>
                    <span class="text-sm">Kaldır</span>
                </div>`
                : `<div class="flex items-center space-x-2 ${saveColor} hover:text-blue-500 post-action" onclick="toggleSave(this, ${post.id}, false)">
                    <i data-lucide="${saveIcon}" class="w-5 h-5"></i>
                    <span class="text-sm">${saveText}</span>
                </div>`;

            const avatarHtml = post.profilePicture 
                ? `<img src="${post.profilePicture}" alt="PP" class="w-10 h-10 rounded-full object-cover mr-3">`
                : `<div class="w-10 h-10 rounded-full bg-blue-600 mr-3 flex items-center justify-center text-white font-bold">${post.initial || post.author.charAt(0).toUpperCase()}</div>`;
            
            // Yazar bilgilerini tıklanabilir hale getir
            const authorClickable = post.authorId ? `onclick="viewProfile(${post.authorId})"` : '';


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

            // Medya (Resim/Video) gösterimi
            let mediaTag = '';
            if (post.imageUrl) {
                if (post.imageUrl.startsWith('data:video/')) {
                    mediaTag = `<video src="${post.imageUrl}" controls class="w-full h-auto max-h-96 rounded-lg mb-4 object-cover"></video>`;
                } else {
                    mediaTag = `<img src="${post.imageUrl}" 
                                 onerror="this.onerror=null; this.src='https://placehold.co/600x300/3B82F6/ffffff?text=Medya+Yüklenemedi';"
                                 alt="Gönderi Resmi" 
                                 class="w-full h-auto rounded-lg mb-4 object-cover">`;
                }
            }


            return `
                <div class="post-card bg-white p-5 rounded-xl shadow-md border border-gray-200" data-post-id="${post.id}">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-start cursor-pointer" ${authorClickable}>
                            ${avatarHtml}
                            <div>
                                <p class="font-bold text-gray-800 hover:underline">${post.author}</p>
                                <p class="text-sm text-gray-500 hover:underline">${post.handle} • ${post.time}</p>
                            </div>
                        </div>
                        ${optionsButton}
                    </div>
                    <p class="text-gray-900 mb-4 whitespace-pre-line">${post.content}</p>
                    
                    ${mediaTag}

                    <!-- Etkileşimler -->
                    <div class="flex space-x-6 border-t pt-3 border-gray-100">
                        <div class="flex items-center space-x-2 ${likeColor} hover:text-red-500 post-action" onclick="toggleLike(this, ${post.id})">
                            <i data-lucide="${likeIcon}" class="w-5 h-5"></i>
                            <span class="text-sm">${post.likes}</span>
                        </div>
                        ${saveAction}
                    </div>
                </div>
            `;
        }

        function toggleLike(element, postId) {
            if (!currentUserId) {
                alertUser("Beğenmek için lütfen giriş yapın.");
                showAuthModal('login');
                return;
            }
            
            const post = posts.find(p => p.id === postId);
            if (!post) return;

            const countElement = element.querySelector('span');
            if (!countElement) return;

            
            if (post.isLiked) {
                post.likes--;
                post.isLiked = false;
                element.classList.remove('text-red-500');
                element.classList.add('text-gray-500');
                updateIcon(element, 'heart', countElement, post.likes); 
            } else {
                post.likes++;
                post.isLiked = true;
                element.classList.remove('text-gray-500');
                element.classList.add('text-red-500');
                updateIcon(element, 'heart-fill', countElement, post.likes); 
            }
            
            saveState();

            // Beğenilenler sayfasındaysak yeniden render et
            if (currentView === 'liked') {
                renderApp();
            }
        }

        function toggleSave(element, postId, isSavedPage) {
            if (!currentUserId) {
                alertUser("Kaydetmek için lütfen giriş yapın.");
                showAuthModal('login');
                return;
            }
            
            const post = posts.find(p => p.id === postId);
            if (!post) return;

            // ... (Kalan toggleSave mantığı aynı kalacak)
            const textElement = element.querySelector('span');
            
            if (isSavedPage) {
                const postIndex = posts.findIndex(p => p.id === postId);
                if (postIndex > -1) {
                    posts[postIndex].isSaved = false; // Ana listede durumu değiştir
                }
                
                const postCard = element.closest('.post-card');
                if (postCard) { postCard.remove(); }
                
                // Eğer son gönderiyi kaldırdıysak, Kaydedilenler ekranını yeniden render et
                if (posts.filter(p => p.isSaved).length === 0) {
                     renderApp(); 
                }
                alertUser("Gönderi Kaydedilenlerden Kaldırıldı.");
                saveState();
                return;
            }
            
            if (post.isSaved) {
                post.isSaved = false;
                
                element.classList.remove('text-blue-500');
                element.classList.add('text-gray-500');
                textElement.textContent = 'Kaydet';
                updateIcon(element, 'bookmark', textElement);
                alertUser("Gönderi Kaydedilenlerden Kaldırıldı.");

            } else {
                post.isSaved = true;

                element.classList.remove('text-gray-500');
                element.classList.add('text-blue-500');
                textElement.textContent = 'Kaydedildi';
                updateIcon(element, 'bookmark-check', textElement);
                alertUser("Gönderi Kaydedildi!");
            }
            
            saveState();

            if (currentView === 'saved') { renderApp(); }
        }

        // Gönderi Seçenekleri Menüsü Yönetimi
        function togglePostOptions(postId, buttonElement) {
            document.querySelectorAll('[id^="options-menu-"]').forEach(menu => {
                if (menu.id !== `options-menu-${postId}`) {
                    menu.classList.add('hidden');
                }
            });

            const menu = document.getElementById(`options-menu-${postId}`);
            if (menu) {
                menu.classList.toggle('hidden');
            }
        }

        document.addEventListener('click', (event) => {
            if (!event.target.closest('[onclick^="togglePostOptions"]') && !event.target.closest('[id^="options-menu-"]')) {
                document.querySelectorAll('[id^="options-menu-"]').forEach(menu => {
                    menu.classList.add('hidden');
                });
            }
        });

        function hidePostOptions(postId) {
            const menu = document.getElementById(`options-menu-${postId}`);
            if (menu) {
                menu.classList.add('hidden');
            }
        }
        
        // Gönderi Modalını Göster/Gizle
        function showPostModal(isEditing = false) {
            if (!currentUserId) {
                alertUser("Gönderi atmak için önce giriş yapmalısınız.");
                showAuthModal('login');
                return;
            }
            const modal = document.getElementById('post-modal');
            
            // Yeni Gönderi varsayılan değerleri
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

            // Modal kapatıldığında düzenleme durumunu sıfırla
            editingPostId = null;
            currentPostMedia = null;
            if (postFileInput) postFileInput.value = '';
        }

        // GÖNDERİ YÖNETİMİ (SİLME/DÜZENLEME) 

        function prepareEditPost(postId) {
            const post = posts.find(p => p.id === postId);
            if (!post) return;
            editingPostId = postId;
            hidePostOptions(postId);
            showPostModal(true);
            document.getElementById('modal-post-input').value = post.content.replace(/<br>/g, '\n');
            document.getElementById('post-modal-title').textContent = 'Gönderiyi Düzenle'; 
            document.getElementById('modal-post-button').textContent = 'Kaydet';
            currentPostMedia = post.imageUrl || null; 
            updatePostModalMediaDisplay(currentPostMedia ? currentPostMedia.startsWith('data:video/') : false); // Medya tipini doğru ilet
            document.getElementById('modal-post-button').setAttribute('onclick', 'updatePost()');
        }

        function updatePost() {
            if (!editingPostId) return;
            const content = document.getElementById('modal-post-input').value.trim();
            if (!content && !currentPostMedia) {
                alertUser("Lütfen bir içerik veya bir resim ekleyin.");
                return;
            }

            const post = posts.find(p => p.id === editingPostId);
            if (post) {
                post.content = content.replace(/\n/g, '<br>');
                post.time = `${new Date().toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit' })} • Düzenlendi`;
                post.imageUrl = currentPostMedia; 
                post.profilePicture = currentUser.profilePicture;

                hidePostModal();
                saveState();
                renderApp();
                alertUser("Gönderi başarıyla güncellendi.");
            }
            
            editingPostId = null;
            currentPostMedia = null;
        }
        
        function deletePost(postId) {
            if (!confirm('Bu gönderiyi silmek istediğinizden emin misiniz?')) {
                hidePostOptions(postId);
                return;
            }
            
            const postIndex = posts.findIndex(p => p.id === postId);
            if (postIndex > -1) {
                posts.splice(postIndex, 1);
            }

            // Gonderi sayısını güncelle
            if (currentUser.postCount > 0) {
                currentUser.postCount--;
            }

            saveState();

            alertUser("Gönderi başarıyla silindi.");
            
            if (currentView === 'profile' || currentView === 'saved' || currentView === 'liked' || currentView === 'messages' || currentView === 'search') {
                renderApp();
            } else {
                // Sadece DOM'dan kaldır
                const postElement = document.querySelector(`.post-card[data-post-id="${postId}"]`);
                if (postElement) { postElement.remove(); }
            }
        }
        
        // YARDIMCI FONKSİYONLAR
        function updateIcon(element, newIconName, textElement, newText = null) {
            const oldIconElement = element.querySelector('svg') || element.querySelector('i');
            if (oldIconElement) { oldIconElement.remove(); }
            const newIconElement = document.createElement('i');
            newIconElement.className = 'w-5 h-5'; 
            newIconElement.setAttribute('data-lucide', newIconName);
            element.insertBefore(newIconElement, textElement);
            lucide.createIcons({ parent: element });
            if (newText !== null) { textElement.textContent = newText; }
        }
        
        // BAŞLATMA 
        document.addEventListener('DOMContentLoaded', () => {
            profileFileInput = document.getElementById('profile-picture-input');
            if (profileFileInput) { profileFileInput.addEventListener('change', handleFileSelect); }
            
            postFileInput = document.getElementById('post-image-input');
            // GÜNCEL: postFileInput için event listener
            if (postFileInput) { postFileInput.addEventListener('change', handlePostMediaSelect); } 

            loadState(); // Durumu yükle
            
            // Eğer giriş yapılmışsa, gönderi sayısını hesapla
            if (currentUserId) {
                currentUser.postCount = posts.filter(post => post.authorId === currentUser.id).length;
            }

            lucide.createIcons(); 
            renderApp();
        });
