<?php
// Fungsi untuk cek menu aktif
function is_active($page_name)
{
    $current_page = basename($_SERVER['PHP_SELF']);
    return ($current_page == $page_name) ? true : false;
}
?>

<script>
    // Cek status sidebar dari memori browser SEBELUM halaman dirender
    // Ini mencegah sidebar "berkedip" (besar lalu kecil tiba-tiba) saat refresh
    if (localStorage.getItem('sidebarState') === 'collapsed') {
        document.documentElement.classList.add('sidebar-closed');
    }
</script>

<style>
    /* CSS Scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.1);
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 4px;
    }

    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }

    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    /* PENTING: Style Sidebar saat mode "Closed/Mini" */
    /* Kita gunakan class di tag HTML agar aktif instan */
    html.sidebar-closed #desktop-sidebar {
        width: 5rem;
        /* Lebar jadi kecil (w-20) */
        overflow: visible;
        /* Agar tooltip bisa muncul keluar */
    }

    /* Sembunyikan elemen saat mode mini */
    html.sidebar-closed #desktop-sidebar .sidebar-text,
    html.sidebar-closed #desktop-sidebar #sidebar-title,
    html.sidebar-closed #desktop-sidebar .sidebar-divider span {
        opacity: 0;
        width: 0;
        display: none;
    }

    html.sidebar-closed #desktop-sidebar .sidebar-divider div {
        background-color: rgba(255, 255, 255, 0.2);
    }

    html.sidebar-closed #desktop-sidebar a {
        justify-content: center;
        padding-left: 0;
        padding-right: 0;
    }

    /* Tooltip hanya muncul saat mode mini & di-hover */
    html.sidebar-closed #desktop-sidebar .sidebar-tooltip {
        display: none;
    }

    html.sidebar-closed #desktop-sidebar a:hover .sidebar-tooltip {
        display: block;
    }
</style>

