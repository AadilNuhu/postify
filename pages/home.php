<?php
// Start session and output buffering at the VERY TOP
// session_start();
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
            <h1 class="text-2xl font-bold mb-4">Latest Posts</h1>
        </section>

    </main>
</body>
</html>
<?php
ob_end_flush();
?>