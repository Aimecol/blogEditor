<?php
require_once 'auth.php';
require_once 'db.php';

if (!isLoggedIn()) {
    die(json_encode(['success' => false, 'error' => 'Not authenticated']));
}

$input = json_decode(file_get_contents('php://input'), true);
$post_id = $input['id'] ?? 0;
$user_id = $_SESSION['user_id'];

// Make sure the user owns the post
$query = "DELETE FROM blog_posts WHERE id = $post_id AND user_id = $user_id";
$result = mysqli_query($conn, $query);

if ($result && mysqli_affected_rows($conn) > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to delete post']);
}
?>