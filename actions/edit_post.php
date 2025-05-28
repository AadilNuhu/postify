<?php
session_start();
include '../config.php';

// Simulate user login (replace this in production)
$_SESSION['user_id'] = $_SESSION['user_id'] ?? 1;

$user_id = $_SESSION['user_id'];

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_post'])) {
    $post_id = $_POST['post_id'];
    $content = trim($_POST['content']);

    // Get old media
    $stmt = $conn->prepare("SELECT image_url FROM posts WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();
    $mediaPath = $post['image_url'];

    // Upload new media if any
    if (!empty($_FILES['media']['name'])) {
        $uploadDir = 'uploads/';
        $fileName = basename($_FILES['media']['name']);
        $targetPath = $uploadDir . time() . "_" . $fileName;

        if (move_uploaded_file($_FILES['media']['tmp_name'], $targetPath)) {
            $mediaPath = $targetPath;
        }
    }

    // Update post
    $stmt = $conn->prepare("UPDATE posts SET content = ?, image_url = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssii", $content, $mediaPath, $post_id, $user_id);
    $stmt->execute();
}

// Fetch user's posts
$stmt = $conn->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$posts = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage My Posts</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="grid grid-cols-3 m:grid-cols-2 sm:grid-cols-1 bg-gray-800 p-6">
  <div class="max-w-3xl mx-auto">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-3xl font-bold text-purple-700 mb-6">Manage My Posts</h1>
        <a href="../index.php" class="text-purple-500 underline">Go To Homepage</a>
    </div>

    <?php while ($post = $posts->fetch_assoc()): ?>
      <div class="bg-gray-500 p-4 rounded shadow mb-6">
        <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
          <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
          <input type="hidden" name="update_post" value="1">

          <textarea name="content" class="w-full p-2 border rounded"><?= htmlspecialchars($post['content']) ?></textarea>

          <?php if ($post['image_url']): ?>
            <?php if (str_ends_with($post['image_url'], '.mp4')): ?>
              <video src="<?= $post['image_url'] ?>" controls class="w-full rounded"></video>
            <?php else: ?>
              <img src="<?= $post['image_url'] ?>" class="w-full rounded" />
            <?php endif; ?>
          <?php endif; ?>

          <input type="file" name="media" accept="image/*,video/*" class="w-full border p-2 rounded" />

          <button type="submit" class="bg-purple-600 text-white py-2 px-4 rounded hover:bg-purple-700">
            Update Post
          </button>
        </form>
      </div>
    <?php endwhile; ?>
  </div>
</body>
</html>
