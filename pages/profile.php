<?php
// Database connection 
include '../config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get logged-in user's data
$user_id = $_SESSION['user_id'];
$query = "SELECT id, username, email, avatar, bio, created_at FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    die("No user found in database");
}

// Handle form submissions
$errors = [];
$success = false;

// Update profile info (except password)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $new_username = trim($_POST['username']);
    $new_email = trim($_POST['email']);
    $new_bio = trim($_POST['bio']);

    // Validate inputs
    if (empty($new_username)) {
        $errors[] = "Username is required";
    }
    if (empty($new_email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    if (empty($errors)) {
        $update_query = "UPDATE users SET username = ?, email = ?, bio = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("sssi", $new_username, $new_email, $new_bio, $user_id);

        if ($update_stmt->execute()) {
            $success = "Profile updated successfully!";
            // Refresh user data
            $user['username'] = $new_username;
            $user['email'] = $new_email;
            $user['bio'] = $new_bio;
        } else {
            $errors[] = "Error updating profile: " . $conn->error;
        }
    }
}

// Handle avatar upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_avatar']) && isset($_FILES['avatar'])) {
    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($_FILES["avatar"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image
    $check = getimagesize($_FILES["avatar"]["tmp_name"]);
    if ($check === false) {
        $errors[] = "File is not an image.";
    }

    // Check file size (max 2MB)
    if ($_FILES["avatar"]["size"] > 2000000) {
        $errors[] = "Sorry, your file is too large (max 2MB).";
    }

    // Allow certain file formats
    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        $errors[] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    }

    if (empty($errors)) {
        // Generate unique filename
        $new_filename = uniqid() . '.' . $imageFileType;
        $target_path = $target_dir . $new_filename;

        if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_path)) {
            // Update database
            $update_avatar_query = "UPDATE users SET avatar = ? WHERE id = ?";
            $update_avatar_stmt = $conn->prepare($update_avatar_query);
            $update_avatar_stmt->bind_param("si", $target_path, $user_id);

            if ($update_avatar_stmt->execute()) {
                $success = "Avatar updated successfully!";
                $user['avatar'] = $target_path;
            } else {
                $errors[] = "Error updating avatar: " . $conn->error;
            }
        } else {
            $errors[] = "Sorry, there was an error uploading your file.";
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Verify current password
    $password_query = "SELECT password FROM users WHERE id = ?";
    $password_stmt = $conn->prepare($password_query);
    $password_stmt->bind_param("i", $user_id);
    $password_stmt->execute();
    $password_result = $password_stmt->get_result();
    $db_password = $password_result->fetch_assoc()['password'];

    if (!password_verify($current_password, $db_password)) {
        $errors[] = "Current password is incorrect";
    } elseif ($new_password !== $confirm_password) {
        $errors[] = "New passwords don't match";
    } elseif (strlen($new_password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }

    if (empty($errors)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_password_query = "UPDATE users SET password = ? WHERE id = ?";
        $update_password_stmt = $conn->prepare($update_password_query);
        $update_password_stmt->bind_param("si", $hashed_password, $user_id);

        if ($update_password_stmt->execute()) {
            $success = "Password changed successfully!";
        } else {
            $errors[] = "Error changing password: " . $conn->error;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en" class="<?php echo isset($_COOKIE['darkMode']) ? 'dark' : ''; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['username']); ?>'s Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/styles.css">
    <script>
        // Check for dark mode preference
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.classList.add('dark');
            document.cookie = "darkMode=true; path=/; max-age=31536000";
        }

        // Listen for changes in color scheme
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
            if (event.matches) {
                document.documentElement.classList.add('dark');
                document.cookie = "darkMode=true; path=/; max-age=31536000";
            } else {
                document.documentElement.classList.remove('dark');
                document.cookie = "darkMode=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT";
            }
        });

        function toggleEdit(section) {
            const viewMode = document.getElementById(`${section}-view`);
            const editMode = document.getElementById(`${section}-edit`);

            if (viewMode.style.display === 'none') {
                viewMode.style.display = 'block';
                editMode.style.display = 'none';
            } else {
                viewMode.style.display = 'none';
                editMode.style.display = 'block';
            }
        }
    </script>
</head>

<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 transition-colors duration-200 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div id="success-message" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <?php echo htmlspecialchars($success); ?>
            </div>
            <script>
                setTimeout(function() {
                    var msg = document.getElementById('success-message');
                    if (msg) msg.style.display = 'none';
                }, 2000);
            </script>
        <?php endif; ?>

        <div class="max-w-3xl mx-auto">
            <!-- Profile Header -->
            <div class="flex flex-col items-center md:flex-row md:items-start gap-6 mb-8 p-6 rounded-lg bg-white dark:bg-gray-800 shadow">
                <!-- Avatar -->
                <div class="relative">
                    <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-indigo-500 dark:border-indigo-400">
                        <?php if (!empty($user['avatar'])): ?>
                            <img src="<?php echo htmlspecialchars($user['avatar']); ?>" alt="Profile picture" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                <span class="text-4xl text-indigo-600 dark:text-indigo-300 font-bold">
                                    <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <button onclick="toggleEdit('avatar')" class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold py-1 px-2 rounded">
                        Edit
                    </button>
                </div>

                <!-- Avatar Edit Form -->
                <div id="avatar-edit" style="display: none;" class="w-full md:w-auto">
                    <form method="post" enctype="multipart/form-data" class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <div class="mb-4">
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="avatar">
                                Upload new avatar
                            </label>
                            <input type="file" name="avatar" id="avatar" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" name="update_avatar" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Save
                            </button>
                            <button type="button" onclick="toggleEdit('avatar')" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>

                <!-- User Info -->
                <div class="flex-1">
                    <div id="profile-view">
                        <h1 class="text-3xl font-bold mb-2"><?php echo htmlspecialchars($user['username']); ?></h1>
                        <p class="text-indigo-600 dark:text-indigo-400 mb-4"><?php echo htmlspecialchars($user['email']); ?></p>

                        <?php if (!empty($user['bio'])): ?>
                            <p class="text-gray-600 dark:text-gray-300 mb-4"><?php echo nl2br(htmlspecialchars($user['bio'])); ?></p>
                        <?php else: ?>
                            <p class="text-gray-400 italic mb-4">No bio yet</p>
                        <?php endif; ?>

                        <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                            Member since <?php echo date('F Y', strtotime($user['created_at'])); ?>
                        </div>
                        <button onclick="toggleEdit('profile')" class="mt-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-1 px-3 rounded text-sm">
                            Edit Profile
                        </button>
                    </div>

                    <!-- Profile Edit Form -->
                    <div id="profile-edit" style="display: none;">
                        <form method="post" class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <div class="mb-4">
                                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="username">
                                    Username
                                </label>
                                <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="email">
                                    Email
                                </label>
                                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="bio">
                                    Bio
                                </label>
                                <textarea name="bio" id="bio" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline h-24"><?php echo htmlspecialchars($user['bio']); ?></textarea>
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" name="update_profile" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                    Save
                                </button>
                                <button type="button" onclick="toggleEdit('profile')" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <a href="../index.php" class="text-purple-600 underline">Go to Homepage</a>
            </div>

            <!-- Password Change Section -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-8">
                <h2 class="text-xl font-semibold mb-4">Change Password</h2>
                <form method="post">
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="current_password">
                            Current Password
                        </label>
                        <input type="password" name="current_password" id="current_password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="new_password">
                            New Password
                        </label>
                        <input type="password" name="new_password" id="new_password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="confirm_password">
                            Confirm New Password
                        </label>
                        <input type="password" name="confirm_password" id="confirm_password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    <button type="submit" name="change_password" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Change Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>