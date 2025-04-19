<?php
require_once 'auth.php';
require_once 'db.php';

if (!isLoggedIn()) {
    die(json_encode(['success' => false, 'error' => 'Not authenticated']));
}

$input = json_decode(file_get_contents('php://input'), true);
$user_id = $_SESSION['user_id'];

// Sanitize inputs
$title = mysqli_real_escape_string($conn, $input['title'] ?? '');
$meta_description = mysqli_real_escape_string($conn, $input['meta_description'] ?? '');
$content = mysqli_real_escape_string($conn, $input['content'] ?? '');
$status = mysqli_real_escape_string($conn, $input['status'] ?? 'draft');
$post_id = $input['id'] ?? null;

if (empty($title)) {
    die(json_encode(['success' => false, 'error' => 'Title is required']));
}

if ($post_id) {
    // Update existing post
    $query = "UPDATE blog_posts SET 
              title = '$title',
              meta_description = '$meta_description',
              content = '$content',
              status = '$status'
              WHERE id = $post_id AND user_id = $user_id";
} else {
    // Create new post
    $query = "INSERT INTO blog_posts 
              (user_id, title, meta_description, content, status) 
              VALUES 
              ($user_id, '$title', '$meta_description', '$content', '$status')";
}

$result = mysqli_query($conn, $query);

if ($result) {
    $id = $post_id ?? mysqli_insert_id($conn);
    echo json_encode(['success' => true, 'id' => $id]);
} else {
    echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
}
?>