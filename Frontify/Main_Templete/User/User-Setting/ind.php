<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Blog Posts</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f9f9f9;
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

    .header .menu a {
        color: white;
        text-decoration: none;
        font-size: 16px;
        margin-right: 20px;
    }

    .header .menu a:hover {
        text-decoration: underline;
    }

    .posts-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
        padding: 20px;
    }

    .post-box {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        width: 300px;
    }

    .post-box img {
        width: 100%;
        height: 150px;
        object-fit: cover;
    }

    .post-content {
        padding: 15px;
    }

    .post-title {
        font-size: 18px;
        color: #333;
        margin: 0 0 10px;
    }

    .post-date {
        font-size: 14px;
        color: #888;
        margin-bottom: 10px;
    }

    .content-text {
        font-size: 14px;
        color: #555;
        margin-bottom: 15px;
    }

    .post-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 15px;
        border-top: 1px solid #ddd;
    }

    .post-footer button {
        background-color: #ff5733;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 5px 10px;
        cursor: pointer;
        font-size: 12px;
    }

    .post-footer button:hover {
        background-color: #e64e2b;
    }

    .comment-popup {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: white;
        border: 1px solid #ccc;
        padding: 20px;
        z-index: 1000;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        display: none;
    }

    .comment-popup h2 {
        margin: 0 0 10px;
    }

    .comment-popup ul {
        padding: 0;
        margin: 0 0 20px;
        list-style-type: none;
    }

    .comment-popup li {
        padding: 5px 0;
        border-bottom: 1px solid #ddd;
    }

    .comment-popup button {
        background-color: #333;
        color: white;
        padding: 5px 10px;
        border: none;
        cursor: pointer;
    }

    .comment-popup button:hover {
        background-color: #555;
    }
    </style>
</head>

<body>
    <header>
        <div class="header">
            <div class="logo">
                <img src="../../../Frontify/Images/BlogNest_LOGO.png" alt="Blognest Logo">
                <span>BLOGNEST</span>
            </div>
            <div class="menu">
                <a href="MainIndex.php">Home</a>
                <a href="create_blog.php">Create New Blog</a>
                <a href="view_blog.php">My Blog</a>
                <a href="../../../DataForge/UserData/logout.php">Logout</a>
            </div>
        </div>
    </header>

    <div class="posts-container">
        <?php
        if ($result_posts && mysqli_num_rows($result_posts) > 0) {
            while ($post = mysqli_fetch_assoc($result_posts)) {
        ?>
        <div class="post-box" data-id="<?php echo $post['id']; ?>">
            <?php if ($post['con_img']): ?>
            <img src="data:image/jpeg;base64,<?php echo base64_encode($post['con_img']); ?>" alt="Post Image">
            <?php endif; ?>

            <div class="post-content">
                <span class="post-date"><?php echo date('F j, Y', strtotime($post['publish_date'])); ?></span>
                <h3 class="post-title"><?php echo htmlspecialchars($post['blog_title']); ?></h3>
                <p class="content-text">
                    <?php echo nl2br(htmlspecialchars($post['contant'])); ?>
                </p>
            </div>

            <div class="post-footer">
                <button class="like-btn">❤️ <?php echo $post['like']; ?></button>
                <button class="comment-btn" onclick="toggleCommentPopup(<?php echo $post['id']; ?>)">💬</button>
            </div>
        </div>
        <?php
            }
        } else {
            echo "<p>No posts available.</p>";
        }
        ?>
    </div>

    <script>
    function toggleCommentPopup(postId) {
        const popup = document.getElementById('popup-' + postId);
        popup.style.display = popup.style.display === 'none' ? 'block' : 'none';
    }

    function closePopup(postId) {
        const popup = document.getElementById('popup-' + postId);
        popup.style.display = 'none';
    }
    </script>
</body>

</html>



<?php
@include '../../../DataForge/UserData/index_Set.php';
@include '../../../DataForge/UserData/config.php';


// Redirect to login if the session is not active
if (!isset($_SESSION['eid'])) {
    header('Location: ../../../Frontify/Enterance/login.php');
    exit();
}

