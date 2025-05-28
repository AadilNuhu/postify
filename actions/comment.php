<?php
session_start();
require '../config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $post_id = intval($_POST['post_id']);
    $comment = trim($_POST['comment']);

    if (empty($comment)) {
        echo json_encode(['status' => 'error', 'message' => 'Comment cannot be empty.']);
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $post_id, $user_id, $comment);

    if ($stmt->execute()) {
        // Fetch the latest comments
        $fetch = $conn->prepare("
            SELECT c.comment, c.created_at, u.username, u.avatar 
            FROM comments c 
            JOIN users u ON c.user_id = u.id 
            WHERE c.post_id = ? 
            ORDER BY c.created_at DESC
        ");
        $fetch->bind_param("i", $post_id);
        $fetch->execute();
        $result = $fetch->get_result();

        $commentsHtml = '';
        while ($row = $result->fetch_assoc()) {
            $commentsHtml .= '
                <div class="flex items-start gap-2 py-2 border-b border-gray-200 dark:border-gray-700">
                    <img src="' . htmlspecialchars($row['avatar'] ?? 'default.jpg') . '" class="w-8 h-8 rounded-full" alt="Avatar">
                    <div>
                        <p class="text-sm font-semibold">' . htmlspecialchars($row['username']) . '</p>
                        <p class="text-sm">' . nl2br(htmlspecialchars($row['comment'])) . '</p>
                        <span class="text-xs text-gray-400">' . date("F j, Y g:i A", strtotime($row['created_at'])) . '</span>
                    </div>
                </div>';
        }

        echo json_encode(['status' => 'success', 'commentsHtml' => $commentsHtml]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'DB insert failed.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
