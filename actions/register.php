<?php
require '../config.php';

$username = $_POST['username'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$bio = $_POST['bio'] ?? null;
$avatarPath = null;

if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['avatar']['tmp_name'];
    $fileName = $_FILES['avatar']['name'];
    $fileSize = $_FILES['avatar']['size'];
    $fileType = $_FILES['avatar']['type'];
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    if (in_array(strtolower($fileExtension), $allowedExtensions)) {
        $newFileName = uniqid('avatar_', true) . '.' . $fileExtension;
        $uploadFileDir = 'uploads/';
        $destPath = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $avatarPath = $destPath;
        }
    }
}
 
$sql = "INSERT INTO users (username, email, password, avatar, bio)
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $username, $email, $password, $avatarPath, $bio);

if ($stmt->execute()) {
    echo "<script>alert('Account created successfull. Redirecting to login ... ')</script>";
    echo "<script>window.open('../pages/login.php')</script>";  
} else {
    echo "<script>alert('An Error Occured')</script>";
    echo "<script>window.open('../pages/register.php')</script>";
    echo "Error: " . $stmt->error;
}
?>
