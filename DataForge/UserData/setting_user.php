<?php
@include 'config.php';
session_start();

// Redirect to login if the session is not active
if (!isset($_SESSION['eid'])) {
    header('Location: ../../../Frontify/Enterance/login.php');
    exit();
}

// Sanitize the email from session
$email = mysqli_real_escape_string($conn, $_SESSION['eid']);

// Query to fetch user details
$query = "SELECT uname, profile_image, type FROM log_detail WHERE eid = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Default values
$username = "Guest";
$profileImage = "../../../Frontify/Images/Person.jpg"; // Default image path

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    $username = $user['uname'] ?? $username;
    $profileImage = $user['profile_image'] ? "../../../Frontify/Images/" . $user['profile_image'] : $profileImage;
    $userType = $user['type'] ?? $userType; // Safely check for 'type' key
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uname = $_POST['uname'];
    $newProfileImage = null;

    // Handle image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['size'] > 0) {
        if ($_FILES['profile_image']['size'] > 1048576) { // Check if image size exceeds 1 MB
            echo "<script>alert('Image size cannot be greater than 1 MB!');</script>";
        } else {
            // Generate a unique filename for the image
            $imageExtension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
            $newImageName = uniqid() . "." . $imageExtension;
            $uploadPath = "../../../Frontify/Images/" . $newImageName;

            // Move the uploaded image to the target directory
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadPath)) {
                $newProfileImage = $newImageName;
            } else {
                echo "<script>alert('Failed to upload image!');</script>";
            }
        }
    }

    // Update database with new profile image or just username
    if ($newProfileImage) {
        $stmt = $conn->prepare("UPDATE log_detail SET uname=?, profile_image=? WHERE eid=?");
        $stmt->bind_param("sss", $uname, $newProfileImage, $email);
    } else {
        $stmt = $conn->prepare("UPDATE log_detail SET uname=? WHERE eid=?");
        $stmt->bind_param("ss", $uname, $email);
    }
    $stmt->execute();

    // Refresh session variables and page
    $_SESSION['user_name'] = $uname;
    if ($newProfileImage) {
        $_SESSION['profile_image'] = "../../../Frontify/Images/" . $newProfileImage;
    }

    // Set a session variable to indicate a successful update
    $_SESSION['update_message'] = "Your data has been successfully updated.";

    // Redirect to MainIndex.php
    header("Location: ../../../Frontify/Main_Templete/User/MainIndex.php");
    exit();
}

// Free resources
if ($result) {
    mysqli_free_result($result);
}
mysqli_stmt_close($stmt);

?>