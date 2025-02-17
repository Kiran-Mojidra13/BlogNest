<?php
session_start();
require '../DataForge/AdminData/config.php'; // Include your database connection

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
// Fetch posts for the home page
$posts = $conn->query("SELECT * FROM post_manage WHERE set_home=1 and status='active' ORDER BY id DESC");

// Check if the user is logged in
$is_logged_in = isset($_SESSION['user_id']);
$user_role = $_SESSION['role'] ?? null;

function generateMediaUrl($data, $type) {
    return 'data:' . $type . ';base64,' . base64_encode($data);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Frontify/Style/styl.css">
    <style>

    </style>
    <title>Home - BlogNest</title>
</head>
<header>
    <div class="logosec">
        <div class="logo">
            <img src="../Frontify/Images/BlogNest_LOGO.png" alt="BlogNest Logo" class="blognest-logo">
            BlogNest
        </div>
        <img src="menu-icon.png" class="icn menuicn" id="menuicn" alt="menu-icon">
    </div>

    <div class="message">
        <?php if ($is_logged_in): ?>
        <div class="header_profile">
            <a href="./setting.php" class="profile_image_container">
                <img src="<?php echo htmlspecialchars($_SESSION['profile_image'] ?: '../../../Frontify/Images/Person.jpg'); ?>"
                    alt="Profile Picture" class="profile_image">
            </a>
            <span class="username"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
        </div>
        <?php else: ?>
        <div class="auth_links">
            <a href="../Frontify/Main_Templete/Enterance/login.php" class="login_link">Login</a>
            <a href="../Frontify/Main_Templete/Enterance/Register.php" class="register_link">Register</a>
        </div>
        <?php endif; ?>
    </div>
</header>

<body>
    <!-- Header -->


    <!-- Main Content -->
    <main>
        <?php if ($posts->num_rows > 0): ?>
        <?php 
        $counter = 0; // Initialize a counter to track posts
        while ($post = $posts->fetch_assoc()): 
            // Open a new container for every pair of posts
            if ($counter % 3 == 0): ?>
        <div class="posts-container">
            <?php endif; ?>

            <div class="blog-container">
                <h1 class="blog-title"><?php echo htmlspecialchars($post['blog_title']); ?></h1>
                <p class="blog-description">
                    <?php echo htmlspecialchars($post['category']); ?>,
                    <?php echo date("F j, Y", strtotime($post['publish_date'])); ?>
                </p>
                <!-- Media display -->
                <?php if (!empty($post['con_img'])): ?>
                <img src="<?php echo generateMediaUrl($post['con_img'], 'image/jpeg'); ?>" alt="Blog Image"
                    class="blog-image">
                <?php elseif (!empty($post['con_vedio'])): ?>
                <video controls class="blog-video">
                    <source src="<?php echo generateMediaUrl($post['con_vedio'], 'video/mp4'); ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                <?php else: ?>
                <img src="default-image.png" alt="Default Blog Image" class="blog-image">
                <?php endif; ?>

                <p class="blog-content"><?php echo nl2br(htmlspecialchars($post['contant'])); ?></p>
                <h3>Published By: <?php echo htmlspecialchars($post['uname']); ?></h3>
            </div>

            <?php 
            $counter++; // Increment the counter
            
            // Close the container after every pair of posts
            if ($counter % 3 == 0): ?>
        </div>
        <?php endif; ?>
        <?php endwhile; ?>

        <!-- Close the last container if there are an odd number of posts -->
        <?php if ($counter % 3 != 0): ?>
        </div>
        <?php endif; ?>
        <?php else: ?>
        <p>No posts available for display.</p>
        <?php endif; ?>
    </main>





    <!-- Footer -->


    <script src="script.js"></script>
</body>


<footer>
    <p>&copy; 2024 BlogNest. All Rights Reserved.</p>
</footer>

</html>