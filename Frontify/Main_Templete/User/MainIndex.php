<?php
@include '../../../DataForge/UserData/index_Set.php';
include '../../../DataForge/UserData/config.php'; // Include your database connection file

// Start session to manage user login

$eid = $_SESSION['eid']; // Use `eid` from session as the unique user identifier

// Fetch all posts
$query = "SELECT * FROM post_manage WHERE status='active'";
$posts = $conn->query($query);


// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'like' && isset($_POST['post_id'])) {
            $postId = intval($_POST['post_id']);
            $conn->query("UPDATE post_manage SET like = like + 1 WHERE id = $postId");

            $result = $conn->query("SELECT like FROM post_manage WHERE id = $postId");
            $row = $result->fetch_assoc();
            echo $row['likes'];
            exit;
        } 
        
        elseif ($_POST['action'] === 'comment' && isset($_POST['comment']) && isset($_POST['post_id'])) {
            $comment = htmlspecialchars($_POST['comment']);
            $postId = intval($_POST['post_id']);
            $conn->query("INSERT INTO comment (pst_id, uname, comment) VALUES ('$postId', '$eid', '$comment')");

            $result = $conn->query("SELECT * FROM comment WHERE pst_id = '$postId' ORDER BY id DESC");
            while ($row = $result->fetch_assoc()) {
                echo "<div class='comment'><h4>" . htmlspecialchars($row['uname']) . "</h4><p>" . htmlspecialchars($row['comment']) . "</p></div>";
            }
            exit;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BLOGNEST</title>

    <link rel="stylesheet" href="../../Style/user/header.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #333;
        padding: 10px 20px;
        color: white;
    }

    .header .logo {
        display: flex;
        align-items: center;
    }

    .header .logo img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        margin-right: 10px;
    }

    .header .logo span {
        font-size: 24px;
        font-weight: bold;
    }

    .header .menu {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .header .menu a {
        color: white;
        text-decoration: none;
        font-size: 16px;
    }

    .header .menu a:hover {
        text-decoration: underline;
    }

    .header .user-info {
        display: flex;
        align-items: center;
        cursor: pointer;
    }

    .header .user-info img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 10px;
    }

    .header .user-info span {
        font-size: 16px;
    }

    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background-color: #f9f9f9;
    }

    .blog-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(45%, 1fr));
        gap: 20px;
        padding: 20px;
    }

    .blog-post {
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .blog-post img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .blog-post .content {
        padding: 15px;
    }

    .blog-post .category {
        font-size: 14px;
        font-weight: bold;
        color: #007BFF;
        margin-bottom: 5px;
    }

    .blog-post h2 {
        font-size: 18px;
        color: #333;
        margin: 0 0 10px;
    }

    .blog-post .blog-date {
        font-size: 14px;
        color: #666;
        margin-bottom: 10px;
    }

    .blog-post p {
        font-size: 16px;
        line-height: 1.6;
        color: #555;
    }

    .blog-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 15px;
        border-top: 1px solid #ddd;
        background: #f5f5f5;
    }

    .blog-actions button {
        background: #007BFF;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }

    .blog-actions button:hover {
        background: #0056b3;
    }

    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background: white;
        padding: 20px;
        border-radius: 8px;
        width: 90%;
        max-width: 500px;
        position: relative;
    }

    .close-modal {
        position: absolute;
        top: 10px;
        right: 10px;
        background: transparent;
        border: none;
        font-size: 20px;
        cursor: pointer;
    }

    .comment-section {
        padding: 15px;
        border-top: 1px solid #ddd;
    }

    .comment-section h3 {
        font-size: 16px;
        margin-bottom: 10px;
    }

    .comment-section .comment {
        margin-bottom: 10px;
    }

    .comment-section .comment h4 {
        margin: 0;
        font-size: 14px;
        color: #007BFF;
    }

    .comment-section textarea {
        width: 100%;
        padding: 10px;
        margin-top: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .comment-section button {
        background: #28a745;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }

    .comment-section button:hover {
        background: #218838;
    }
    </style>
</head>

<header>
    <div class="header">
        <!-- Logo Section -->
        <div class="logo">
            <img src="../../../Frontify/Images/BlogNest_LOGO.png" alt="Blognest Logo">
            <span>BLOGNEST</span>
        </div>

        <!-- Menu Section -->
        <div class="menu">
            <a href="MainIndex.php">Home</a>
            <a href="create_blog.php">Create New Blog</a>

            <a href="view_blog.php">My Blog</a>
            <a href="../../../DataForge/UserData/logout.php">Logout</a>
        </div>

        <!-- User Info Section -->
        <div class="user-info" onclick="location.href='setting.php'">
            <img src="<?php echo htmlspecialchars($profileImage); ?>" alt="User Profile">
            <span><?php echo htmlspecialchars($username); ?></span>
        </div>
    </div>
</header>

<body>
    <div class="blog-container">
        <?php while ($row = $posts->fetch_assoc()): ?>
        <div class="blog-post">
            <?php if (!empty($row['con_img'])): ?>
            <img src="data:image/jpeg;base64,<?= base64_encode($row['con_img']) ?>" alt="Post Image">
            <?php endif; ?>
            <div class="content">
                <div class="category">
                    <?= htmlspecialchars($row['category'] ?? 'No Category') ?>
                </div>
                <h2><?= htmlspecialchars($row['blog_title'] ?? 'Untitled Post') ?></h2>
                <p class="blog-date">Published on <?= $row['publish_date'] ?? 'Unknown Date' ?> by
                    <?= htmlspecialchars($row['uname'] ?? 'Unknown Author') ?></p>
                <p><?= nl2br(htmlspecialchars($row['contant'] ?? 'No content available.')) ?></p>
            </div>
            <div class="blog-actions">
                <button class="like-button" data-post-id="<?= $row['id'] ?>">
                    <i class="fas fa-thumbs-up"></i>
                    Like (<span id="like-count-<?= $row['id'] ?>"><?= $row['like'] ?? 0 ?></span>)
                </button>
                <button class="open-comments" data-post-id="<?= $row['id'] ?>">Comments</button>
            </div>

            <div class="comment-section" id="comment-section-<?= $row['id'] ?>" style="display:none;">
                <h3>Comments</h3>
                <div id="comment-list-<?= $row['id'] ?>">
                    <!-- Comments will be loaded here dynamically -->
                </div>
                <textarea class="comment-text" data-post-id="<?= $row['id'] ?>"
                    placeholder="Add a comment..."></textarea>
                <button class="submit-comment" data-post-id="<?= $row['id'] ?>">Submit</button>
            </div>
        </div>
        <?php endwhile; ?>
    </div>



    <script>
    document.querySelectorAll('.like-button').forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.getAttribute('data-post-id');
            const likeCountSpan = document.getElementById(`like-count-${postId}`);

            fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=like&post_id=${postId}`,
                })
                .then(response => response.text())
                .then(data => {
                    if (!isNaN(data)) {
                        likeCountSpan.textContent = data;
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    });

    document.querySelectorAll('.open-comments').forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.getAttribute('data-post-id');
            const commentSection = document.getElementById(`comment-section-${postId}`);

            fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=comment&post_id=${postId}`,
                })
                .then(response => response.text())
                .then(data => {
                    const commentList = document.getElementById(`comment-list-${postId}`);
                    commentList.innerHTML = data;
                    commentSection.style.display = 'block';
                });
        });
    });

    document.querySelectorAll('.submit-comment').forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.getAttribute('data-post-id');
            const commentText = document.querySelector(`.comment-text[data-post-id="${postId}"]`).value;

            fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=comment&post_id=${postId}&comment=${encodeURIComponent(commentText)}`,
                })
                .then(response => response.text())
                .then(data => {
                    const commentList = document.getElementById(`comment-list-${postId}`);
                    commentList.innerHTML = data;
                    document.querySelector(`.comment-text[data-post-id="${postId}"]`).value = '';
                });
        });
    });
    </script>

</body>

</html>