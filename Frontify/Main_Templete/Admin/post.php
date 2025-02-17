<?php
@include '../../../DataForge/AdminData/Aindex_set.php';
@include '../../../DataForge/AdminData/config.php';

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Handle search functionality
$searchQuery = '';
if (isset($_POST['search'])) {
    $searchQuery = mysqli_real_escape_string($conn, $_POST['search']);
    $query = "SELECT * FROM post_manage WHERE blog_title LIKE '%$searchQuery%' 
              OR uname LIKE '%$searchQuery%' 
              OR category LIKE '%$searchQuery%' 
              OR status LIKE '%$searchQuery%'";
} else {
    $query = "SELECT * FROM post_manage";
}

// Initialize a success message variable
$success_message = '';

// Handle delete operation
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $delete_query = "DELETE FROM post_manage WHERE id = $delete_id";
    if (mysqli_query($conn, $delete_query)) {
        $success_message = "Post deleted successfully.";
    } else {
        $success_message = "Error deleting post: " . mysqli_error($conn);
    }
}

// Handle block operation
if (isset($_GET['block_id'])) {
    $block_id = intval($_GET['block_id']);
    $block_query = "UPDATE post_manage SET status = 'blocked' WHERE id = $block_id";
    if (mysqli_query($conn, $block_query)) {
        $success_message = "Post blocked successfully.";
    } else {
        $success_message = "Error blocking post: " . mysqli_error($conn);
    }
}

// Handle unblock operation
if (isset($_GET['unblock_id'])) {
    $unblock_id = intval($_GET['unblock_id']);
    $unblock_query = "UPDATE post_manage SET status = 'active' WHERE id = $unblock_id";
    if (mysqli_query($conn, $unblock_query)) {
        $success_message = "Post unblocked successfully.";
    } else {
        $success_message = "Error unblocking post: " . mysqli_error($conn);
    }
}

