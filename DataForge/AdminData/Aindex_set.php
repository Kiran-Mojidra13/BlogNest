<?php
@include 'config.php';
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