// Sanitize the email from the session
$email = mysqli_real_escape_string($conn, $_SESSION['eid']);

// Query to fetch user details
$query = "SELECT uname, eid AS email, profile_image, type FROM log_detail WHERE eid = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Initialize variables
$username = null;
$profileImage = null;
$userType = null;

// Fetch user details from the database if available
if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    $username = $user['uname'];
    $email = $user['email'];
    $profileImage = $user['profile_image'];
    $userType = $user['type'];

    // Check if profile image exists, otherwise use a default image
    $profileImage = (!empty($profileImage)) ? $profileImage : '../../../Frontify/Images/Person.jpg';

    // Set session variables
    $_SESSION['user_name'] = $username;
    $_SESSION['profile_image'] = $profileImage;
    $_SESSION['user_type'] = $userType;
} else {
    // Redirect back to login if user details are not found (safety net)
    session_destroy();
    header('Location: ../../../Frontify/Enterance/login.php');
    exit();
}

// Free resources
mysqli_free_result($result);
mysqli_stmt_close($stmt);

 /* -------------------------------------------------- comment --------------------------------------------------------------------- */
 $query_posts = "SELECT pm.id, pm.blog_title, pm.publish_date, pm.con_vedio, pm.con_img, pm.contant, pm.status, pm.like,
 GROUP_CONCAT(c.comment SEPARATOR ', ') AS comments
FROM post_manage pm
LEFT JOIN comment c ON pm.id = c.pst_id
WHERE pm.uname = ?
GROUP BY pm.id
ORDER BY pm.publish_date DESC";

$stmt_posts = mysqli_prepare($conn, $query_posts);
mysqli_stmt_bind_param($stmt_posts, "s", $username);
mysqli_stmt_execute($stmt_posts);
$result_posts = mysqli_stmt_get_result($stmt_posts);