<aside id="desktop-sidebar"
    class="bg-[#066d70] text-white hidden md:flex flex-col flex-shrink-0 transition-[width] duration-300 h-screen sticky top-0 z-40 w-64 shadow-xl font-sans">

    <div
        class="h-16 flex items-center justify-between px-5 bg-[#055a5d] border-b border-[#078d91]/30 flex-shrink-0 overflow-hidden">
        <div class="flex items-center gap-3 whitespace-nowrap">
            <div
                class="w-9 h-9 min-w-[2.25rem] bg-white/10 rounded-lg flex items-center justify-center text-white backdrop-blur-sm shadow-inner">
                <i class="fas fa-cube text-lg"></i>
            </div>
            <div id="sidebar-title" class="flex flex-col transition-opacity duration-300">
                <h2 class="text-lg font-bold tracking-wide leading-none">Admin</h2>
                <span class="text-[10px] text-emerald-200/80 font-medium tracking-wider uppercase">Panel Control</span>
            </div>
        </div>

        <button onclick="toggleDesktopSidebar()"
            class="w-6 h-6 flex items-center justify-center rounded hover:bg-white/10 text-emerald-200 transition focus:outline-none">
            <i id="toggle-icon" class="fas fa-chevron-left text-xs"></i>
        </button>
    </div>

    <nav class="flex-1 overflow-y-auto custom-scrollbar py-4 px-3 space-y-1">

        <?php
        function render_menu($link, $icon, $label)
        {
            $active = is_active($link);
            // Style active state
            $bg = $active ? 'bg-[#09AFB5] text-white shadow-md' : 'text-emerald-100 hover:bg-white/10 hover:text-white';

            echo '
            <a href="' . $link . '" class="relative flex items-center h-11 px-3 rounded-lg transition-colors duration-200 group ' . $bg . '">
                <div class="min-w-[1.5rem] flex justify-center">
                    <i class="' . $icon . ' text-lg transition-transform group-hover:scale-110"></i>
                </div>
                
                <span class="sidebar-text ml-3 text-sm font-medium whitespace-nowrap">' . $label . '</span>
                
                <div class="sidebar-tooltip hidden absolute left-14 top-1/2 -translate-y-1/2 bg-gray-900 text-white text-xs px-3 py-2 rounded shadow-xl z-[100] whitespace-nowrap border border-gray-700 pointer-events-none">
                    ' . $label . '
                    <div class="absolute top-1/2 -left-1 -translate-y-1/2 border-4 border-transparent border-r-gray-900"></div>
                </div>
            </a>';
        }

        render_menu('index.php', 'fas fa-home', 'Dashboard');
        render_menu('produk.php', 'fas fa-box', 'Kelola Produk');
        render_menu('kategori.php', 'fas fa-tags', 'Kategori');
        render_menu('program.php', 'fas fa-bullhorn', 'Program');
        render_menu('brands.php', 'fas fa-copyright', 'Brand');
        render_menu('banner.php', 'fas fa-images', 'Banner');
        render_menu('kelola_galeri.php', 'fas fa-photo-video', 'Galeri');

        if (isset($_SESSION['role']) && ($_SESSION['role'] == 'superadmin' || $_SESSION['role'] == 'super_admin')):
            ?>
            <div class="sidebar-divider pt-4 pb-2 px-3">
                <div class="h-px bg-emerald-600/50 mb-2"></div>
                <span class="sidebar-text text-[10px] text-emerald-300/70 font-bold uppercase tracking-widest pl-1">Super
                    Admin</span>
            </div>

            <?php
            render_menu('users.php', 'fas fa-users', 'Kelola User');
            render_menu('profil.php', 'fas fa-store', 'Setting Profil');
            render_menu('backup.php', 'fas fa-database', 'Backup DB');
            ?>
        <?php endif; ?>

        <div class="mt-6 pt-4 border-t border-emerald-600/30">
            <a href="logout"
                class="relative flex items-center h-11 px-3 rounded-lg text-red-200 hover:bg-red-500/20 hover:text-white transition group">
                <div class="min-w-[1.5rem] flex justify-center">
                    <i class="fas fa-sign-out-alt text-lg"></i>
                </div>
                <span class="sidebar-text ml-3 text-sm font-medium whitespace-nowrap">Logout</span>
                <div
                    class="sidebar-tooltip hidden absolute left-14 top-1/2 -translate-y-1/2 bg-red-900 text-white text-xs px-3 py-2 rounded shadow-xl z-[100] whitespace-nowrap border border-red-700 pointer-events-none">
                    Logout
                    <div class="absolute top-1/2 -left-1 -translate-y-1/2 border-4 border-transparent border-r-red-900">
                    </div>
                </div>
            </a>
        </div>

    </nav>
</aside>

