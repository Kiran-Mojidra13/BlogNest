<?php
@include '../../../DataForge/AdminData/Aindex_set.php';
@include '../../../DataForge/AdminData/config.php';

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
    // Check if the form is submitted
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
    <link rel="stylesheet" href="../../Style/Admin/setting.css">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <style>
    .publish-checkbox {
        margin-top: 10px;
        display: flex;
        align-items: center;
    }

    .publish-checkbox label {
        margin-right: 10px;
        font-size: 16px;
    }

    .publish-checkbox input {
        margin-right: 10px;
        transform: scale(1.2);
    }

    .publish-checkbox button {
        padding: 8px 16px;
        background-color: #4CAF50;
        color: white;
        border: none;
        cursor: pointer;
        font-size: 14px;
    }


    .blog-container {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        /* Ensures 3 posts per row */
        gap: 20px;
        /* Space between posts */
        padding: 20px;
    }

    @media (max-width: 768px) {
        .blog-container {
            grid-template-columns: repeat(2, 1fr);
            /* 2 posts per row on medium screens */
        }
    }

    @media (max-width: 480px) {
        .blog-container {
            grid-template-columns: 1fr;
            /* 1 post per row on small screens */
        }
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
        height: 250px;
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
<header>
    <div class="logosec">
        <div class="logo"><img src="../../images/blognest_logo.png" alt="BlogNest Logo" class="blognest-logo">BlogNest
        </div><a href="index.php"><svg xmlns="http://www.w3.org/2000/svg" class="icn menuicn" id="menuicn"
                alt="menu-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                style="height: 30px; width: 30px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg></a>
    </div>
    <div class="message">
        <div class="circle"></div>
        <div class="header_profile"><a href="./setting.php" class="profile_image_container"><img
                    src="<?php echo htmlspecialchars($_SESSION['profile_image'] ?: '../../../Frontify/Images/Person.jpg'); ?>"
                    alt="Profile Picture" class="profile_image"></a><span class="username"><?php echo htmlspecialchars($_SESSION['user_name']);
    ?></span></div>
    </div>
</header>

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