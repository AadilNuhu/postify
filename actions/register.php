<?php
session_start();
require '../config.php';
error_log("Register attempt: username=$username, email=$email, password=" . ($password ? 'set' : 'empty'));

$username = trim($_POST['username'] ?? '');
$email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
$password = $_POST['password'] ?? '';
$bio = trim($_POST['bio'] ?? '');
$avatarPath = null;

if (!$username || !$email || !$password) {
    $_SESSION['register_error'] = "All fields are required.";
    header("Location: ../pages/register.php");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['register_error'] = "Invalid email format.";
    header("Location: ../pages/register.php");
    exit();
}

if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    $fileTmp = $_FILES['avatar']['tmp_name'];
    $fileName = basename($_FILES['avatar']['name']);
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($fileExt, $allowed)) {
        $_SESSION['register_error'] = "Only JPG, PNG, or GIF files allowed.";
        header("Location: ../pages/register.php");
        exit();
    }

    $newName = uniqid("avatar_", true) . "." . $fileExt;
    $uploadDir = "../uploads/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    $uploadPath = $uploadDir . $newName;

    if (move_uploaded_file($fileTmp, $uploadPath)) {
        $avatarPath = "uploads/" . $newName;
    } else {
        $_SESSION['register_error'] = "Failed to upload avatar.";
        header("Location: ../pages/register.php");
        exit();
    }
}

$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

$stmt = $conn->prepare("INSERT INTO users (username, email, password, avatar, bio) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $username, $email, $hashedPassword, $avatarPath, $bio);

if ($stmt->execute()) {
    $_SESSION['signup_success'] = "Account created. Please login.";
    header("Location: ../pages/login.php");
    exit();
} else {
    $_SESSION['register_error'] = "Registration failed. Email might already be in use.";
    header("Location: ../pages/register.php");
    exit();
}
?>