<nav
    class="md:hidden fixed bottom-0 left-0 w-full bg-white z-50 border-t border-gray-200 pb-safe shadow-[0_-1px_3px_rgba(0,0,0,0.05)]">
    <div class="grid grid-cols-5 h-[60px] items-center">
        <a href="index"
            class="flex flex-col items-center justify-center w-full h-full space-y-1 active:scale-95 transition-transform group">
            <div class="relative">
                <i
                    class="fas fa-home text-xl <?= is_active('index.php') ? 'text-[#09AFB5]' : 'text-gray-400 group-hover:text-gray-600' ?>"></i>
                <?php if (is_active('index.php')): ?>
                    <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-1 h-1 bg-[#09AFB5] rounded-full"></div>
                <?php endif; ?>
            </div>
            <span
                class="text-[10px] font-medium <?= is_active('index.php') ? 'text-[#09AFB5]' : 'text-gray-500' ?>">Home</span>
        </a>

        <a href="produk"
            class="flex flex-col items-center justify-center w-full h-full space-y-1 active:scale-95 transition-transform group">
            <div class="relative">
                <i
                    class="fas fa-box text-xl <?= is_active('produk.php') ? 'text-[#09AFB5]' : 'text-gray-400 group-hover:text-gray-600' ?>"></i>
                <?php if (is_active('produk.php')): ?>
                    <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-1 h-1 bg-[#09AFB5] rounded-full"></div>
                <?php endif; ?>
            </div>
            <span
                class="text-[10px] font-medium <?= is_active('produk.php') ? 'text-[#09AFB5]' : 'text-gray-500' ?>">Produk</span>
        </a>

        <a href="kategori"
            class="flex flex-col items-center justify-center w-full h-full space-y-1 active:scale-95 transition-transform group">
            <div class="relative">
                <i
                    class="fas fa-tags text-xl <?= is_active('kategori.php') ? 'text-[#09AFB5]' : 'text-gray-400 group-hover:text-gray-600' ?>"></i>
                <?php if (is_active('kategori.php')): ?>
                    <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-1 h-1 bg-[#09AFB5] rounded-full"></div>
                <?php endif; ?>
            </div>
            <span
                class="text-[10px] font-medium <?= is_active('kategori.php') ? 'text-[#09AFB5]' : 'text-gray-500' ?>">Kategori</span>
        </a>

        <a href="program"
            class="flex flex-col items-center justify-center w-full h-full space-y-1 active:scale-95 transition-transform group">
            <div class="relative">
                <i
                    class="fas fa-bullhorn text-xl <?= is_active('program.php') ? 'text-[#09AFB5]' : 'text-gray-400 group-hover:text-gray-600' ?>"></i>
                <?php if (is_active('program.php')): ?>
                    <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-1 h-1 bg-[#09AFB5] rounded-full"></div>
                <?php endif; ?>
            </div>
            <span
                class="text-[10px] font-medium <?= is_active('program.php') ? 'text-[#09AFB5]' : 'text-gray-500' ?>">Program</span>
        </a>

        <div class="relative w-full h-full flex flex-col items-center justify-center group">
            <button onclick="toggleMobileMenu()"
                class="flex flex-col items-center justify-center w-full h-full space-y-1 active:scale-95 transition-transform focus:outline-none">
                <i class="fas fa-bars text-xl text-gray-400 group-hover:text-gray-600"></i>
                <span class="text-[10px] font-medium text-gray-500">Lainnya</span>
            </button>

            <div id="mobile-popup"
                class="hidden absolute bottom-[70px] right-2 bg-white shadow-2xl rounded-2xl border border-gray-100 w-56 flex-col overflow-hidden z-[60] origin-bottom-right animate-scale-up">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-100">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Menu Lainnya</p>
                </div>
                <div class="p-2 space-y-1 max-h-[60vh] overflow-y-auto">
                    <a href="brands"
                        class="flex items-center gap-3 px-3 py-2 text-sm text-gray-600 hover:bg-[#09AFB5]/10 hover:text-[#09AFB5] rounded-lg transition"><i
                            class="fas fa-copyright w-5 text-center"></i> Brand</a>
                    <a href="banner"
                        class="flex items-center gap-3 px-3 py-2 text-sm text-gray-600 hover:bg-[#09AFB5]/10 hover:text-[#09AFB5] rounded-lg transition"><i
                            class="fas fa-images w-5 text-center"></i> Banner</a>
                    <a href="kelola_galeri"
                        class="flex items-center gap-3 px-3 py-2 text-sm text-gray-600 hover:bg-[#09AFB5]/10 hover:text-[#09AFB5] rounded-lg transition"><i
                            class="fas fa-photo-video w-5 text-center"></i> Galeri</a>
                    <?php if (isset($_SESSION['role']) && ($_SESSION['role'] == 'superadmin' || $_SESSION['role'] == 'super_admin')): ?>
                        <div class="h-px bg-gray-100 my-1 mx-2"></div>
                        <p class="px-3 py-1 text-[10px] font-bold text-gray-400 uppercase">Super Admin</p>
                        <a href="users"
                            class="flex items-center gap-3 px-3 py-2 text-sm text-gray-600 hover:bg-[#09AFB5]/10 hover:text-[#09AFB5] rounded-lg transition"><i
                                class="fas fa-users w-5 text-center"></i> User</a>
                        <a href="profil"
                            class="flex items-center gap-3 px-3 py-2 text-sm text-gray-600 hover:bg-[#09AFB5]/10 hover:text-[#09AFB5] rounded-lg transition"><i
                                class="fas fa-store w-5 text-center"></i> Profil</a>
                        <a href="backup"
                            class="flex items-center gap-3 px-3 py-2 text-sm text-gray-600 hover:bg-[#09AFB5]/10 hover:text-[#09AFB5] rounded-lg transition"><i
                                class="fas fa-database w-5 text-center"></i> Backup</a>
                    <?php endif; ?>
                </div>
                <div class="p-2 border-t border-gray-100 bg-gray-50">
                    <a href="logout"
                        class="flex items-center gap-3 px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition font-bold"><i
                            class="fas fa-sign-out-alt w-5 text-center"></i> Logout</a>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
    // --- MOBILE ---
    function toggleMobileMenu() {
        const popup = document.getElementById('mobile-popup');
        popup.classList.toggle('hidden'); popup.classList.toggle('flex');
    }
    document.addEventListener('click', function (event) {
        const popup = document.getElementById('mobile-popup');
        const btn = document.querySelector('button[onclick="toggleMobileMenu()"]');
        if (popup && !popup.contains(event.target) && !btn.contains(event.target) && !popup.classList.contains('hidden')) {
            popup.classList.add('hidden'); popup.classList.remove('flex');
        }
    });

    // --- DESKTOP ---
    const toggleIcon = document.getElementById('toggle-icon');

    // Set Icon saat load
    if (document.documentElement.classList.contains('sidebar-closed')) {
        toggleIcon.className = 'fas fa-chevron-right text-xs';
    }

    function toggleDesktopSidebar() {
        // Toggle class di HTML tag
        document.documentElement.classList.toggle('sidebar-closed');

        // Simpan status & Ubah Icon
        if (document.documentElement.classList.contains('sidebar-closed')) {
            localStorage.setItem('sidebarState', 'collapsed');
            toggleIcon.className = 'fas fa-chevron-right text-xs';
        } else {
            localStorage.setItem('sidebarState', 'expanded');
            toggleIcon.className = 'fas fa-chevron-left text-xs';
        }
    }
</script>
<script>
    (function (global) {
        if (typeof (global) === "undefined") {
            throw new Error("window is undefined");
        }

        var _hash = "!";
        var noBackPlease = function () {
            global.location.href += "#";

            // Membuat delay sedikit agar browser 'tertipu'
            global.setTimeout(function () {
                global.location.href += "!";
            }, 50);
        };

        // Setiap kali interval waktu, paksa state history baru
        global.onhashchange = function () {
            if (global.location.hash !== _hash) {
                global.location.hash = _hash;
            }
        };

        global.onload = function () {
            noBackPlease();

            // Matikan fungsi backspace pada keyboard (kecuali di input field)
            document.body.onkeydown = function (e) {
                var elm = e.target.nodeName.toLowerCase();
                if (e.which === 8 && (elm !== 'input' && elm !== 'textarea')) {
                    e.preventDefault();
                }
                // Hentikan event bubbling
                e.stopPropagation();
            };
        }
    })(window);

    // Tambahan: Manipulasi History PushState
    history.pushState(null, null, location.href);
    window.onpopstate = function () {
        history.go(1);
    };
</script>

<style>
    @media (max-width: 768px) {
        body {
            padding-bottom: 80px !important;
        }
    }

    @keyframes scaleUp {
        from {
            opacity: 0;
            transform: scale(0.95) translateY(10px);
        }

        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .animate-scale-up {
        animation: scaleUp 0.2s ease-out forwards;
    }
</style>