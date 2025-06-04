<?php
require_once './config.php';

// Fetch all posts with user info
$sql = "SELECT posts.*, users.username, users.avatar FROM posts 
        JOIN users ON posts.user_id = users.id 
        ORDER BY posts.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Postify - Share Your Moments</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            primary: {
              50: '#f0f9ff',
              100: '#e0f2fe',
              200: '#bae6fd',
              300: '#7dd3fc',
              400: '#38bdf8',
              500: '#0ea5e9',
              600: '#0284c7',
              700: '#0369a1',
              800: '#075985',
              900: '#0c4a6e',
            }
          }
        }
      }
    }
  </script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    
    body {
      font-family: 'Poppins', sans-serif;
    }
    
    .gradient-text {
      background: linear-gradient(90deg, #0ea5e9, #0c4a6e);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
    }
    
    .post-card {
      @apply transition-transform duration-200 ease-in-out;
    }
    
    .post-card:hover {
      @apply transform -translate-y-1 shadow-lg;
    }
    
    #theme-toggle:hover {
      @apply transform rotate-12;
    }
    
    /* Professional Sidebar Styles */
    .sidebar {
      @apply fixed top-0 left-0 h-full w-64 bg-white dark:bg-gray-800 shadow-xl z-40 transform -translate-x-full transition-all duration-300 ease-in-out;
    }
    
    .sidebar.open {
      @apply transform translate-x-0;
    }
    
    .overlay {
      @apply fixed inset-0 bg-black bg-opacity-50 z-30 opacity-0 invisible transition-all duration-300;
    }
    
    .overlay.open {
      @apply opacity-100 visible;
    }
    
    /* Hamburger Menu Animation */
    .hamburger-menu {
      @apply relative w-6 h-6 transition-all duration-300;
    }
    
    .hamburger-line {
      @apply absolute left-0 w-full h-0.5 bg-gray-900 dark:bg-white transition-all duration-300;
    }
    
    .hamburger-line:nth-child(1) {
      @apply top-1;
    }
    
    .hamburger-line:nth-child(2) {
      @apply top-1/2 -translate-y-1/2;
    }
    
    .hamburger-line:nth-child(3) {
      @apply bottom-1;
    }
    
    .hamburger-menu.open .hamburger-line:nth-child(1) {
      @apply top-1/2 -translate-y-1/2 rotate-45;
    }
    
    .hamburger-menu.open .hamburger-line:nth-child(2) {
      @apply opacity-0;
    }
    
    .hamburger-menu.open .hamburger-line:nth-child(3) {
      @apply bottom-1/2 translate-y-1/2 -rotate-45;
    }
  </style>
  <script>
    // Check for saved theme preference or use system preference
    if (localStorage.getItem('color-theme') === 'dark' || (!localStorage.getItem('color-theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      document.documentElement.classList.add('dark');
    } else {
      document.documentElement.classList.remove('dark');
    }
  </script>
</head>

<body class="min-h-screen flex flex-col bg-gray-50 dark:bg-gray-900">
  <?php include "./includes/navbar.php"; ?>

  <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div id="success-message" class="fixed top-20 right-4 z-50">
      <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-100 px-4 py-3 rounded-lg shadow-lg flex items-center">
        <i class="fas fa-check-circle mr-2"></i>
        <span>Post uploaded successfully!</span>
      </div>
    </div>
    <script>
      setTimeout(function() {
        var msg = document.getElementById('success-message');
        if (msg) msg.style.display = 'none';
      }, 3000);
    </script>
  <?php endif; ?>

  <main class="flex-1">
    <div class="container mx-auto px-4 py-8">
      <!-- Hero Section -->
      <section class="text-center mb-12">
        <h1 class="text-4xl md:text-5xl font-bold mb-4 gradient-text">Welcome to Postify</h1>
        <p class="text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
          Share your thoughts, moments, and creativity with the world. Connect with others through posts, images, and videos.
        </p>
        <div class="mt-6">
          <a href="./create_post.php" class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-full transition duration-300 shadow-lg hover:shadow-xl">
            <i class="fas fa-plus mr-2"></i> Create New Post
          </a>
        </div>
      </section>

      <!-- Posts Grid -->
      <section class="mb-12">
        <h2 class="text-2xl font-semibold mb-6 text-gray-800 dark:text-gray-200">Recent Posts</h2>
        
        <?php if ($result->num_rows === 0): ?>
          <div class="text-center py-12">
            <i class="fas fa-newspaper text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-xl font-medium text-gray-600 dark:text-gray-400">No posts yet</h3>
            <p class="text-gray-500 dark:text-gray-500 mt-2">Be the first to share something!</p>
          </div>
        <?php else: ?>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while ($post = $result->fetch_assoc()): ?>
              <div class="post-card bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
                <!-- Post Header -->
                <div class="p-4 flex items-center">
                  <img src="<?php echo htmlspecialchars($post['avatar'] ?? 'images.jpeg'); ?>" 
                       alt="User Avatar" 
                       class="w-10 h-10 rounded-full object-cover border-2 border-primary-100 dark:border-primary-800">
                  <div class="ml-3">
                    <h4 class="font-semibold text-gray-800 dark:text-gray-100"><?php echo htmlspecialchars($post['username']); ?></h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                      <?php echo date("M j, Y · g:i a", strtotime($post['created_at'])); ?>
                    </p>
                  </div>
                </div>
                
                <!-- Post Content -->
                <?php if (!empty($post['content'])): ?>
                  <div class="px-4 pb-3">
                    <p class="text-gray-700 dark:text-gray-300"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                  </div>
                <?php endif; ?>
                
                <!-- Post Media -->
                <?php if (!empty($post['image_url'])): ?>
                  <div class="w-full">
                    <?php if (preg_match('/\.mp4$/i', $post['image_url'])): ?>
                      <video controls class="w-full h-64 object-cover">
                        <source src="<?php echo htmlspecialchars($post['image_url']); ?>" type="video/mp4">
                        Your browser does not support the video tag.
                      </video>
                    <?php else: ?>
                      <img class="w-full h-64 object-cover" 
                           src="<?php echo htmlspecialchars($post['image_url']); ?>" 
                           alt="Post Media" />
                    <?php endif; ?>
                  </div>
                <?php endif; ?>
                
                <!-- Post Actions -->
                <div class="px-4 py-3 flex justify-between border-t border-gray-100 dark:border-gray-700">
                  <button class="flex items-center text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition">
                    <i class="far fa-heart mr-1"></i> Like
                  </button>
                  <button class="flex items-center text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition">
                    <i class="far fa-comment mr-1"></i> Comment
                  </button>
                </div>
              </div>
            <?php endwhile; ?>
          </div>
        <?php endif; ?>
      </section>
    </div>
  </main>

  <?php include "./includes/footer.php"; ?>

  <!-- Theme Toggle Button -->
  <button id="theme-toggle" class="fixed bottom-6 right-6 bg-gray-200 dark:bg-gray-700 p-3 rounded-full shadow-lg transition-all duration-300 hover:scale-110">
    <i id="theme-icon" class="fas fa-moon dark:fa-sun text-gray-800 dark:text-yellow-300 text-lg"></i>
  </button>

  <script>
    // Theme toggle functionality
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    
    themeToggle.addEventListener('click', function() {
      // Toggle the dark class on the html element
      document.documentElement.classList.toggle('dark');
      
      // Update icon and save preference
      if (document.documentElement.classList.contains('dark')) {
        localStorage.setItem('color-theme', 'dark');
        themeIcon.classList.replace('fa-moon', 'fa-sun');
      } else {
        localStorage.setItem('color-theme', 'light');
        themeIcon.classList.replace('fa-sun', 'fa-moon');
      }
    });
    
    // Initialize icon based on current theme
    if (document.documentElement.classList.contains('dark')) {
      themeIcon.classList.replace('fa-moon', 'fa-sun');
    }
    
    // Professional Sidebar Toggle
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebarClose = document.getElementById('sidebar-close');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const hamburgerMenu = document.querySelector('.hamburger-menu');
    
    function toggleSidebar() {
      const isOpen = sidebar.classList.toggle('open');
      overlay.classList.toggle('open');
      hamburgerMenu.classList.toggle('open');
      document.body.style.overflow = isOpen ? 'hidden' : '';
    }
    
    sidebarToggle.addEventListener('click', toggleSidebar);
    sidebarClose.addEventListener('click', toggleSidebar);
    overlay.addEventListener('click', toggleSidebar);
    
    // Simple animation for posts
    document.addEventListener('DOMContentLoaded', function() {
      const posts = document.querySelectorAll('.post-card');
      posts.forEach((post, index) => {
        post.style.opacity = '0';
        post.style.transform = 'translateY(20px)';
        post.style.animation = `fadeInUp 0.5s ease forwards ${index * 0.1}s`;
      });
      
      // Add animation keyframes
      const style = document.createElement('style');
      style.textContent = `
        @keyframes fadeInUp {
          to {
            opacity: 1;
            transform: translateY(0);
          }
        }
      `;
      document.head.appendChild(style);
    });
  </script>
</body>

</html>