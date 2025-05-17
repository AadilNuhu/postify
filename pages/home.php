<?php
// Initialize session and output buffering
session_start();
ob_start();

// Configuration & DB connection
require_once './config.php';

// Redirect unauthenticated users
if (!isset($_SESSION['user_id'])) {
    header("Location: ./pages/login.php");
    exit();
}

// Fetch posts along with associated user data using prepared statements
$stmt = $conn->prepare("
    SELECT posts.*, users.username, users.avatar
    FROM posts
    INNER JOIN users ON posts.user_id = users.id
    ORDER BY posts.created_at DESC
");
$stmt->execute();
$posts_result = $stmt->get_result();

if (!$posts_result) {
    die("Query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Home | Postify</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<body class="bg-gray-100 text-gray-900 dark:bg-gray-900 dark:text-white min-h-screen">

    <!-- Include navigation bar -->
    <?php include './includes/navbar.php'; ?>

    <main class="container mx-auto px-4 py-6 grid grid-cols-1 md:grid-cols-12 gap-6">
        <!-- Feed section -->
        <section class="md:col-span-8 space-y-6">
            <h1 class="text-2xl font-bold border-b pb-2">Latest Posts</h1>

            <?php while ($post = $posts_result->fetch_assoc()): ?>
                <article class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
                    <!-- Post Header -->
                    <header class="flex items-center space-x-3 mb-3">
                        <img src="<?= htmlspecialchars($post['avatar']) ?>" alt="Avatar" class="w-10 h-10 rounded-full object-cover">
                        <div>
                            <p class="font-semibold">@<?= htmlspecialchars($post['username']) ?></p>
                            <time class="text-sm text-gray-500 dark:text-gray-400"><?= date("F j, Y, g:i a", strtotime($post['created_at'])) ?></time>
                        </div>
                    </header>

                    <!-- Post Content -->
                    <?php if (!empty($post['content'])): ?>
                        <p class="mb-3 text-gray-800 dark:text-gray-200 whitespace-pre-wrap"><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                    <?php endif; ?>

                    <!-- Media Content -->
                    <?php if (!empty($post['image_url'])): ?>
                        <?php if (preg_match('/\.mp4$/i', $post['image_url'])): ?>
                            <video controls class="w-full rounded-lg mb-3">
                                <source src="<?= htmlspecialchars($post['image_url']) ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        <?php else: ?>
                            <img src="<?= htmlspecialchars($post['image_url']) ?>" alt="Post Media" class="w-full rounded-lg mb-3" />
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Post Actions -->
                    <div class="flex space-x-6 text-sm text-gray-600 dark:text-gray-300 mt-4">
                        <button class="hover:text-blue-500 flex items-center">
                            <i class="fas fa-thumbs-up mr-1"></i> Like
                        </button>
                        <button onclick="toggleComments('comment-<?= $post['id'] ?>')" class="hover:text-blue-500 flex items-center">
                            <i class="fas fa-comment mr-1"></i> Comment
                        </button>
                        <button class="hover:text-blue-500 flex items-center">
                            <i class="fas fa-share mr-1"></i> Share
                        </button>
                    </div>

                    <!-- Comments Section -->
                    <section id="comment-<?= $post['id'] ?>" class="hidden mt-4">
                        <!-- Comment Form -->
                        <form action="comment.php" method="POST">
                            <input type="hidden" name="post_id" value="<?= $post['id'] ?>" />
                            <textarea name="comment" placeholder="Write a comment..." required
                                class="w-full p-2 border rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white resize-none"></textarea>
                            <button type="submit"
                                class="bg-blue-600 text-white px-4 py-1 mt-2 rounded hover:bg-blue-700 text-sm">Post</button>
                        </form>

                        <!-- Show/Hide Comment List -->
                        <div class="mt-3">
                            <button onclick="toggleCommentBox(<?= $post['id'] ?>)" class="text-blue-600 text-sm">Show Comments</button>
                            <div id="comment-box-<?= $post['id'] ?>" class="hidden mt-2 p-3 bg-gray-50 dark:bg-gray-800 rounded shadow">
                                <div id="comment-list-<?= $post['id'] ?>" class="text-sm space-y-2 text-gray-700 dark:text-gray-200">
                                    <!-- AJAX-loaded comments will appear here -->
                                </div>
                                <form onsubmit="submitComment(event, <?= $post['id'] ?>)" class="mt-2">
                                    <textarea name="comment" class="w-full p-2 rounded text-sm" placeholder="Write your comment..." required></textarea>
                                    <button type="submit" class="mt-1 bg-blue-600 text-white px-3 py-1 rounded text-xs">Post Comment</button>
                                </form>
                            </div>
                        </div>
                    </section>
                </article>
            <?php endwhile; ?>
        </section>
    </main>

    <!-- JavaScript Section -->
    <script>
        function toggleComments(id) {
            const el = document.getElementById(id);
            el.classList.toggle('hidden');
        }

        function toggleCommentBox(postId) {
            const box = document.getElementById('comment-box-' + postId);
            box.classList.toggle('hidden');

            // Optionally, load comments via AJAX
            fetch(`load_comments.php?post_id=${postId}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('comment-list-' + postId).innerHTML = data;
                })
                .catch(error => console.error('Error fetching comments:', error));
        }

        function submitComment(event, postId) {
            event.preventDefault();

            const form = event.target;
            const formData = new FormData(form);

            fetch('submit_comment.php', {
                    method: 'POST',
                    body: formData
                }).then(res => res.text())
                .then(() => {
                    form.reset();
                    toggleCommentBox(postId); // Reload comments
                })
                .catch(err => console.error('Comment submit failed', err));
        }
    </script>

</body>

</html>
<?php ob_end_flush(); ?>