/* -------------------------------------------------   delete   ---------------------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post_id'])) {
    $deletePostId = (int)$_POST['delete_post_id'];
    $query_delete = "DELETE FROM post_manage WHERE id = ? AND uname = ?";
    $stmt_delete = mysqli_prepare($conn, $query_delete);
    mysqli_stmt_bind_param($stmt_delete, "is", $deletePostId, $username);
    mysqli_stmt_execute($stmt_delete);

    if (mysqli_stmt_affected_rows($stmt_delete) > 0) {
        echo json_encode(['status' => 'success', 'post_id' => $deletePostId]);
    } else {
        echo json_encode(['status' => 'error']);
    }
    
    mysqli_stmt_close($stmt_delete);
    exit();
}


   

?>

<!-- HTML Content for displaying posts -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Blog Posts</title>
    <link rel="stylesheet" href="../../Style/user/view.css">
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
    <div class="posts-container">
        <?php
        if ($result_posts && mysqli_num_rows($result_posts) > 0) {
            // Display each post
            while ($post = mysqli_fetch_assoc($result_posts)) {
                ?>
        <div class="post-box" data-id="<?php echo $post['id']; ?>">

            <div class="post-header">
                <h1 class="post-title"><?php echo htmlspecialchars($post['blog_title']); ?></h1>
                <span class="post-date"><?php echo date('F j, Y', strtotime($post['publish_date'])); ?></span>
            </div>
            <div class="post-content">
                <?php if ($post['con_vedio']): ?>
                <video width="100%" controls>
                    <source src="data:video/mp4;base64,<?php echo base64_encode($post['con_vedio']); ?>"
                        type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                <?php elseif ($post['con_img']): ?>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($post['con_img']); ?>" alt="Post Image"
                    class="post-image">
                <?php endif; ?>
                <p class="content-text"><?php echo nl2br(htmlspecialchars($post['contant'])); ?></p>
            </div>

            <!-- Like and Comment Section -->
            <div class="post-footer">
                <div class="post-interactions">
                    <button class="like-btn">
                        ❤️ <?php echo $post['like']; ?>
                    </button>
                    <button class="comment-btn" onclick="toggleCommentPopup(<?php echo $post['id']; ?>)">
                        💬
                    </button>
                </div>
                <div class="post-actions">
                    <button class="update-btn"><?php echo htmlspecialchars($post['status']); ?></button>
                    <button class="delete-btn" onclick="deletePostAjax(<?php echo $post['id']; ?>)">Delete Post</button>

                </div>
            </div>

            <!-- Comment Popup -->
            <!-- Comment Popup -->
            <div class="comment-popup" id="popup-<?php echo $post['id']; ?>" style="display: none;">
                <div class="popup-content">
                    <h2>Comments</h2>
                    <ul class="comment-list">
                        <?php 
                        $comments = $post['comments'] ? explode(', ', $post['comments']) : [];
                        foreach ($comments as $comment) {
                            echo "<li>" . htmlspecialchars($comment) . "</li>";
                        }
                        ?>
                    </ul>
                    <button onclick="closePopup(<?php echo $post['id']; ?>)">Close</button>
                </div>
            </div>


        </div>
        <?php
            }
        } else {
            echo "<p>No posts available.</p>";
        }
        ?>
    </div>

    <script>
    function toggleCommentPopup(postId) {
        const popup = document.getElementById('popup-' + postId);
        popup.style.display = popup.style.display === 'none' ? 'block' : 'none';
    }

    function closePopup(postId) {
        const popup = document.getElementById('popup-' + postId);
        popup.style.display = 'none';
    }

    function updatePost(postId) {
        window.location.href = 'update_post.php?id=' + postId;
    }
    </script>
    <script>
    function deletePostAjax(postId) {
        if (confirm('Are you sure you want to delete this post?')) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '', true); // Send to the same page
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onload = function() {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.status === 'success') {
                        const postElement = document.querySelector('.post-box[data-id="' + response.post_id + '"]');
                        if (postElement) {
                            postElement.remove();
                        }
                    } else {
                        alert('Failed to delete the post. Please try again.');
                    }
                }
            };

            xhr.send('delete_post_id=' + postId);
        }
    }
    </script>

</body>

</html>

<?php
// Free resources
mysqli_free_result($result_posts);
mysqli_stmt_close($stmt_posts);

// Close the database connection
mysqli_close($conn);
?><?php
@include '../../../DataForge/UserData/index_Set.php';
include '../../../DataForge/UserData/config.php'; // Include your database connection file

// Start session to manage user login
session_start();
$eid = $_SESSION['eid']; // Use `eid` from session as the unique user identifier

// Fetch all posts
$query = "SELECT * FROM post_manage WHERE status='active'";
$posts = $conn->query($query);

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'like' && isset($_POST['post_id'])) {
            $postId = intval($_POST['post_id']);
            $conn->query("UPDATE post_manage SET likes = likes + 1 WHERE id = $postId");

            $result = $conn->query("SELECT likes FROM post_manage WHERE id = $postId");
            $row = $result->fetch_assoc();
            echo $row['likes'];
            exit;
        } elseif ($_POST['action'] === 'comment' && isset($_POST['comment']) && isset($_POST['post_id'])) {
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
    /* Add your existing styles here */
    </style>
</head>

<body>
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
                    Like (<span id="like-count-<?= $row['id'] ?>"><?= $row['likes'] ?? 0 ?></span>)
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



