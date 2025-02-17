<?php
@include '../../../DataForge/UserData/index_Set.php';
include '../../../DataForge/UserData/config.php'; // Include your database connection file

// Fetch all posts
$query = "SELECT * FROM post_manage";
$posts = $conn->query($query);

// Start session to manage user login

$eid = $_SESSION['eid']; // Use `eid` from session as the unique user identifier

// Handle AJAX requests for likes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'like') {
    $postId = $_POST['post_id'];

    // Check if the user has already liked this post using session
    if (!isset($_SESSION['liked_posts'])) {
        $_SESSION['liked_posts'] = []; // Initialize session variable to track likes
    }

    if (in_array($postId, $_SESSION['liked_posts'])) {
        // Unlike the post
        $updateQuery = "UPDATE post_manage SET `like` = `like` - 1 WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("i", $postId);
        $stmt->execute();

        // Remove post from the liked posts session
        $_SESSION['liked_posts'] = array_diff($_SESSION['liked_posts'], [$postId]);
    } else {
        // Like the post
        $updateQuery = "UPDATE post_manage SET `like` = `like` + 1 WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("i", $postId);
        $stmt->execute();

        // Add post to the liked posts session
        $_SESSION['liked_posts'][] = $postId;
    }

    // Fetch updated likes count
    $likeQuery = "SELECT `like` FROM post_manage WHERE id = ?";
    $stmt = $conn->prepare($likeQuery);
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $stmt->bind_result($likes);
    $stmt->fetch();

    echo json_encode(['success' => true, 'like' => $likes]);
    exit;
}

// Handle AJAX requests for fetching comments
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'fetch_comments') {
    $postId = $_GET['pst_id'];
    $commentQuery = "SELECT * FROM comment WHERE post_id = ?";
    $stmt = $conn->prepare($commentQuery);
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $result = $stmt->get_result();

    $comments = [];
    while ($row = $result->fetch_assoc()) {
        $comments[] = $row;
    }
    echo json_encode($comments);
    exit;
}

// Handle AJAX requests for adding comments
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_comment') {
    $postId = $_POST['pst_id'];
    $comment = $_POST['comment'];

    $insertComment = "INSERT INTO comment (id, pst_id, comment) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insertComment);
    $stmt->bind_param("sis", $eid, $postId, $comment);
    $stmt->execute();

    echo json_encode(['success' => true]);
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../Style/user/view_all.css">
    <link rel="stylesheet" href="../../Style/user/header.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <title>Document</title>
</head>
<header class="header" id="header">
    <div class="header_toggle"><i class='bx bx-menu' id="header-toggle"></i></div>
    <div class="header_profile">
        <!-- Profile Image --><a href="./setting.php" class="profile_image_container"><img
                src="<?php echo htmlspecialchars($_SESSION['profile_image']); ?>" alt="Profile Picture"
                class="profile_image"></a>
        <!-- Username --><span class="username"><?php echo htmlspecialchars($_SESSION['user_name']);
    ?></span>
    </div>
</header>

