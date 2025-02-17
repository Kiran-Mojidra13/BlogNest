<?php
@include 'config.php';
session_start();

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
if ($result) {
    mysqli_free_result($result);
}
mysqli_stmt_close($stmt);

?>