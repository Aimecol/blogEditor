<?php
require_once 'php/auth.php';

if (!isLoggedIn()) {
    header('Location: index.php');
    exit();
}

require_once 'php/db.php';

// Fetch user's posts
$user_id = $_SESSION['user_id'];
$query = "SELECT id, title, status, created_at, updated_at FROM blog_posts WHERE user_id = $user_id ORDER BY updated_at DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Editor - Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4a90e2;
            --dark-color: #2c3e50;
            --light-color: #f5f6fa;
            --error-color: #e74c3c;
            --success-color: #2ecc71;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--light-color);
            min-height: 100vh;
        }

        .navbar {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h1 {
            color: var(--dark-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar .user-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: #357abd;
        }

        .btn-logout {
            background: var(--error-color);
            color: white;
        }

        .posts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .post-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .post-card:hover {
            transform: translateY(-5px);
        }

        .post-status {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }

        .status-draft {
            background: #ffeaa7;
            color: #b7791f;
        }

        .status-published {
            background: #c4e6ff;
            color: #2779bd;
        }

        .post-title {
            font-size: 1.25rem;
            color: var(--dark-color);
            margin-bottom: 1rem;
        }

        .post-meta {
            font-size: 0.875rem;
            color: #666;
            margin-bottom: 1rem;
        }

        .post-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .post-actions button {
            padding: 0.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .post-actions button:hover {
            background: #eee;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #666;
        }

        @media (max-width: 768px) {
            .posts-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <h1><i class="fas fa-pen-fancy"></i> Blog Editor</h1>
        <div class="user-actions">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="editor.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Post
            </a>
            <a href="php/logout.php" class="btn btn-logout">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </nav>

    <div class="container">
        <div class="posts-grid">
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php while ($post = mysqli_fetch_assoc($result)): ?>
                    <div class="post-card">
                        <span class="post-status status-<?php echo $post['status']; ?>">
                            <?php echo ucfirst($post['status']); ?>
                        </span>
                        <h2 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h2>
                        <div class="post-meta">
                            <div>Created: <?php echo date('M j, Y', strtotime($post['created_at'])); ?></div>
                            <div>Updated: <?php echo date('M j, Y', strtotime($post['updated_at'])); ?></div>
                        </div>
                        <div class="post-actions">
                            <a href="editor.php?id=<?php echo $post['id']; ?>" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php if ($post['status'] === 'published'): ?>
                                <a href="view_post.php?id=<?php echo $post['id']; ?>" target="_blank" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                            <?php endif; ?>
                            <button onclick="deletePost(<?php echo $post['id']; ?>)" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-pen-to-square fa-3x"></i>
                    <h2>No posts yet</h2>
                    <p>Create your first blog post to get started!</p>
                    <a href="editor.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create New Post
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function deletePost(postId) {
            if (confirm('Are you sure you want to delete this post?')) {
                fetch('php/delete_post.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: postId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Failed to delete post');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the post');
                });
            }
        }
    </script>
</body>
</html>