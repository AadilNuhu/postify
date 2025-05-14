<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Network Navbar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/a2e0b8e7d5.js" crossorigin="anonymous"></script>
</head>

<body class="bg-white text-gray-900 dark:bg-gray-900 dark:text-white transition duration-300">
    <!-- Top Navbar -->
    <nav class="flex items-center justify-between px-4 py-2 shadow-md bg-white dark:bg-gray-800">
        <!-- Left Logo -->
        <div class="flex items-center gap-4">
            <img src="/logo.png" alt="Logo" class="w-10 h-10 rounded-full">
            <span class="text-xl font-semibold">SocialConnect</span>
        </div>

        <!-- Search Bar -->
        <div class="hidden md:flex flex-1 justify-center">
            <input type="text" placeholder="Search..." class="w-2/3 px-4 py-1 rounded-full border border-gray-300 focus:outline-none focus:ring dark:bg-gray-700 dark:border-gray-600" />
        </div>

        <!-- Right Menu -->
        <div class="flex items-center gap-6">
            <a href="#" class="hover:text-blue-600"><i class="fas fa-home"></i></a>
            <a href="#" class="hover:text-blue-600"><i class="fas fa-edit"></i></a>
            <a href="#" class="hover:text-blue-600"><i class="fas fa-bell"></i></a>
            <a href="#" class="hover:text-blue-600"><i class="fas fa-envelope"></i></a>

            <!-- Dark Mode Toggle -->
            <button id="theme-toggle" class="focus:outline-none">
                <i id="theme-icon" class="fas fa-moon"></i>
            </button>

            <!-- User Dropdown -->
            <div class="relative group">
                <button class="flex items-center gap-2 focus:outline-none">
                    <img src="/avatar.jpg" alt="Avatar" class="w-8 h-8 rounded-full">
                    <span class="hidden md:inline-block font-medium">
                        <?php echo $_SESSION['username'] ?? 'Guest'; ?>
                    </span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-md shadow-lg opacity-0 group-hover:opacity-100 transition-opacity">
                    <a href="profile.php" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">Profile</a>
                    <a href="dashboard.php" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">Dashboard</a>
                    <a href="logout.php" class="block px-4 py-2 text-red-600 hover:bg-gray-100 dark:hover:bg-gray-600">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Left Sidebar (Desktop Only) -->
    <div class="hidden lg:flex flex-col w-64 h-screen fixed top-0 left-0 pt-20 px-4 bg-white dark:bg-gray-900 border-r dark:border-gray-700">
        <a href="#" class="py-2 px-4 rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2"><i class="fas fa-book-open"></i> Community Blogs</a>
        <a href="#" class="py-2 px-4 rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2"><i class="fas fa-users"></i> Friends</a>
        <a href="#" class="py-2 px-4 rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2"><i class="fas fa-hashtag"></i> Topics</a>
        <a href="#" class="py-2 px-4 rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2"><i class="fas fa-compass"></i> Explore</a>
        <a href="#" class="py-2 px-4 rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2"><i class="fas fa-cog"></i> Settings</a>
        <a href="logout.php" class="py-2 px-4 text-red-600 rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- JavaScript: Dark Mode Toggle -->
    <script>
        const toggle = document.getElementById('theme-toggle');
        const icon = document.getElementById('theme-icon');
        const root = document.documentElement;

        if (localStorage.theme === 'dark') {
            document.body.classList.add('dark');
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
        }

        toggle.addEventListener('click', () => {
            document.body.classList.toggle('dark');
            const isDark = document.body.classList.contains('dark');
            icon.classList.toggle('fa-sun', isDark);
            icon.classList.toggle('fa-moon', !isDark);
            localStorage.theme = isDark ? 'dark' : 'light';
        });
    </script>
</body>

</html>