<?php
@include '../../../DataForge/AdminData/config.php';
session_start();

// Redirect to login if the session is not active
if (!isset($_SESSION['eid'])) {
    header('Location: ../../../Frontify/Entarence/login.php');
    exit();
}

// Sanitize the email from session
$email = mysqli_real_escape_string($conn, $_SESSION['eid']);

// Query to fetch user details
$query = "SELECT uname, profile_image FROM log_detail WHERE eid = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Default values for guest user
$username = "Guest";
$profileImage = "../../../Frontify/Images/Person.jpg";

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    $username = $user['uname'] ?? $username;
    $profileImage = $user['profile_image'] ?? $profileImage;
}

// Free resources
if ($result) {
    mysqli_free_result($result);
}
mysqli_stmt_close($stmt);

// Update session variables for global use
$_SESSION['user_name'] = $username;
$_SESSION['profile_image'] = $profileImage;
?>

<?php
@include '../../../DataForge/AdminData/config.php';

// Queries
$totalPostsQuery = "SELECT COUNT(*) as total_posts FROM post_manage where status= 'active'";
$totalPostsBlock = "SELECT COUNT(*) as total_blocks FROM post_manage where status= 'blocked'";
$totalCommentsQuery = "SELECT COUNT(*) as total_comments FROM comment";
$totalUsersQuery = "SELECT COUNT(*) as total_users FROM log_detail WHERE type = 'user'";
$totalAdminsQuery = "SELECT COUNT(*) as total_admins FROM log_detail WHERE type = 'admin'";

// Execute queries
$totalPostsResult = $conn->query($totalPostsQuery);
$totalPostsResultBlock = $conn->query($totalPostsBlock);
$totalCommentsResult = $conn->query($totalCommentsQuery);
$totalUsersResult = $conn->query($totalUsersQuery);
$totalAdminsResult = $conn->query($totalAdminsQuery);

// Fetch results
$totalPosts = $totalPostsResult->fetch_assoc()['total_posts'] ?? 0;
$totalBlocks = $totalPostsResultBlock->fetch_assoc()['total_blocks'] ?? 0;
$totalComments = $totalCommentsResult->fetch_assoc()['total_comments'] ?? 0;
$totalUsers = $totalUsersResult->fetch_assoc()['total_users'] ?? 0;
$totalAdmins = $totalAdminsResult->fetch_assoc()['total_admins'] ?? 0;

// Close connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, 
                   initial-scale=1.0">
    <title>BlogNest</title>
    <link rel="stylesheet" href="../../Style/Admin/IStyle.css">
    <link rel="stylesheet" href="../../Style/Admin/IndexResponsive.css">
    <!-- FontAwesome Icon Library -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
    .box-icon {
        font-size: 40px;
        /* Adjust icon size */
        color: #eaeaea;
        /* Set icon color */
        display: block;
        margin: 0 auto;
    }

    .option1 {
        border-left: 5px solid #152228c6;
        background-color: #71aeca;
        color: white;
        cursor: pointer;
    }



    /* Use flexbox to ensure icon and text appear side by side */
    .nav-option {
        display: flex;
        align-items: center;
        /* Vertically align the icon and text */
        gap: 10px;
        /* Add some space between the icon and the text */
        padding: 0 30px 0 20px;
        height: 60px;
        transition: all 0.1s ease-in-out;
    }

    /* Icon styling */
    .nav-img {
        font-size: 24px;
        /* Adjust the size of the icon */
        color: #042520;
        /* Set the color of the icon */
    }

    /* Text styling */
    .nav-option p {
        font-size: 18px;
        /* Set the size of the text */
        color: #042520;
        /* Set the text color */
        margin: 0;
        /* Remove default margin */
    }

    /* Hover effect for the icon */
    .nav-img:hover {
        color: #e7ecf1;
        /* Change icon color on hover */
    }

    /* Optional: Hover effect for the entire nav-option */
    .nav-option:hover {
        background-color: #75c8d1;
        /* Optional hover background effect */
        border-left: 5px solid #a2a2a2;
        /* Border on hover */
    }
    </style>
</head>

