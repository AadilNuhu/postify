<?php

// session_start();

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
    <title>Social Network Navbar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/a2e0b8e7d5.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body class="bg-white text-gray-900 dark:bg-gray-900 dark:text-white transition duration-300">
    <!-- Top Navbar -->
    <nav class="flex items-center justify-between px-4 py-2 shadow-md bg-white dark:bg-gray-800 sticky top-0 z-50">
        <!-- Left Logo and Hamburger -->
        <div class="flex items-center gap-4">
            <img src="Dpostify.png" alt="Logo" class="w-10 h-10 rounded-full">
            <span class="text-xl font-semibold">Postify</span>
            <!-- Hamburger Menu (Mobile Only) -->
            <button id="mobile-menu-toggle" class="lg:hidden text-xl focus:outline-none">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <!-- Search Bar -->
        <div class="hidden md:flex flex-1 justify-center">
            <input type="text" placeholder="Search..." class="w-2/3 px-4 py-1 rounded-full border border-gray-300 focus:outline-none focus:ring dark:bg-gray-700 dark:border-gray-600" />
        </div>

        <!-- Right Menu -->
        <div class="flex items-center gap-6">
            <a href="#" class="relative hover:text-blue-600"><i class="fas fa-home"></i></a>
            <a href="#" class="relative hover:text-blue-600"><i class="fas fa-edit"></i></a>
            <a href="#" class="relative hover:text-blue-600">
                <i class="fas fa-bell"></i>
                <span class="absolute -top-2 -right-2 text-xs bg-red-600 text-white rounded-full px-1">3</span>
            </a>
            <a href="#" class="relative hover:text-blue-600">
                <i class="fas fa-envelope"></i>
                <span class="absolute -top-2 -right-2 text-xs bg-red-600 text-white rounded-full px-1">5</span>
            </a>
            <!-- Dark Mode Toggle -->
            <button id="theme-toggle" class="focus:outline-none">
                <i id="theme-icon" class="fas"></i>
            </button>
            <!-- User Dropdown -->
            <div class="relative group">
                <button class="flex items-center gap-2 focus:outline-none">
                    <img src="<?php echo $_SESSION['avatar'] ?? 'images.jpeg'; ?>" alt="Avatar" class="w-8 h-8 rounded-full">
                    <span class="hidden md:inline-block font-medium"><?php echo $_SESSION['username'] ?? 'Guest'; ?></span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-md shadow-lg opacity-0 group-hover:opacity-100 transition-opacity">
                    <?php if (isset($_SESSION['username'])): ?>
                        <a href="profile.php" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">Profile</a>
                        <a href="dashboard.php" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">Dashboard</a>
                        <a href="../actions/logout.php" class="block px-4 py-2 text-red-600 hover:bg-gray-100 dark:hover:bg-gray-600">Logout</a>
                    <?php else: ?>
                        <a href="../pages/login.php" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">Login</a>
                        <a href="../pages/register.php" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu Drawer -->
    <div id="mobile-menu" class="lg:hidden fixed top-0 left-0 h-full w-64 bg-white dark:bg-gray-900 shadow transform -translate-x-full transition-transform z-50">
        <div class="p-4 border-b dark:border-gray-700 flex items-center justify-between">
            <span class="text-lg font-semibold">Menu</span>
            <button id="mobile-menu-close" class="text-xl"><i class="fas fa-times"></i></button>
        </div>
        <nav class="flex flex-col p-4 gap-2">
            <a href="#" class="py-2 px-4 rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2"><i class="fas fa-book-open"></i> Community Blogs</a>
            <a href="#" class="py-2 px-4 rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2"><i class="fas fa-users"></i> Friends</a>
            <a href="#" class="py-2 px-4 rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2"><i class="fas fa-hashtag"></i> Topics</a>
            <a href="#" class="py-2 px-4 rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2"><i class="fas fa-compass"></i> Explore</a>
            <a href="#" class="py-2 px-4 rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2"><i class="fas fa-cog"></i> Settings</a>
            <a href="logout.php" class="py-2 px-4 text-red-600 rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>

    <!-- Left Sidebar (Desktop Only) -->
    <div class="hidden lg:flex flex-col w-64 h-screen fixed top-0 left-0 pt-20 px-4 bg-white dark:bg-gray-900 border-r dark:border-gray-700">
        <a href="#" class="py-2 px-4 rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2"><i class="fas fa-book-open"></i> Community Blogs</a>
        <a href="#" class="py-2 px-4 rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2"><i class="fas fa-users"></i> Friends</a>
        <a href="#" class="py-2 px-4 rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2"><i class="fas fa-hashtag"></i> Topics</a>
        <a href="#" class="py-2 px-4 rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2"><i class="fas fa-compass"></i> Explore</a>
        <a href="#" class="py-2 px-4 rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2"><i class="fas fa-cog"></i> Settings</a>
        <a href="logout.php" class="py-2 px-4 text-red-600 rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- JavaScript -->
    <script>
        const toggle = document.getElementById('theme-toggle');
        const icon = document.getElementById('theme-icon');

        const updateIcon = (isDark) => {
            icon.classList.remove('fa-sun', 'fa-moon');
            icon.classList.add(isDark ? 'fa-sun' : 'fa-moon');
        };

        const isDarkMode = () => document.body.classList.contains('dark');

        // Initial icon setup
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.body.classList.add('dark');
            updateIcon(true);
        } else {
            document.body.classList.remove('dark');
            updateIcon(false);
        }

        toggle.addEventListener('click', () => {
            const isDark = document.body.classList.toggle('dark');
            localStorage.theme = isDark ? 'dark' : 'light';
            updateIcon(isDark);
        });

        // Mobile Menu Toggle
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileMenuClose = document.getElementById('mobile-menu-close');

        mobileMenuToggle.addEventListener('click', () => {
            mobileMenu.classList.remove('-translate-x-full');
        });

        mobileMenuClose.addEventListener('click', () => {
            mobileMenu.classList.add('-translate-x-full');
        });
    </script>
</body>

</html>
