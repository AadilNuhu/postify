<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ./pages/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postify Navbar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/a2e0b8e7d5.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <style>
        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
        }
        .sidebar.open {
            transform: translateX(0);
        }
        .overlay {
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        .overlay.open {
            display: block;
            opacity: 1;
        }
    </style>
    <script>
        // Immediately set the theme based on preference
        if (localStorage.getItem('color-theme') === 'dark' || (!localStorage.getItem('color-theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>

<body class="bg-white text-gray-900 dark:bg-gray-900 dark:text-white transition-colors duration-200">
    <nav class="flex items-center justify-between px-4 py-2 shadow-md bg-white dark:bg-gray-800 sticky top-0 z-50 transition-colors duration-200">
        <div class="flex items-center gap-6">
            <button id="sidebar-toggle" class="text-xl focus:outline-none text-gray-900 dark:text-white">
                <i class="fas fa-bars"></i>
            </button>
            <span class="text-xl font-semibold text-gray-900 dark:text-white">Postify</span>
        </div>

        <div class="hidden md:flex flex-1 justify-center">
            <input type="text" placeholder="Search..." class="w-2/3 px-4 py-1 rounded-full border border-gray-300 focus:outline-none focus:ring bg-white text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
        </div>

        <div class="flex items-center gap-6">
            <a href="#" class="relative hover:text-blue-600 text-gray-900 dark:text-white transition-colors"><i class="fas fa-home"></i></a>
            <a href="#" class="relative hover:text-blue-600 text-gray-900 dark:text-white transition-colors">
                <i class="fas fa-bell"></i>
                <span class="absolute -top-2 -right-2 text-xs bg-red-600 text-white rounded-full px-1">3</span>
            </a>
            <a href="#" class="relative hover:text-blue-600 text-gray-900 dark:text-white transition-colors">
                <i class="fas fa-envelope"></i>
                <span class="absolute -top-2 -right-2 text-xs bg-red-600 text-white rounded-full px-1">5</span>
            </a>
            <div class="relative group">
                <button class="flex items-center gap-2 focus:outline-none text-gray-900 dark:text-white transition-colors">
                    <img src="<?php echo $_SESSION['avatar'] ?? 'images.jpeg'; ?>" alt="Avatar" class="w-8 h-8 rounded-full">
                    <span class="hidden md:inline-block font-medium"><?php echo $_SESSION['username'] ?? 'Guest'; ?></span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-md shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 text-gray-900 dark:text-white">
                    <?php if (isset($_SESSION['username'])): ?>
                        <a href="./pages/profile.php" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">Profile</a>
                        <a href="dashboard.php" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">Dashboard</a>
                        <a href="./actions/logout.php" class="block px-4 py-2 text-red-600 hover:bg-gray-100 dark:hover:bg-gray-600">Logout</a>
                    <?php else: ?>
                        <a href="../pages/login.php" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">Login</a>
                        <a href="../pages/register.php" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Overlay -->
    <div id="overlay" class="overlay fixed inset-0 bg-black bg-opacity-50 z-40"></div>

    <!-- Sidebar -->
    <div id="sidebar" class="sidebar fixed top-0 left-0 h-full w-64 bg-white dark:bg-gray-900 shadow-lg z-50 text-gray-900 dark:text-white transition-colors duration-200">
        <div class="p-4 border-b dark:border-gray-700 flex items-center justify-between">
            <span class="text-lg font-semibold">Menu</span>
            <button id="sidebar-close" class="text-xl text-gray-900 dark:text-white"><i class="fas fa-times"></i></button>
        </div>
        <nav class="flex flex-col p-4 gap-2">
            <a href="#" class="py-2 px-4 rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2 transition-colors"><i class="fas fa-book-open"></i> Community Blogs</a>
            <a href="../index.php" class="py-2 px-4 rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2 transition-colors"><i class="fas fa-list"></i> View All Posts</a>
            <a href="./actions/create_post.php" class="py-2 px-4 rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2 transition-colors"><i class="fas fa-plus-circle"></i> Create Post</a>
            <a href="./actions/edit_post.php" class="py-2 px-4 rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2 transition-colors"><i class="fas fa-edit"></i> Edit Post</a>
            <a href="#" class="py-2 px-4 rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2 transition-colors"><i class="fas fa-users"></i> Friends</a>
            <a href="#" class="py-2 px-4 rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2 transition-colors"><i class="fas fa-hashtag"></i> Topics</a>
            <a href="./actions/logout.php" class="py-2 px-4 text-red-600 rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2 transition-colors"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>

    <!-- Theme Toggle Button -->
    <button id="theme-toggle" class="fixed bottom-4 right-4 bg-gray-200 dark:bg-gray-700 p-3 rounded-full shadow-lg transition-all duration-300 z-50">
        <i id="theme-icon" class="fas fa-moon dark:fa-sun text-gray-800 dark:text-yellow-300"></i>
    </button>

    <script>
        // Theme toggle functionality
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        
        const updateTheme = (isDark) => {
            if (isDark) {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
                themeIcon.classList.replace('fa-moon', 'fa-sun');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
                themeIcon.classList.replace('fa-sun', 'fa-moon');
            }
        };

        themeToggle.addEventListener('click', () => {
            const isDark = !document.documentElement.classList.contains('dark');
            updateTheme(isDark);
        });

        // Initialize theme based on preference
        if (localStorage.getItem('color-theme') === 'dark' || (!localStorage.getItem('color-theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            updateTheme(true);
        } else {
            updateTheme(false);
        }

        // Sidebar functionality
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebarClose = document.getElementById('sidebar-close');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        if (sidebarToggle && sidebarClose && sidebar && overlay) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.add('open');
                overlay.classList.add('open');
                document.body.style.overflow = 'hidden';
            });

            sidebarClose.addEventListener('click', () => {
                sidebar.classList.remove('open');
                overlay.classList.remove('open');
                document.body.style.overflow = '';
            });

            overlay.addEventListener('click', () => {
                sidebar.classList.remove('open');
                overlay.classList.remove('open');
                document.body.style.overflow = '';
            });
        }

        // Close sidebar when clicking on a link
        document.querySelectorAll('#sidebar a').forEach(link => {
            link.addEventListener('click', () => {
                sidebar.classList.remove('open');
                overlay.classList.remove('open');
                document.body.style.overflow = '';
            });
        });
    </script>
</body>

</html>