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
} else {
    // Redirect back to login if user details are not found (safety net)
    session_destroy();
    header('Location: ../../../Frontify/Enterance/login.php');
    exit();
}

// Free resources
if ($result) {
    mysqli_free_result($result);
}
mysqli_stmt_close($stmt);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUsername = mysqli_real_escape_string($conn, $_POST['uname']);
    $updatedProfileImage = $profileImage; // Default to existing profile image

    // Handle profile image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['size'] > 0) {
        $targetDir = "../../../Frontify/Images/";
        $targetFile = $targetDir . basename($_FILES['profile_image']['name']);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Validate image type and size
        if ($_FILES['profile_image']['size'] <= 1048576 && 
            in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile)) {
                $updatedProfileImage = $targetFile;
            } else {
                echo "<script>alert('Failed to upload the profile image.');</script>";
            }
        } else {
            echo "<script>alert('Invalid image file or size exceeds 1 MB.');</script>";
        }
    }

    // Update database
    $updateQuery = "UPDATE log_detail SET uname = ?, profile_image = ? WHERE eid = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, "sss", $newUsername, $updatedProfileImage, $email);
    if (mysqli_stmt_execute($stmt)) {
        // Update session variables
        $_SESSION['user_name'] = $newUsername;
        $_SESSION['profile_image'] = $updatedProfileImage;

        header('Location: MainIndex.php');
        echo "<script>alert('Profile updated successfully!');</script>";
        // Refresh the page to reflect updated details
        echo "<script>location.reload();</script>";
        exit();
    } else {
        echo "<script>alert('Failed to update profile. Please try again.');</script>";
    }

    mysqli_stmt_close($stmt);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    .card {
        border-radius: 10px;
        overflow: hidden;
    }

    .border-end-lg {
        border-right: 1px solid #e9ecef;
    }

    @media (max-width: 992px) {
        .border-end-lg {
            border-right: none;
        }
    }

    @media (max-width: 576px) {
        h4 {
            font-size: 1.5rem;
        }
    }
    </style>
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
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6">
                <div class="card shadow">
                    <div class="row g-0 flex-column flex-lg-row">
                        <div class="col-12 col-lg-4 text-center py-4 border-end-lg">
                            <!-- Profile Image -->
                            <img src="<?= htmlspecialchars($profileImage) ?>" alt="Profile Image"
                                class="img-fluid rounded-circle mb-3" id="profilePreview">
                            <h5 class="mt-2"><?= htmlspecialchars($username) ?></h5>
                            <button class="btn btn-primary mt-2"
                                onclick="document.getElementById('profile_image').click()">Update Profile
                                Image</button>
                        </div>
                        <div class="col-12 col-lg-8 p-4">
                            <h4 class="text-center text-lg-start mb-4">Profile Settings</h4>
                            <form method="POST" enctype="multipart/form-data">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="uname" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="uname" name="uname"
                                            value="<?= htmlspecialchars($username) ?>">
                                    </div>
                                    <div class="col-12">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="<?= htmlspecialchars($email) ?>" readonly>
                                    </div>
                                    <div class="col-12">
                                        <label for="type" class="form-label">Type</label>
                                        <input type="text" class="form-control" id="type"
                                            value="<?= htmlspecialchars($userType) ?>" readonly>
                                    </div>
                                    <div class="col-12">
                                        <label for="profile_image" class="form-label">Profile Image</label>
                                        <input type="file" class="form-control" id="profile_image" name="profile_image"
                                            onchange="validateImageSize(event)">
                                    </div>
                                    <button type="submit" class="btn btn-primary mt-3 w-100">Save Changes</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js">
    </script>
    <script>
    // Validate image size before submission
    function validateImageSize(event) {
        const file = event.target.files[0];
        if (file && file.size > 1048576) { // 1 MB = 1048576 bytes
            alert("Image size cannot be greater than 1 MB!");
            event.target.value = ""; // Clear the file input
        } else {
            const reader = new FileReader();
            reader.onload = function() {
                document.getElementById("profilePreview").src = reader.result;
            };
            reader.readAsDataURL(file);
        }
    }
    /* Pass PHP variables to JavaScript
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('uname').value = <?= json_encode($username) ?>;
        document.getElementById('email').value = <?= json_encode($email) ?>;
        document.getElementById('type').value = <?= json_encode($userType) ?>;
        document.getElementById('profilePreview').src = <?= json_encode($profileImage) ?>;
    });*/
    </script>
</body>

</html>