<?php
@include '../../../DataForge/AdminData/Aindex_set.php';
@include '../../../DataForge/AdminData/config.php';

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch posts with status 'active'
$posts = $conn->query("SELECT * FROM post_manage WHERE status = 'active' ORDER BY id DESC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = $_POST['post_id'];
    $publish = isset($_POST['publish']) ? 1 : 0; // Checkbox value (1 for checked, 0 for unchecked)

    // Update the post's publish status in the database
    $stmt = $conn->prepare("UPDATE post_manage SET set_home = ? WHERE id = ?");
    $stmt->bind_param("ii", $publish, $post_id);

    if ($stmt->execute()) {
        echo "Post status updated successfully.";
    } else {
        echo "Error updating post status: " . $conn->error;
    }

    // Redirect back to the current page (home.php)
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Styled Blog Posts</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
    body {
        font-family: 'Arial', sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f8f9fa;
    }

    header {
        background-color: #333;
        color: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 30px;
    }

    .logo {
        display: flex;
        align-items: center;
        font-size: 24px;
        font-weight: bold;
    }

    .logo img {
        width: 40px;
        margin-right: 10px;
    }

    .blog-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        padding: 20px;
    }

    .blog-post {
        background: #fff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .blog-post:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    .category {
        background-color: #007bff;
        color: white;
        font-size: 14px;
        padding: 5px 10px;
        text-transform: uppercase;
        font-weight: bold;
        text-align: center;
    }

    .blog-title {
        font-size: 20px;
        font-weight: bold;
        margin: 15px;
    }

    .blog-date {
        font-size: 14px;
        color: #6c757d;
        margin: 0 15px 10px;
    }

    .blog-image img {
        width: 100%;
        height: auto;
    }

    .blog-content {
        padding: 15px;
        color: #333;
    }

    .publish-checkbox {
        display: flex;
        align-items: center;
        margin: 15px;
    }

    .publish-checkbox input {
        margin-right: 10px;
        transform: scale(1.2);
    }

    .publish-checkbox button {
        padding: 8px 16px;
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }

    .blog-actions {
        margin: 15px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }

    .like-btn {
        display: flex;
        align-items: center;
        cursor: pointer;
        color: #007bff;
    }

    .like-btn i {
        margin-right: 5px;
    }

    .like-btn:hover {
        color: #0056b3;
    }
    </style>
</head>

<body>
    <header>
        <div class="logo">
            <img src="../../images/blognest_logo.png" alt="BlogNest Logo">
            BlogNest
        </div>
        <div class="header_profile">
            <img src="<?php echo htmlspecialchars($_SESSION['profile_image'] ?: '../../../Frontify/Images/Person.jpg'); ?>"
                alt="Profile Picture" style="width: 40px; border-radius: 50%;">
            <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
        </div>
    </header>

    <div class="blog-container">
        <?php while ($row = $posts->fetch_assoc()): ?>
        <div class="blog-post">
            <div class="category"> <?= htmlspecialchars($row['category'] ?? 'No Category') ?> </div>
            <h2 class="blog-title"> <?= htmlspecialchars($row['blog_title'] ?? 'Untitled Post') ?> </h2>
            <p class="blog-date"> Published on <?= $row['publish_date'] ?? 'Unknown Date' ?> by
                <?= htmlspecialchars($row['uname'] ?? 'Unknown Author') ?> </p>

            <?php if (!empty($row['con_img'])): ?>
            <div class="blog-image">
                <img src="data:image/jpeg;base64,<?= base64_encode($row['con_img']) ?>" alt="Post Image">
            </div>
            <?php elseif (!empty($row['con_video'])): ?>
            <video controls class="blog-video">
                <source src="data:video/mp4;base64,<?= base64_encode($row['con_video']) ?>" type="video/mp4">
            </video>
            <?php endif; ?>

            <p class="blog-content"> <?= nl2br(htmlspecialchars($row['contant'] ?? 'No content available.')) ?> </p>

            <form method="POST" action="home.php">
                <div class="publish-checkbox">
                    <input type="checkbox" id="publish_<?= $row['id'] ?>" name="publish" value="1"
                        <?= $row['set_home'] == 1 ? 'checked' : '' ?>>
                    <label for="publish_<?= $row['id'] ?>">Publish</label>
                    <input type="hidden" name="post_id" value="<?= $row['id'] ?>">
                    <button type="submit">Update</button>
                </div>
            </form>

            <div class="blog-actions">
                <div class="like-btn">
                    <i class="fas fa-thumbs-up"></i>
                    <span class="like-count"> <?= htmlspecialchars($row['like'] ?? 0) ?> </span>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</body>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../Style/Admin/setting.css">
    <link rel="stylesheet" href="../../Style/user/view_all.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <style>
    .blog-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        padding: 20px;
    }

    .blog-post {
        background: #fff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .blog-post:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    .category {
        background-color: #007bff;
        color: white;
        font-size: 14px;
        padding: 5px 10px;
        text-transform: uppercase;
        font-weight: bold;
        text-align: center;
    }

    .blog-title {
        font-size: 20px;
        font-weight: bold;
        margin: 15px;
    }

    .blog-date {
        font-size: 14px;
        color: #6c757d;
        margin: 0 15px 10px;
    }

    .blog-image img {
        width: 100%;
        height: auto;
    }

    .blog-content {
        padding: 15px;
        color: #333;
        height: 150px;
        overflow: hidden;
        position: relative;
    }

    .blog-content.expanded {
        height: auto;
    }

    .view-more {
        margin: 0 15px 15px;
        cursor: pointer;
        color: #007bff;
        text-decoration: underline;
        font-size: 14px;
    }

    .view-more:hover {
        color: #0056b3;
    }

    .publish-checkbox {
        display: flex;
        align-items: center;
        margin: 15px;
    }

    .publish-checkbox input {
        margin-right: 10px;
        transform: scale(1.2);
    }

    .publish-checkbox button {
        padding: 8px 16px;
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }

    .blog-actions {
        margin: 15px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }

    .like-btn {
        display: flex;
        align-items: center;
        cursor: pointer;
        color: #007bff;
    }

    .like-btn i {
        margin-right: 5px;
    }

    .like-btn:hover {
        color: #0056b3;
    }
    </style>
