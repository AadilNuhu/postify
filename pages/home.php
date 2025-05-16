<?php
session_start();
require 'db_connection.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch user info
$user_id = $_SESSION['user_id'];
$user_sql = "SELECT * FROM users WHERE id = $user_id";
$user_result = mysqli_query($conn, $user_sql);
$user = mysqli_fetch_assoc($user_result);

// Fetch posts
$post_sql = "SELECT posts.*, users.username, users.avatar FROM posts 
             JOIN users ON posts.user_id = users.id 
             ORDER BY posts.created_at DESC";
$posts_result = mysqli_query($conn, $post_sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Home | AkNet</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 text-gray-900 dark:bg-gray-900 dark:text-white">
    <!-- Header Navbar -->
    <header class="flex justify-between items-center px-6 py-4 bg-white dark:bg-gray-800 shadow">
        <div class="flex items-center gap-4">
            <img src="logo.png" alt="AkNet" class="h-8">
            <input type="text" placeholder="Search..." class="rounded-full px-4 py-2 w-72 bg-gray-100 focus:outline-none">
        </div>
        <nav class="flex items-center gap-6 text-sm">
            <a href="#" class="hover:text-blue-600">Home</a>
            <a href="#" class="hover:text-blue-600">Messages</a>
            <a href="#" class="hover:text-blue-600">Notifications</a>
            <div class="relative">
                <img src="<?= $user['avatar'] ?>" class="h-8 w-8 rounded-full" alt="Profile">
            </div>
        </nav>
    </header>

    <!-- Main Grid Layout -->
    <main class="grid grid-cols-12 gap-4 px-6 py-4">
        <!-- Left Sidebar -->
        <aside class="col-span-3 bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-center">
                <img src="<?= $user['avatar'] ?>" class="h-16 w-16 mx-auto rounded-full mb-2" alt="Profile">
                <h2 class="font-semibold"><?= $user['username'] ?></h2>
                <p class="text-sm text-gray-500">Takoradi, Ghana</p>
            </div>
            <hr class="my-4">
            <ul class="text-sm space-y-2">
                <li><a href="#" class="hover:text-blue-500">View Analytics</a></li>
                <li><a href="#" class="hover:text-blue-500">Saved Items</a></li>
            </ul>
        </aside>

        <!-- Feed Section -->
        <section class="col-span-6 space-y-4">
            <!-- Post Creator -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                <form action="create_post.php" method="POST" enctype="multipart/form-data">
                    <textarea name="content" class="w-full p-2 rounded border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white" rows="2" placeholder="What's on your mind?"></textarea>
                    <div class="flex justify-between mt-2">
                        <input type="file" name="image">
                        <button class="bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700" type="submit">Post</button>
                    </div>
                </form>
            </div>

            <!-- Posts Feed -->
            <?php while ($post = mysqli_fetch_assoc($posts_result)): ?>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                    <div class="flex items-center mb-2">
                        <img src="<?= $post['avatar'] ?>" class="w-10 h-10 rounded-full mr-2" alt="Avatar">
                        <div>
                            <p class="font-semibold"><?= htmlspecialchars($post['username']) ?></p>
                            <p class="text-xs text-gray-500"><?= date("F j, Y, g:i a", strtotime($post['created_at'])) ?></p>
                        </div>
                    </div>
                    <p class="mb-2"><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                    <?php if ($post['image_url']): ?>
                        <img src="<?= $post['image_url'] ?>" class="w-full rounded-lg mb-2">
                    <?php endif; ?>
                    <div class="flex justify-around text-sm text-gray-600 dark:text-gray-300">
                        <button class="hover:text-blue-600">Like</button>
                        <button class="hover:text-blue-600">Comment</button>
                        <button class="hover:text-blue-600">Repost</button>
                        <button class="hover:text-blue-600">Share</button>
                    </div>
                </div>
            <?php endwhile; ?>
        </section>

        <!-- Right Sidebar -->
        <aside class="col-span-3 space-y-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                <h3 class="font-semibold text-lg mb-2">Add to your feed</h3>
                <div class="space-y-2 text-sm">
                    <div>
                        <p>Dr. Daniel McKorley</p>
                        <button class="text-blue-600">+ Follow</button>
                    </div>
                    <div>
                        <p>Peter Bawuah, MA</p>
                        <button class="text-blue-600">+ Follow</button>
                    </div>
                </div>
            </div>
        </aside>
    </main>
</body>

</html>