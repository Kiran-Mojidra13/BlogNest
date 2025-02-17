<?php
@include 'config.php';
session_start();
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