<body>
    <form method="POST">
        <div class="blog-container">
            <?php while ($row = $posts->fetch_assoc()): ?>
            <div class="blog-post" data-id="<?= $row['id'] ?>">
                <div class="category"><?= htmlspecialchars($row['category'] ?? 'No Category') ?></div>
                <h2 class="blog-title"><?= htmlspecialchars($row['blog_title'] ?? 'Untitled Post') ?></h2>
                <p class="blog-date">Published on <?= $row['publish_date'] ?? 'Unknown Date' ?> by
                    <?= htmlspecialchars($row['uname'] ?? 'Unknown Author') ?></p>
                <?php if (!empty($row['con_img'])): ?>
                <div class="blog-image"><img src="data:image/jpeg;base64,<?= base64_encode($row['con_img']) ?>"
                        alt="Post Image"></div>
                <?php elseif (!empty($row['con_video'])): ?>
                <video controls class="blog-video">
                    <source src="data:video/mp4;base64,<?= base64_encode($row['con_video']) ?>" type="video/mp4">
                </video>
                <?php endif; ?>
                <p class="blog-content"><?= nl2br(htmlspecialchars($row['contant'] ?? 'No content available.')) ?></p>
                <div class="blog-actions">
                    <div class="like-btn" data-id="<?= $row['id'] ?>">
                        <i class="fas fa-thumbs-up"></i>
                        <span class="like-count"><?= htmlspecialchars($row['like'] ?? 0) ?></span>
                    </div>

                    <?php if ($row['option'] == 1): ?>
                    <div class="comment-btn" data-id="<?= $row['id'] ?>">
                        <i class="fas fa-comment"></i> Comment
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        </div>


        <div id="comment-popup" style="display: none;">
            <div class="popup-content">
                <h3>Comments</h3>
                <div id="comments-list"></div>
                <textarea id="comment-input" placeholder="Write a comment..."></textarea>
                <button id="submit-comment">Submit</button>
                <button id="close-popup">Close</button>
            </div>
        </div>
    </form>

    <!--<script src="../../Script/user/view_all.js">-->
    </script>
    <script>
    document.querySelector('.blog-container').addEventListener('click', (e) => {
        // Handle like button click
        if (e.target.closest('.like-btn')) {
            const btn = e.target.closest('.like-btn');
            const postId = btn.dataset.id;

            fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        action: 'like',
                        post_id: postId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        btn.querySelector('.like-count').textContent = data.likes;
                    }
                });
        }

        // Handle comment button click
        if (e.target.closest('.comment-btn')) {
            const btn = e.target.closest('.comment-btn');
            const postId = btn.dataset.id;

            fetch(`?action=fetch_comments&post_id=${postId}`)
                .then(response => response.json())
                .then(comments => {
                    const popup = document.getElementById('comment-popup');
                    const commentsList = document.getElementById('comments-list');
                    commentsList.innerHTML = comments.map(c =>
                        `<p><b>${c.uname}</b>: ${c.comment}</p>`).join('');
                    popup.style.display = 'block';

                    document.getElementById('submit-comment').onclick = () => {
                        const commentInput = document.getElementById('comment-input').value;
                        fetch('', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: new URLSearchParams({
                                action: 'add_comment',
                                post_id: postId,
                                comment: commentInput
                            })
                        }).then(() => {
                            popup.style.display = 'none';
                        });
                    };

                    document.getElementById('close-popup').onclick = () => {
                        popup.style.display = 'none';
                    };
                });
        }
    });


    const categorySelect = document.getElementById('category-select');
    const searchInput = document.getElementById('search-input');
    const searchBtn = document.getElementById('search-btn');
    const blogPosts = document.querySelectorAll('.blog-post');

    // Filter blog posts based on category selection
    categorySelect.addEventListener('change', () => {
        const selectedCategory = categorySelect.value;
        blogPosts.forEach(post => {
            if (selectedCategory === 'all' || post.querySelector('.category').textContent ===
                selectedCategory) {
                post.style.display = 'block';
            } else {
                post.style.display = 'none';
            }
        });
    });

    // Filter blog posts based on search input
    searchBtn.addEventListener('click', () => {
        const searchTerm = searchInput.value.toLowerCase();
        blogPosts.forEach(post => {
            const blogTitle = post.querySelector('.blog-title').textContent.toLowerCase();
            const blogContent = post.querySelector('.blog-content').textContent.toLowerCase();
            if (blogTitle.includes(searchTerm) || blogContent.includes(searchTerm)) {
                post.style.display = 'block';
            } else {
                post.style.display = 'none';
            }
        });
    });


    //****************************  like ************************************************** */
    document.querySelectorAll('.like-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const postId = btn.dataset.id;
            fetch('like_post.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        post_id: postId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        btn.querySelector('.like-count').textContent = data.likes;
                    }
                });
        });
    });


    /********************************************** comment pop up */
    document.querySelectorAll('.comment-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const postId = btn.dataset.id;
            fetch(`fetch_comments.php?post_id=${postId}`)
                .then(response => response.json())
                .then(comments => {
                    const popup = document.createElement('div');
                    popup.classList.add('comment-popup');
                    popup.innerHTML =
                        `<h3>Comments</h3><div>${comments.map(c => `<p><b>${c.uname}</b>: ${c.comment}</p>`).join('')}</div>`;
                    document.body.appendChild(popup);
                });
        });
    });
    </script>
</body>

</html>