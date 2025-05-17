<?php
session_start();
require '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $post_id = intval($_POST['post_id']);
    $comment = trim($_POST['comment']);

    if ($comment !== '') {
        $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $post_id, $user_id, $comment);

        if ($stmt->execute()) {
            // Fetch user info to return in response
            $user_query = $conn->prepare("SELECT username, avatar FROM users WHERE id = ?");
            $user_query->bind_param("i", $user_id);
            $user_query->execute();
            $user_result = $user_query->get_result()->fetch_assoc();

            echo json_encode([
                'success' => true,
                'html' => '
                    <div class="mb-2 flex items-start space-x-3">
                        <img src="' . htmlspecialchars($user_result['avatar']) . '" alt="Avatar" class="w-8 h-8 rounded-full">
                        <div>
                            <p class="font-semibold">' . htmlspecialchars($user_result['username']) . '</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300">' . nl2br(htmlspecialchars($comment)) . '</p>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Just now · <button class="hover:text-blue-500">Like</button> · <button class="hover:text-red-500">Dislike</button>
                            </div>
                        </div>
                    </div>'
            ]);
            exit;
        }
    }
}

echo json_encode(['success' => false, 'error' => 'Invalid request or not authenticated']);
exit;
