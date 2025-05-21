<?php
session_start();
require '../config.php'; // DB Connection

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_SESSION['user_id']);
    $content = trim($_POST['content']);
    $media_url = null;

    // File Upload
    if (!empty($_FILES['media']['name'])) {
        $allowed_types = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'video/mp4'  => 'mp4'
        ];

        $file_type = mime_content_type($_FILES['media']['tmp_name']);
        $file_tmp = $_FILES['media']['tmp_name'];
        $file_ext = $allowed_types[$file_type] ?? null;

        if ($file_ext) {
            $file_name = time() . '_' . bin2hex(random_bytes(5)) . '.' . $file_ext;
            $upload_path = 'uploads/' . $file_name;

            if (!is_dir('uploads')) {
                mkdir('uploads', 0755, true);
            }

            if (move_uploaded_file($file_tmp, $upload_path)) {
                $media_url = $upload_path;
            } else {
                $message = 'Error uploading the file. Please try again.';
            }
        } else {
            $message = 'Invalid file type. Only JPG, PNG, GIF, and MP4 files are allowed.';
        }
    }

    // Insert post if content or media is provided
    if ($content || $media_url) {
        $stmt = $conn->prepare("INSERT INTO posts (user_id, content, image_url) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $content, $media_url);
        if ($stmt->execute()) {
            $stmt->close();
            echo "<script>alert('post uploaded successfully')</script>";
            echo "<script>window.open('../index.php',_self)</script>";
            exit;
        } else {
            $message = "Database error: could not save post.";
        }
    } elseif (empty($message)) {
        $message = "Please enter content or upload a media file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Create Post</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex justify-center items-center min-h-screen bg-gray-100 text-gray-900 dark:bg-gray-900 dark:text-white p-6">

    <div class="w-full md:w-[40%] bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <h2 class="text-2xl font-bold mb-4 text-center">Create a Post</h2>

        <?php if ($message): ?>
            <p class="text-red-500 text-center mb-4"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <form action="create_post.php" method="POST" enctype="multipart/form-data">
            <textarea name="content" placeholder="What's on your mind?" rows="4"
                class="w-full p-3 mb-4 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>

            <input type="file" name="media" accept="image/*,video/*"
                class="mb-4 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
                   file:rounded-full file:border-0 file:text-sm file:font-semibold
                   file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />

            <div id="preview" class="mb-4 hidden">
                <p class="font-semibold mb-1 text-gray-600 dark:text-gray-300">Preview:</p>
                <img id="previewImage" class="w-full rounded-lg hidden" alt="Image Preview" />
                <video id="previewVideo" class="w-full rounded-lg hidden" controls></video>
            </div>

            <button type="submit"
                class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-all">
                Post
            </button>
            <p class="flex justify-center items-center py-4">Go Back to <a href="../index.php" class="ml-2 underline"> Homepage</a></p>
        </form>
    </div>

    <script>
        const mediaInput = document.querySelector('input[name="media"]');
        const previewSection = document.getElementById('preview');
        const previewImage = document.getElementById('previewImage');
        const previewVideo = document.getElementById('previewVideo');

        mediaInput.addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;

            const fileType = file.type;

            if (fileType.startsWith("image/")) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewImage.classList.remove('hidden');
                    previewVideo.classList.add('hidden');
                    previewSection.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else if (fileType.startsWith("video/")) {
                const url = URL.createObjectURL(file);
                previewVideo.src = url;
                previewVideo.classList.remove('hidden');
                previewImage.classList.add('hidden');
                previewSection.classList.remove('hidden');
            } else {
                previewSection.classList.add('hidden');
            }
        });
    </script>
</body>

</html>