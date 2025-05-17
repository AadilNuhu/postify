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
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100 text-gray-900 dark:bg-gray-900 dark:text-white">
    <?php include './includes/navbar.php'; ?>

    <main class="grid grid-cols-1 md:grid-cols-12 gap-4 px-4 md:px-6 py-6">
        <!-- Feed -->
        <section class="md:col-span-6 space-y-4">
            <h1 class="text-2xl font-bold mb-4">Latest Posts</h1>

            <?php while ($post = $posts_result->fetch_assoc()): ?>
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4">
                    <div class="flex items-center mb-2">
                        <img src="<?= htmlspecialchars($post['avatar']) ?>" alt="avatar" class="w-10 h-10 rounded-full mr-3">
                        <div>
                            <p class="font-semibold">@<?= htmlspecialchars($post['username']) ?></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400"><?= date("F j, Y, g:i a", strtotime($post['created_at'])) ?></p>
                        </div>
                    </div>

                    <?php if (!empty($post['content'])): ?>
                        <p class="mb-2 text-gray-800 dark:text-gray-200"> <?= nl2br(htmlspecialchars($post['content'])) ?> </p>
                    <?php endif; ?>

                    <?php if (!empty($post['image_url'])): ?>
                        <?php if (preg_match('/\.(mp4)$/i', $post['image_url'])): ?>
                            <video controls class="w-full rounded-lg mb-2">
                                <source src="<?= htmlspecialchars($post['image_url']) ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        <?php else: ?>
                            <img src="<?= htmlspecialchars($post['image_url']) ?>" class="w-full rounded-lg mb-2" />
                        <?php endif; ?>
                    <?php endif; ?>

                    <div class="flex items-center space-x-6 mt-4">
                        <button class="flex items-center text-gray-600 dark:text-gray-300 hover:text-blue-500">
                            <i class="fas fa-thumbs-up mr-1"></i> Like
                        </button>
                        <button onclick="toggleComments('comment-<?= $post['id'] ?>')" class="flex items-center text-gray-600 dark:text-gray-300 hover:text-blue-500">
                            <i class="fas fa-comment mr-1"></i> Comment
                        </button>
                        <button class="flex items-center text-gray-600 dark:text-gray-300 hover:text-blue-500">
                            <i class="fas fa-share mr-1"></i> Share
                        </button>
                    </div>

                    <!-- Comments Section (toggle dropdown) -->
                    <div id="comment-<?= $post['id'] ?>" class="hidden mt-4">
                        <form method="POST" action="comment.php" class="mb-2">
                            <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                            <textarea name="comment" rows="2" placeholder="Write a comment..." class="w-full p-2 border rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                            <button type="submit" class="bg-blue-600 text-white mt-2 py-1 px-3 rounded hover:bg-blue-700">Send</button>
                        </form>

                        <!-- Future: Display list of comments here -->
                        <div class="text-sm text-gray-500 dark:text-gray-400 italic">No comments yet.</div>
                    </div>
                </div>
            <?php endwhile; ?>

        </section>
    </main>

    <script>
        function toggleComments(id) {
            const el = document.getElementById(id);
            el.classList.toggle('hidden');
        }
    </script>
</body>
</html>
<?php ob_end_flush(); ?>
