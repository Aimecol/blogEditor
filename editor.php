<?php
require_once 'php/auth.php';
require_once 'php/db.php';

if (!isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$post = null;
if (isset($_GET['id'])) {
    $post_id = (int)$_GET['id'];
    $user_id = $_SESSION['user_id'];
    $query = "SELECT * FROM blog_posts WHERE id = $post_id AND user_id = $user_id";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $post = mysqli_fetch_assoc($result);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Editor - <?php echo $post ? 'Edit Post' : 'New Post'; ?></title>
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
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .container {
            max-width: 1200px;
            margin: 5rem auto 2rem;
            padding: 0 1rem;
        }

        .editor-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .toolbar {
            padding: 1rem;
            border-bottom: 1px solid #eee;
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            background: #f8f9fa;
        }

        .toolbar button {
            padding: 0.5rem;
            border: none;
            background: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .toolbar button:hover {
            background: #e9ecef;
        }

        .toolbar button.active {
            background: var(--primary-color);
            color: white;
        }

        .toolbar-group {
            display: flex;
            gap: 0.25rem;
            padding: 0 0.5rem;
            border-right: 1px solid #dee2e6;
        }

        .toolbar-group:last-child {
            border-right: none;
        }

        .post-meta {
            padding: 1rem;
            background: white;
        }

        .post-meta input {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            font-size: 1rem;
        }

        .editor {
            min-height: 500px;
            padding: 2rem;
            outline: none;
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .editor:focus {
            outline: none;
        }

        .editor h1, .editor h2, .editor h3, .editor h4, .editor h5, .editor h6 {
            margin: 1.5rem 0 1rem;
            color: var(--dark-color);
        }

        .editor p {
            margin-bottom: 1rem;
        }

        .editor blockquote {
            border-left: 4px solid var(--primary-color);
            margin: 1rem 0;
            padding: 1rem;
            background: #f8f9fa;
        }

        .editor pre {
            background: #2d3436;
            color: #fff;
            padding: 1rem;
            border-radius: 5px;
            margin: 1rem 0;
            font-family: monospace;
            white-space: pre-wrap;
        }

        .editor img {
            max-width: 100%;
            height: auto;
            margin: 1rem 0;
        }

        .status-bar {
            padding: 1rem;
            background: #f8f9fa;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .word-count {
            color: #666;
            font-size: 0.9rem;
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
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-outline {
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
            background: none;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .save-indicator {
            position: fixed;
            top: 1rem;
            right: 1rem;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            background: var(--success-color);
            color: white;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .save-indicator.show {
            opacity: 1;
        }

        @media (max-width: 768px) {
            .toolbar-group {
                border-right: none;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <h1><i class="fas fa-pen-fancy"></i> Blog Editor</h1>
        <div>
            <a href="dashboard.php" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </nav>

    <div class="container">
        <form id="postForm" class="editor-container">
            <div class="post-meta">
                <input type="text" id="title" placeholder="Post Title" value="<?php echo $post ? htmlspecialchars($post['title']) : ''; ?>" required>
                <input type="text" id="meta_description" placeholder="Meta Description (SEO)" value="<?php echo $post ? htmlspecialchars($post['meta_description']) : ''; ?>">
            </div>

            <div class="toolbar">
                <div class="toolbar-group">
                    <button type="button" data-command="h1" title="Heading 1"><i class="fas fa-heading"></i> 1</button>
                    <button type="button" data-command="h2" title="Heading 2"><i class="fas fa-heading"></i> 2</button>
                    <button type="button" data-command="h3" title="Heading 3"><i class="fas fa-heading"></i> 3</button>
                </div>
                <div class="toolbar-group">
                    <button type="button" data-command="bold" title="Bold"><i class="fas fa-bold"></i></button>
                    <button type="button" data-command="italic" title="Italic"><i class="fas fa-italic"></i></button>
                    <button type="button" data-command="underline" title="Underline"><i class="fas fa-underline"></i></button>
                </div>
                <div class="toolbar-group">
                    <button type="button" data-command="justifyLeft" title="Align Left"><i class="fas fa-align-left"></i></button>
                    <button type="button" data-command="justifyCenter" title="Align Center"><i class="fas fa-align-center"></i></button>
                    <button type="button" data-command="justifyRight" title="Align Right"><i class="fas fa-align-right"></i></button>
                </div>
                <div class="toolbar-group">
                    <button type="button" data-command="insertUnorderedList" title="Bullet List"><i class="fas fa-list-ul"></i></button>
                    <button type="button" data-command="insertOrderedList" title="Numbered List"><i class="fas fa-list-ol"></i></button>
                </div>
                <div class="toolbar-group">
                    <button type="button" data-command="createLink" title="Insert Link"><i class="fas fa-link"></i></button>
                    <button type="button" data-command="insertImage" title="Insert Image"><i class="fas fa-image"></i></button>
                    <button type="button" data-command="code" title="Code Block"><i class="fas fa-code"></i></button>
                    <button type="button" data-command="blockquote" title="Blockquote"><i class="fas fa-quote-right"></i></button>
                </div>
            </div>

            <div id="editor" class="editor" contenteditable="true"><?php echo $post ? $post['content'] : ''; ?></div>

            <div class="status-bar">
                <div class="word-count">0 words</div>
                <div>
                    <button type="button" class="btn btn-outline" data-action="save-draft">
                        <i class="fas fa-save"></i> Save Draft
                    </button>
                    <button type="button" class="btn btn-primary" data-action="publish">
                        <i class="fas fa-paper-plane"></i> Publish
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div id="saveIndicator" class="save-indicator">
        <i class="fas fa-check"></i> Changes saved
    </div>

    <script>
        const editor = document.getElementById('editor');
        const toolbar = document.querySelector('.toolbar');
        const saveIndicator = document.getElementById('saveIndicator');
        let autoSaveTimeout;
        const postId = <?php echo $post ? $post['id'] : 'null'; ?>;

        // Initialize editor
        editor.addEventListener('input', () => {
            updateWordCount();
            scheduleAutoSave();
        });

        // Toolbar functionality
        toolbar.addEventListener('click', (e) => {
            const button = e.target.closest('button');
            if (!button) return;

            e.preventDefault();
            const command = button.dataset.command;

            switch(command) {
                case 'h1':
                case 'h2':
                case 'h3':
                    document.execCommand('formatBlock', false, command);
                    break;
                case 'createLink':
                    const url = prompt('Enter URL:');
                    if (url) document.execCommand(command, false, url);
                    break;
                case 'insertImage':
                    const imgUrl = prompt('Enter image URL:');
                    if (imgUrl) document.execCommand(command, false, imgUrl);
                    break;
                case 'code':
                    document.execCommand('insertHTML', false, '<pre><code>' + window.getSelection() + '</code></pre>');
                    break;
                case 'blockquote':
                    document.execCommand('formatBlock', false, 'blockquote');
                    break;
                default:
                    document.execCommand(command, false, null);
            }
        });

        // Word count
        function updateWordCount() {
            const text = editor.innerText || '';
            const wordCount = text.trim().split(/\s+/).length;
            document.querySelector('.word-count').textContent = `${wordCount} words`;
        }

        // Auto-save functionality
        function scheduleAutoSave() {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(savePost, 2000);
        }

        async function savePost(status = 'draft') {
            const title = document.getElementById('title').value;
            const metaDescription = document.getElementById('meta_description').value;
            const content = editor.innerHTML;

            try {
                const response = await fetch('php/save_post.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id: postId,
                        title,
                        meta_description: metaDescription,
                        content,
                        status
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    showSaveIndicator();
                    if (status === 'published') {
                        window.location.href = 'dashboard.php';
                    }
                } else {
                    alert('Failed to save post');
                }
            } catch (error) {
                console.error('Error saving post:', error);
                alert('Error saving post');
            }
        }

        function showSaveIndicator() {
            saveIndicator.classList.add('show');
            setTimeout(() => {
                saveIndicator.classList.remove('show');
            }, 2000);
        }

        // Save and publish buttons
        document.querySelector('[data-action="save-draft"]').addEventListener('click', () => savePost('draft'));
        document.querySelector('[data-action="publish"]').addEventListener('click', () => savePost('published'));

        // Initial word count
        updateWordCount();
    </script>
</body>
</html>