</head>

<body>
    <div class="blog-container">
        <?php while ($row = $posts->fetch_assoc()): ?>
        <div class="blog-post">
            <div class="category"> <?= htmlspecialchars($row['category'] ?? 'No Category') ?> </div>
            <h2 class="blog-title"> <?= htmlspecialchars($row['blog_title'] ?? 'Untitled Post') ?> </h2>
            <p class="blog-date"> Published on <?= $row['publish_date'] ?? 'Unknown Date' ?> by
                <?= htmlspecialchars($row['uname'] ?? 'Unknown Author') ?> </p>

            <?php if (!empty($row['con_img'])): ?>
            <div class="blog-image">
                <img src="data:image/jpeg;base64,<?= base64_encode($row['con_img']) ?>" alt="Post Image">
            </div>
            <?php elseif (!empty($row['con_video'])): ?>
            <video controls class="blog-video">
                <source src="data:video/mp4;base64,<?= base64_encode($row['con_video']) ?>" type="video/mp4">
            </video>
            <?php endif; ?>

            <div class="blog-content" id="content_<?= $row['id'] ?>">
                <?= nl2br(htmlspecialchars($row['contant'] ?? 'No content available.')) ?>
            </div>
            <span class="view-more" data-id="<?= $row['id'] ?>">View More</span>

            <form method="POST" action="home.php">
                <div class="publish-checkbox">
                    <input type="checkbox" id="publish_<?= $row['id'] ?>" name="publish" value="1"
                        <?= $row['set_home'] == 1 ? 'checked' : '' ?>>
                    <label for="publish_<?= $row['id'] ?>">Publish</label>
                    <input type="hidden" name="post_id" value="<?= $row['id'] ?>">
                    <button type="submit">Update</button>
                </div>
            </form>

            <div class="blog-actions">
                <div class="like-btn">
                    <i class="fas fa-thumbs-up"></i>
                    <span class="like-count"> <?= htmlspecialchars($row['like'] ?? 0) ?> </span>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

    <script>
    document.querySelectorAll('.view-more').forEach(button => {
        button.addEventListener('click', function() {
            const contentId = this.getAttribute('data-id');
            const content = document.getElementById(`content_${contentId}`);
            if (content.classList.contains('expanded')) {
                content.classList.remove('expanded');
                this.textContent = 'View More';
            } else {
                content.classList.add('expanded');
                this.textContent = 'View Less';
            }
        });
    });
    </script>
</body>

</html>