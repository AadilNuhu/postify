<?php
session_start();
require '../config.php';

if (!isset($_SESSION['user_id'])) {
    exit("Unauthorized.");
}

$userId = $_SESSION['user_id'];
$postId = $_POST['post_id'] ?? null;
$content = trim($_POST['content'] ?? '');
$removeMedia = isset($_POST['remove_media']) ? true : false;

if (!$postId || !$content) {
    exit("Missing fields.");
}

// Validate ownership
$stmt = $conn->prepare("SELECT image_url FROM posts WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $postId, $userId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    exit("Permission denied or post not found.");
}

$post = $result->fetch_assoc();
$oldMedia = $post['image_url'];

// Handle new media upload
$newMediaPath = null;
if (!empty($_FILES['new_media']['tmp_name'])) {
    $media = $_FILES['new_media'];
    $ext = pathinfo($media['name'], PATHINFO_EXTENSION);
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'mp4'];

    if (!in_array(strtolower($ext), $allowed)) {
        exit("Unsupported file type.");
    }

    $mediaPath = 'uploads/' . uniqid() . '.' . $ext;
    if (move_uploaded_file($media['tmp_name'], $mediaPath)) {
        $newMediaPath = $mediaPath;
    }
}

// Determine final media path
$finalMedia = $removeMedia ? null : ($newMediaPath ?? $oldMedia);

// Update post
$stmt = $conn->prepare("UPDATE posts SET content = ?, image_url = ? WHERE id = ? AND user_id = ?");
$stmt->bind_param("ssii", $content, $finalMedia, $postId, $userId);
$stmt->execute();

echo "Post updated successfully.";

// Optional: Save to audit log
$log = $conn->prepare("INSERT INTO post_audit_log (post_id, user_id, action, changed_at) VALUES (?, ?, 'EDITED', NOW())");
$log->bind_param("ii", $postId, $userId);
$log->execute();
