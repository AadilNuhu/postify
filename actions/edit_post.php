<?php
session_start();
require '../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ./pages/login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$postId = $_GET['id'] ?? null;

if (!$postId) {
    die("Invalid post ID.");
}

// Fetch post and ensure ownership
$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $postId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Post not found or permission denied.");
}

$post = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Post | Postify</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6 text-gray-900">
    <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-md p-6 space-y-6">
        <h2 class="text-2xl font-bold mb-4">Edit Post</h2>
        
        <form id="editPostForm" enctype="multipart/form-data">
            <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
            <textarea name="content" rows="4" class="w-full border p-2 rounded" required><?= htmlspecialchars($post['content']) ?></textarea>
            
            <?php if ($post['image_url']): ?>
                <div class="mt-4">
                    <label class="block mb-2 text-sm font-medium">Current Media:</label>
                    <?php if (preg_match('/\.mp4$/i', $post['image_url'])): ?>
                        <video controls class="w-full rounded">
                            <source src="<?= $post['image_url'] ?>" type="video/mp4">
                        </video>
                    <?php else: ?>
                        <img src="<?= $post['image_url'] ?>" class="w-full rounded" />
                    <?php endif; ?>
                    <label class="flex items-center mt-2 space-x-2 text-sm">
                        <input type="checkbox" name="remove_media" value="1">
                        <span>Remove current media</span>
                    </label>
                </div>
            <?php endif; ?>

            <div class="mt-4">
                <label for="new_media" class="block text-sm font-medium">Upload New Media (optional)</label>
                <input type="file" name="new_media" accept="image/*,video/*" class="w-full border p-2 rounded" />
            </div>

            <button type="submit" class="bg-blue-600 text-white mt-4 px-4 py-2 rounded hover:bg-blue-700">Update Post</button>
            <div id="updateResult" class="mt-4 text-sm"></div>
        </form>
    </div>

    <script>
        document.getElementById('editPostForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('update_post.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.text())
            .then(data => {
                document.getElementById('updateResult').innerHTML = data;
            })
            .catch(err => {
                document.getElementById('updateResult').innerHTML = 'Something went wrong. Please try again.';
            });
        });
    </script>
</body>
</html>
