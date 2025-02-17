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
    <!--<link rel="stylesheet" href="../../Style/user/view.css">-->
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
        height: 270px;
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

                <div class="post-actions">
                    <button class="update-btn"><?php echo htmlspecialchars($post['status']); ?></button>
                    <button class="delete-btn" onclick="deletePostAjax(<?php echo $post['id']; ?>)">Delete Post</button>

                </div>
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
?>