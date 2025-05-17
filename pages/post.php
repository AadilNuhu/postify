<?php
session_start();
require '../config.php'; // Ensure this file connects to your database

// Fetch posts with user details
$sql = "SELECT posts.*, users.username, users.avatar FROM posts 
        JOIN users ON posts.user_id = users.id 
        ORDER BY posts.created_at DESC";
$result = mysqli_query($conn, $sql);

// Fetch trending posts (top 5 most recent as placeholder)
$trending_sql = "SELECT posts.*, users.username FROM posts 
                JOIN users ON posts.user_id = users.id 
                ORDER BY posts.created_at DESC LIMIT 5";
$trending_result = mysqli_query($conn, $trending_sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Posts Feed</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function toggleComments(id) {
            const el = document.getElementById('comments-' + id);
            el.classList.toggle('hidden');
        }
    </script>
</head>

<body class="bg-gray-100 text-gray-900 dark:bg-gray-900 dark:text-white p-4">
    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Main Posts Area -->
        <div class="lg:col-span-2">
            <h1 class="text-2xl font-bold mb-4">Latest Posts</h1>

            <?php while ($post = mysqli_fetch_assoc($result)): ?>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 mb-4">
                    <div class="flex items-center mb-2">
                        <img src="<?php echo $post['avatar'] ?? 'default.jpg'; ?>" alt="Avatar" class="w-10 h-10 rounded-full mr-3">
                        <div>
                            <p class="font-semibold"><?php echo htmlspecialchars($post['username']); ?></p>
                            <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo date("F j, Y, g:i a", strtotime($post['created_at'])); ?></p>
                        </div>
                    </div>
                    <p class="mb-3"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                    <?php if (!empty($post['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($post['image_url']); ?>" alt="Post Image" class="w-full rounded-lg mb-3">
                    <?php endif; ?>

                    <!-- Post Actions -->
                    <div class="flex justify-between mt-4 text-sm text-gray-500 dark:text-gray-300">
                        <button class="hover:text-blue-600">Like</button>
                        <button onclick="toggleComments(<?php echo $post['id']; ?>)" class="hover:text-blue-600">Comment</button>
                        <button class="hover:text-blue-600">Repost</button>
                        <button class="hover:text-blue-600">Share</button>
                    </div>

                    <!-- Comment Section -->
                    <div id="comments-<?php echo $post['id']; ?>" class="mt-4 hidden">
                        <form method="POST" action="post_comment.php" class="mb-3">
                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                            <textarea name="comment" rows="2" placeholder="Write a comment..." class="w-full p-2 rounded-md border dark:bg-gray-700"></textarea>
                            <button type="submit" class="mt-2 px-4 py-1 bg-blue-600 text-white rounded-md">Send</button>
                        </form>

                        <!-- Placeholder for comments -->
                        <div class="space-y-2">
                            <div class="bg-gray-100 dark:bg-gray-700 p-2 rounded-md">
                                <p><strong>Jane Doe</strong>: This is a sample comment.</p>
                                <div class="flex space-x-3 text-xs text-gray-500 mt-1">
                                    <button class="hover:text-blue-500">Like</button>
                                    <button class="hover:text-red-500">Dislike</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Sidebar -->
        <div>
            <h2 class="text-xl font-semibold mb-3">Trending</h2>
            <?php while ($trend = mysqli_fetch_assoc($trending_result)): ?>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-3 mb-2 shadow-sm">
                    <p class="text-sm font-medium"><?php echo htmlspecialchars($trend['username']); ?>:</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                        <?php echo substr(htmlspecialchars($trend['content']), 0, 60) . '...'; ?>
                    </p>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

<script>
document.querySelectorAll('form.comment-form').forEach(form => {
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const postId = this.dataset.postId;
        const textarea = this.querySelector('textarea');
        const commentText = textarea.value.trim();

        if (!commentText) return;

        fetch('post_comment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `post_id=${postId}&comment=${encodeURIComponent(commentText)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const container = document.querySelector(`#comments-${postId}`);
                container.insertAdjacentHTML('beforeend', data.html);
                textarea.value = '';
            } else {
                alert('Failed to post comment.');
            }
        });
    });
});
</script>


</body>

</html>