<body>

    <!-- for header part -->

    <header>
        <div class="logosec">
            <div class="logo">
                <img src="../../images/blognest_logo.png" alt="BlogNest Logo" class="blognest-logo">
                BlogNest
            </div>
            <img src="https://media.geeksforgeeks.org/wp-content/uploads/20221210182541/Untitled-design-(30).png"
                class="icn menuicn" id="menuicn" alt="menu-icon">
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

    <div class="main-container">
        <div class="navcontainer">
            <nav class="nav">
                <div class="nav-upper-options">
                    <div class="nav-option option1">
                        <a href="./index.php"
                            style="text-decoration: none; color: inherit;display:flex;align-items: center; gap: 10px;">
                            <i class="fas fa-tachometer-alt nav-img"></i>
                            <p>Dashboard</p> <!-- Changed h3 to p -->
                        </a>
                    </div>

                    <div class="nav-option option2">
                        <a href="./post.php"
                            style="text-decoration: none; color: inherit;display:flex;align-items: center; gap: 10px;">
                            <i class="fas fa-newspaper nav-img"></i>
                            <p>Posts</p> <!-- Changed h3 to p -->
                        </a>
                    </div>

                    <div class="nav-option option3">
                        <a href="./user.php"
                            style="text-decoration: none; color: inherit;display:flex;align-items: center; gap: 10px;">
                            <i class="fas fa-chart-line nav-img"></i>
                            <p>Users</p> <!-- Changed h3 to p -->
                        </a>
                    </div>

                    <div class="nav-option option4">
                        <a href="./admin.php"
                            style="text-decoration: none; color: inherit;display:flex;align-items: center; gap: 10px;">
                            <i class="fas fa-school nav-img"></i>
                            <p>Admins</p> <!-- Changed h3 to p -->
                        </a>
                    </div>

                    <div class="nav-option option2">
                        <a href="./home.php"
                            style="text-decoration: none; color: inherit;display:flex;align-items: center; gap: 10px;">
                            <i class="fas fa-user-cog nav-img"></i>
                            <p>Home-Post Setting</p> <!-- Changed h3 to p -->
                        </a>
                    </div>

                    <div class="nav-option option5">
                        <a href="./setting_admin.php"
                            style="text-decoration: none; color: inherit;display:flex;align-items: center; gap: 10px;">
                            <i class="fas fa-cogs nav-img"></i>
                            <p>Settings</p> <!-- Changed h3 to p -->
                        </a>
                    </div>

                    <div class="nav-option logout">
                        <a href="../../../DataForge/UserData/logout.php"
                            style="text-decoration: none; color: inherit;display:flex;align-items: center; gap: 10px;">
                            <i class="fas fa-sign-out-alt nav-img"></i>
                            <p>Logout</p> <!-- Changed h3 to p -->
                        </a>
                    </div>


                </div>
            </nav>
        </div>
        <div class="main">


            <div class="box-container">


                <div class="box box1">
                    <div class="text">
                        <h2 class="topic-heading"><?php echo htmlspecialchars($totalUsers); ?>
                        </h2>
                        <h2 class="topic">Users</h2>
                    </div>
                    <i class="fas fa-users box-icon"></i>
                </div>

                <div class="box box2">
                    <div class="text">
                        <h2 class="topic-heading"><?php echo htmlspecialchars($totalAdmins); ?></h2>
                        <h2 class="topic">Admin</h2>
                    </div>
                    <i class="fas fa-user-shield box-icon"></i>
                </div>

                <div class="box box3">
                    <div class="text">
                        <h2 class="topic-heading"><?php echo htmlspecialchars($totalComments); ?></h2>
                        <h2 class="topic">Comments</h2>
                    </div>
                    <i class="fas fa-comment box-icon"></i>
                </div>

                <div class="box box4">
                    <div class="text">
                        <h2 class="topic-heading"><?php echo htmlspecialchars($totalPosts); ?></h2>
                        <h2 class="topic">Published</h2>
                    </div>
                    <i class="fas fa-paperclip box-icon"></i>
                </div>


            </div>


            <div class="box-container">


                <div class="box box1">
                    <div class="text">
                        <h2 class="topic-heading"><?php echo htmlspecialchars($totalBlocks); ?>
                        </h2>
                        <h2 class="topic">Blocked Post</h2>
                    </div>
                    <i class="fas fa-ban box-icon"></i>
                </div>



            </div>



        </div>


    </div>


    </div>
    </div>

    <script src="../../Script/Admin/IScript.js"></script>
</body>

</html>