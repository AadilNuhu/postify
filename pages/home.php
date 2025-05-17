<?php
// Start session and output buffering at the VERY TOP
session_start();
ob_start();
require './config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ./pages/login.php");
    exit();
}

// Fetch posts with user data
$post_sql = "
    SELECT posts.*, users.username, users.avatar
    FROM posts
    JOIN users ON posts.user_id = users.id
    ORDER BY posts.created_at DESC
";
$posts_result = $conn->query($post_sql);

// Check for query errors
if (!$posts_result) {
    die("Database error: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Home | Postify</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900 dark:bg-gray-900 dark:text-white">
    <?php include './includes/navbar.php'; ?>
    
    <main class="grid grid-cols-1 md:grid-cols-12 gap-4 px-4 md:px-6 py-6">
        <!-- Feed -->
        <section class="md:col-span-6 space-y-4">
            <!-- Post Creator -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                <form action="create_post.php" method="POST" enctype="multipart/form-data">
                    <textarea name="content" rows="2" placeholder="What's on your mind?"
                        class="w-full p-2 rounded border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white"
                        required></textarea>
                    <div class="flex justify-between items-center mt-2">
                        <input type="file" name="image" accept="image/*" class="text-sm" />
                        <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700">Post</button>
                    </div>
                </form>
            </div>

            <!-- Posts Feed -->
            <?php if ($posts_result->num_rows > 0): ?>
                <?php while ($post = $posts_result->fetch_assoc()): ?>
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                        <div class="flex items-center mb-2">
                            <img src="<?= htmlspecialchars($post['avatar'] ?? 'default_avatar.png') ?>" 
                                 class="w-10 h-10 rounded-full mr-2" 
                                 alt="<?= htmlspecialchars($post['username']) ?>'s avatar" />
                            <div>
                                <p class="font-semibold text-base"><?= htmlspecialchars($post['username']) ?></p>
                                <p class="text-xs text-gray-500"><?= date("F j, Y, g:i a", strtotime($post['created_at'])) ?></p>
                            </div>
                        </div>
                        <p class="mb-2 text-sm leading-relaxed"><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                        <?php if (!empty($post['image_url'])): ?>
                            <img src="<?= htmlspecialchars($post['image_url']) ?>" 
                                 class="w-full rounded-lg mb-2" 
                                 alt="Post by <?= htmlspecialchars($post['username']) ?>" />
                        <?php endif; ?>
                        <div class="flex justify-around text-sm text-gray-600 dark:text-gray-300 space-x-4">
                            <button type="button" class="hover:text-blue-600">Like</button>
                            <button type="button" class="hover:text-blue-600">Comment</button>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center text-gray-500">No posts found. Be the first to post!</p>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
<?php
ob_end_flush();
?>