// Fetch updated data from the database
$query = "SELECT * FROM post_manage";
$result = mysqli_query($conn, $query) or die("Error in query: " . mysqli_error($conn));
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../Style/Admin/setting.css">
    <style>
    table td a.unblock {
        background-color: #4caf50;
        /* Green color for Unblock */
    }

    .container {
        max-width: 1200px;
        margin: 20px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    table thead tr {
        background-color: #0078d7;
        color: #fff;
    }

    table th,
    table td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    table tbody tr:hover {
        background-color: #f1f1f1;
    }

    table td img,
    table td video {
        max-width: 200px;
        max-height: 150px;
        border-radius: 5px;
    }

    table td .content {
        max-width: 500px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    table td {
        vertical-align: middle;
    }

    table td a {
        text-decoration: none;
        padding: 8px 15px;
        border-radius: 5px;
        color: #fff;
        font-size: 14px;
        margin-right: 5px;
        transition: background-color 0.3s;
    }

    table td a.block {
        background-color: #ffa500;
    }

    table td a.delete {
        background-color: #ff4d4d;
    }

    table td a:hover {
        opacity: 0.9;
    }

    .no-records {
        text-align: center;
        font-size: 18px;
        color: #888;
        margin-top: 20px;
    }

    table td a {
        text-decoration: none;
        padding: 8px 15px;
        border-radius: 5px;
        color: #fff;
        font-size: 14px;
        margin-right: 5px;
        /* Add spacing between buttons */
        display: inline-block;
        /* Ensure the links are displayed inline */
        transition: background-color 0.3s;
    }

    table td a.block {
        background-color: #ffa500;
    }

    table td a.delete {
        background-color: #ff4d4d;
    }

    table td a:hover {
        opacity: 0.9;
    }


    /***----------------------------------------------- msg pop up ----------------------------------------------------*/
    .modal {
        display: block;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
    }

    .modal-content {
        position: relative;
        margin: 15% auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 10px;
        width: 50%;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    .modal-content p {
        font-size: 18px;
        color: green;
        margin: 0;
    }

    .close-button {
        position: absolute;
        top: 10px;
        right: 15px;
        color: #aaa;
        font-size: 24px;
        font-weight: bold;
        cursor: pointer;
    }

    .close-button:hover,
    .close-button:focus {
        color: #000;
    }

    /* Add your CSS styles for the search form */
    .search-form {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 20px;
    }

    .search-form input {
        padding: 10px;
        width: 300px;
        border: 1px solid #ccc;
        border-radius: 5px 0 0 5px;
        outline: none;
    }

    .search-form button {
        padding: 10px;
        border: none;
        background-color: #0078d7;
        color: #fff;
        font-weight: bold;
        border-radius: 0 5px 5px 0;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .search-form button:hover {
        background-color: #005bb5;
    }
    </style>
    <title>Document</title>
</head>
<header>
    <div class="logosec">
        <div class="logo">
            <img src="../../images/blognest_logo.png" alt="BlogNest Logo" class="blognest-logo">
            BlogNest
        </div>
        <a href="index.php">
            <svg xmlns="http://www.w3.org/2000/svg" class="icn menuicn" id="menuicn" alt="menu-icon" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="height: 30px; width: 30px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </a>

    </div>


    <div class="message">
        <div class="circle"></div>
        <div class="header_profile">
            <a href="./setting.php" class="profile_image_container">
                <img src="<?php echo htmlspecialchars($_SESSION['profile_image'] ?: '../../../Frontify/Images/Person.jpg'); ?>"
                    alt="Profile Picture" class="profile_image">
            </a>
            <span class="username"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
        </div>
    </div>
</header>

<body>
    <!-- Success Message Modal -->
    <?php if (!empty($success_message)): ?>
    <div id="successModal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <p><?php echo htmlspecialchars($success_message); ?></p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Search Form -->
    <div class="container">
        <form method="post" class="search-form">
            <input type="text" name="search" placeholder="Search by blog title, username, category, or status"
                value="<?php echo htmlspecialchars($searchQuery); ?>" />
            <button type="submit">Search</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Blog Title</th>
                    <th>Content</th>
                    <th>Image/Video</th>
                    <th>Publish Date</th>
                    <th>Category</th>
                    <th>Likes</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['uname']); ?></td>
                    <td><?php echo htmlspecialchars($row['blog_title']); ?></td>
                    <td class="content"><?php echo htmlspecialchars(substr($row['contant'], 0, 100)) . '...'; ?></td>
                    <td>
                        <?php if ($row['con_img']): ?>
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($row['con_img']); ?>"
                            alt="Blog Image">
                        <?php elseif ($row['con_vedio']): ?>
                        <video controls>
                            <source src="data:video/mp4;base64,<?php echo base64_encode($row['con_vedio']); ?>"
                                type="video/mp4">
                        </video>
                        <?php else: ?>
                        No Media
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['publish_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                    <td><?php echo $row['like']; ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td>
                        <?php if ($row['status'] === 'blocked'): ?>
                        <a href="post.php?unblock_id=<?php echo $row['id']; ?>" class="unblock"
                            onclick="return confirm('Are you sure you want to unblock this post?');">Unblock</a>
                        <?php else: ?>
                        <a href="post.php?block_id=<?php echo $row['id']; ?>" class="block"
                            onclick="return confirm('Are you sure you want to block this post?');">Block</a>
                        <?php endif; ?>
                        <a href="post.php?delete_id=<?php echo $row['id']; ?>" class="delete"
                            onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php else: ?>
                <tr>
                    <td colspan="10" class="no-records">No records found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Script -->
    <script>
    // Close the modal when the close button is clicked
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('successModal');
        const closeButton = document.querySelector('.close-button');
        if (closeButton) {
            closeButton.addEventListener('click', function() {
                modal.style.display = 'none';
            });
        }

        // Optional: Close the modal after 5 seconds
        setTimeout(function() {
            if (modal) modal.style.display = 'none';
        }, 5000);
    });
    </script>
</body>

</html>