<?php
require_once 'php/db.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$post_id = (int)$_GET['id'];
$query = "SELECT p.*, u.username FROM blog_posts p 
          JOIN users u ON p.user_id = u.id 
          WHERE p.id = $post_id AND p.status = 'published'";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    header('Location: index.php');
    exit();
}

$post = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($post['meta_description']); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4a90e2;
            --dark-color: #2c3e50;
            --light-color: #f5f6fa;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--light-color);
            color: #333;
            line-height: 1.6;
        }

        .navbar {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .post-header {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .post-title {
            font-size: 2.5rem;
            color: var(--dark-color);
            margin-bottom: 1rem;
        }

        .post-meta {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .post-content {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .post-content h1,
        .post-content h2,
        .post-content h3,
        .post-content h4,
        .post-content h5,
        .post-content h6 {
            color: var(--dark-color);
            margin: 1.5rem 0 1rem;
        }

        .post-content p {
            margin-bottom: 1rem;
        }

        .post-content img {
            max-width: 100%;
            height: auto;
            margin: 1rem 0;
            border-radius: 5px;
        }

        .post-content blockquote {
            border-left: 4px solid var(--primary-color);
            padding: 1rem;
            margin: 1rem 0;
            background: #f8f9fa;
        }

        .post-content pre {
            background: #2d3436;
            color: #fff;
            padding: 1rem;
            border-radius: 5px;
            overflow-x: auto;
            margin: 1rem 0;
            font-family: monospace;
        }

        .post-content a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .post-content a:hover {
            text-decoration: underline;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-outline {
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
            background: none;
        }

        .btn-outline:hover {
            background: var(--primary-color);
            color: white;
        }

        @media (max-width: 768px) {
            .post-title {
                font-size: 2rem;
            }

            .container {
                padding: 0 0.5rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="btn btn-outline">
            <i class="fas fa-home"></i> Home
        </a>
    </nav>

    <div class="container">
        <article>
            <header class="post-header">
                <h1 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h1>
                <div class="post-meta">
                    <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($post['username']); ?></span>
                    <span><i class="fas fa-calendar"></i> <?php echo date('F j, Y', strtotime($post['created_at'])); ?></span>
                    <?php if ($post['updated_at'] !== $post['created_at']): ?>
                        <span><i class="fas fa-edit"></i> Updated: <?php echo date('F j, Y', strtotime($post['updated_at'])); ?></span>
                    <?php endif; ?>
                </div>
            </header>

            <div class="post-content">
                <?php echo $post['content']; ?>
            </div>
        </article>
    </div>
</body>
</html>