<?php
session_start();
require './config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ./pages/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$content = trim($_POST['content'] ?? '');

$image_url = null;

if (!empty($_FILES['image']['name'])) {
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);

    $image_file = $target_dir . basename($_FILES["image"]["name"]);
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $image_file)) {
        $image_url = $image_file;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($content === '' && !$image_url) {
        echo "Post content or image required.";
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO posts (user_id, content, image_url, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $user_id, $content, $image_url);
    $stmt->execute();

    header('Location: ../index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create a Post</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.1/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 py-8">
    <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-semibold mb-4">Create a Post</h2>
        <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label for="content" class="block text-gray-700 font-medium mb-2">Content</label>
                <textarea name="content" id="content" rows="4" class="w-full p-2 border border-gray-300 rounded" placeholder="What's on your mind?"></textarea>
            </div>
            <div>
                <label for="image" class="block text-gray-700 font-medium mb-2">Upload Image</label>
                <input type="file" name="image" id="image" class="block w-full text-sm text-gray-600">
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                Post
            </button>
        </form>
    </div>
</body>
</html>
